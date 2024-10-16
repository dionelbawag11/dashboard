<?php

/**
 * Widget represents a separate block of content
 * that is configurable, independent, and can be moved across pages
 */
abstract class Am_Widget extends Am_Block_Base
{
    public function __construct($module = null)
    {
        parent::__construct($this->getTitle(), $this->getId(), $module, $this->path);
    }

    public function getDi()
    {
        return Am_Di::getInstance();
    }

    public function getTitle()
    {
        throw new Am_Exception_NotImplemented(__METHOD__);
    }

    public function getId()
    {
        if (!$this->id)
        {
            $id = preg_replace('#^Am_Widget_#', '', get_class($this));
            $this->id = fromCamelCase($id, '-');
        }
        return $this->id;
    }

    public function render(Am_View $view, $envelope = '%s')
    {
        $this->view = $view;
        if ($this->prepare($view) !== false)
            return parent::render($view, $envelope);
    }

    /**
     * Override this function to assign necessary vars to the view
     * @param Am_View $view
     * @return you can return bool false to cancel rendering
     */
    public function prepare(Am_View $view) {}
}

class Am_Widget_PaymentHistory extends Am_Widget
{
    protected $path = 'member-history-paymenttable.phtml';
    public $id = 'member-history-paymenttable';

    public function prepare(Am_View $view)
    {
        if (!$this->getDi()->auth->getUserId())
            return false;

        $psList = $this->getDi()->paysystemList;
        $payments = $this->getDi()->invoicePaymentTable->findByUserId($this->getDi()->user->pk());
        $hasRefund = false;
        foreach ($payments as $p) {
            $p->_isRefund = false;
            $hasRefund = $hasRefund || $p->isRefunded();
        }
        $view->hasRefund = $hasRefund;

        if ($this->getDi()->config->get('different_invoice_for_refunds')) {
            $refunds = $this->getDi()->invoiceRefundTable->findByUserId($this->getDi()->user->pk());
            foreach ($refunds as $r) {
                $r->_isRefund = true;
            }
            $payments = array_merge($payments, $refunds);
        }

        usort($payments, function ($a, $b) {
            if ($a->dattm > $b->dattm) {
                return -1;
            } elseif ($a->dattm < $b->dattm) {
                return 1;
            } else {
                return 0;
            }
        });

        foreach ($payments as $payment) {
            $payment->_paysysName = $psList->getTitle($payment->paysys_id);
            if ($view->di->config->get('send_pdf_invoice') && $view->di->config->get('pdf_invoice_link')) {
                $payment->_invoice_url = $view->url(['member/get-invoice/id/%s', $view->obfuscate($payment->pk())], [
                    't' => $payment->_isRefund ? 'refund' : 'payment'
                ], false);
            }
        }
        $view->showRefunds = $this->getDi()->config->get('different_invoice_for_refunds');
        $view->payments = $payments;
    }

    public function getTitle()
    {
        return ___('Payment History');
    }
}

class Am_Widget_DetailedSubscriptions extends Am_Widget
{
    protected $path = 'member-history-detailedsubscriptions.phtml';
    public $id = 'member-history-detailedsubscriptions';

    public function prepare(Am_View $view)
    {
        if (!$this->getDi()->auth->getUserId())
            return false;

        $this->user_id = $this->getDi()->user->pk();

        $psList = $this->getDi()->paysystemList;
        $view->activeInvoices = $this->getDi()->invoiceTable->selectObjects("SELECT * FROM ?_invoice i
            WHERE  status IN (?a) AND user_id=?
            ORDER BY tm_started DESC",
                [
                    Invoice::RECURRING_ACTIVE,
                    Invoice::PAID,
                    Invoice::RECURRING_CANCELLED,
                    Invoice::RECURRING_FAILED
                ], $this->user_id);

        foreach ($view->activeInvoices as $invoice) {
            $invoice->_paysysName = $psList->getTitle($invoice->paysys_id);
            // if there is only 1 item, offer upgrade path
            $invoice->_upgrades = [];
            $_ = $invoice->data()->get(Invoice::UPGRADE_CANCEL);
            if (empty($_) &&
                in_array($invoice->getStatus(), [Invoice::PAID, Invoice::RECURRING_ACTIVE, Invoice::RECURRING_CANCELLED])) {
                foreach ($invoice->getItems() as $item)
                {
                    $item->_upgrades = $this->getDi()->productUpgradeTable->findUpgrades($invoice, $item);
                }
            }

            try {
                /**
                 * @var Am_Paysystem_Abstract $ps
                 */
                $ps = $this->getDi()->plugins_payment->loadGet($invoice->paysys_id, false);
            } catch (Exception $e) {
            }

            $invoice->_cancelUrl = $invoice->_restoreUrl = $invoice->_updateCcUrl = null;
            if (!empty($ps)) {

                if ($this->getDi()->config->get('allow_cancel')) {
                    if ($invoice->getStatus() == Invoice::RECURRING_ACTIVE) {
                        $invoice->_cancelUrl = $ps->getUserCancelUrl($invoice);
                    }
                }

                if ($ps->isManualRebillPossible($invoice)) {
                    $invoice->_manualRebillUrl = $ps->getManualRebillUrl($invoice);
                }
                else
                {
                    if ($this->getDi()->config->get('allow_restore')) {
                        if (in_array($invoice->getStatus(),
                            [Invoice::RECURRING_CANCELLED, Invoice::RECURRING_FAILED])) {
                            $invoice->_restoreUrl = $ps->getUserRestoreUrl($invoice);
                        }
                    }

                    if ($invoice->getStatus() == Invoice::RECURRING_ACTIVE) {
                        $invoice->_updateCcUrl = $ps->getUpdateCcLink($invoice->getUser());
                    }

                }

            }

        }
    }

    public function getTitle()
    {
        return ___('Your Subscriptions');
    }
}

class Am_Widget_ActiveSubscriptions extends Am_Widget
{
    protected $path = 'member-main-subscriptions.phtml';
    public $id = 'member-main-subscriptions';

    public function getTitle()
    {
        return ___('Active Subscriptions');
    }

    public function prepare(Am_View $view)
    {
        if (!$this->getDi()->auth->getUserId()) return false;

        $this->user_id = $this->getDi()->user->pk();

        $products_cancel = [];
        $recurringInvoices = $this->getDi()->invoiceTable->selectObjects("SELECT * FROM ?_invoice i
            WHERE  status=? AND user_id=?", Invoice::RECURRING_ACTIVE, $this->user_id);

        if ($this->getDi()->config->get('allow_cancel')) {
            foreach ($recurringInvoices as $invoice) {
                $cancelUrl = null;
                try {
                    $ps = $this->getDi()->plugins_payment->loadGet($invoice->paysys_id, false);
                    if ($ps)
                        $cancelUrl = $ps->getUserCancelUrl($invoice);
                } catch (Exception $e){}
                if ($cancelUrl) {
                    /* @var $invoice Invoice */
                    foreach ($invoice->getItems() as $item) {
                        if ($item->item_type == 'product' && $item->rebill_times > 0) {
                            $products_cancel[$item->item_id] = $cancelUrl;
                        }
                    }
                }
            }
        }

        $products_upgrade = [];
        $activeInvoices = $this->getDi()->invoiceTable->selectObjects("SELECT DISTINCT(i.invoice_id),i.* FROM ?_invoice i, ?_access a
            WHERE  i.status IN (?a) AND i.user_id=? AND a.expire_date > NOW() AND a.invoice_id = i.invoice_id
            ORDER BY i.tm_started DESC",
            [Invoice::RECURRING_ACTIVE, Invoice::PAID, Invoice::RECURRING_CANCELLED], $this->user_id);

        $_ = [];
        foreach ($activeInvoices as $invoice) {
            $is_upgrade_cancel = $invoice->data()->get(Invoice::UPGRADE_CANCEL);
            if (empty($is_upgrade_cancel)) {
                foreach ($invoice->getItems() as $item) {
                    if ($product = $item->tryLoadProduct()) {
                        if (isset($_[$product->pk()])) continue;
                        $_[$product->pk()] = true;
                        if ($upgrades = $this->getDi()->productUpgradeTable->findUpgrades($invoice, $item)) {
                            $item->_upgrades = $upgrades;
                            $products_upgrade[$product->pk()] = $item;
                        }
                    }
                }
            }
        }

        $view->assign('member_products', $this->getDi()->user->getActiveProducts());
        $view->assign('member_future_products', $this->getDi()->user->getFutureProducts());

        $view->assign('products_expire', $this->getDi()->user->getActiveProductsExpiration());
        $view->assign('products_rebill', $this->getDi()->user->getActiveProductsRebill());
        $view->assign('products_begin', $this->getDi()->user->getFutureProductsBeginning());
        $view->assign('products_cancel', $products_cancel);
        $view->assign('products_upgrade', $products_upgrade);
    }
}

class Am_Widget_ActiveResources extends Am_Widget
{
    protected $path = 'member-main-resources.phtml';
    public $id = 'member-main-resources';

    public function getTitle()
    {
        return ___('Active Resources');
    }

    public function prepare(Am_View $view)
    {
        if (!$this->getDi()->auth->getUserId()) return false;

        $left_member_links = $this->getDi()->hook
            ->call(Am_Event::GET_LEFT_MEMBER_LINKS, ['user' => $this->getDi()->user])
            ->getReturn();
        $view->assign('left_member_links', $left_member_links);

        $resources = $this->getDi()->resourceAccessTable
            ->getAllowedResources($this->getDi()->user, ResourceAccess::USER_VISIBLE_TYPES);

        foreach ($resources as $k => $r) {
            if (!$r->renderLink()) unset($resources[$k]);
        }

        $view->assign('resources', $resources);

        if (!$resources && !$left_member_links)
            return false; // disable rendering block!
    }
}

class Am_Widget_MemberLinks extends Am_Widget
{
    protected $path = 'member-main-links.phtml';
    public $id = 'member-main-links';

    public function getTitle()
    {
        return ___('Useful Links');
    }

    public function prepare(Am_View $view)
    {
        if (!$this->getDi()->auth->getUserId())
            return false;
        $member_links = [
            $this->getDi()->url('logout', false) => ___('Logout'),
            $this->getDi()->url('profile', false) => ___('Change Password/Edit Profile'),
            $this->getDi()->url('member/payment-history', false) => ___('Payments History')
        ];
        if($this->getDi()->config->get('enable-personal-data-download'))
        {
            $member_links[$this->getDi()->url('personal-data/download', false)] = ___('Download Personal Data');
        }

        if($this->getDi()->config->get('enable-account-delete') && !$this->getDi()->config->get('hide-delete-link'))
        {
            $member_links[$this->getDi()->url('personal-data/delete', false)] = ___('Delete Personal Data');
        }

        $event = new Am_Event(Am_Event::GET_MEMBER_LINKS, ['user' => $this->getDi()->user]);
        $event->setReturn($member_links);

        $view->assign('member_links', $this->getDi()->hook->call($event)->getReturn());
    }
}

class Am_Widget_Unsubscribe extends Am_Widget
{
    protected $path = 'member-main-unsubscribe.phtml';
    public $id = 'member-main-unsubscribe';

    public function getTitle()
    {
        return ___('E-Mail Preferences');
    }

    public function prepare(Am_View $view)
    {
        if (empty($view->ignore_unsubscribe_config) && Am_Di::getInstance()->config->get('disable_unsubscribe_block', 0))
            return false;
        if (empty($view->user) && !$this->getDi()->auth->getUserId())
            return false;
        if (empty($view->user))
            $view->user =  $this->getDi()->user;

        $req = Am_Di::getInstance()->request;
        if ($req->get('e')) $view->e = preg_replace('/[^A-Za-z0-9-+@_.%]/', '', $req->get('e'));
        if ($req->get('s')) $view->s = preg_replace('/[^A-Za-z0-9-+@_.%]/', '', $req->get('s'));
    }
}
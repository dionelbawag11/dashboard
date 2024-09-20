<?php

/**
 * @setup_url admin-upsell
 * @am_plugin_api 6.0
 */
class Am_Plugin_Upsell extends Am_Plugin
{
    const PLUGIN_STATUS = self::STATUS_PRODUCTION;
    const PLUGIN_COMM = self::COMM_COMMERCIAL;
    const PLUGIN_REVISION = '6.3.20';

    protected $_configPrefix = 'misc.';
    const UPSELL_STATUS_DISPLAYED = 'displayed';
    const UPSELL_STATUS_ACCEPTED = 'accepted';
    const UPSELL_STATUS_REJECTED = 'rejected';

    static function getDbXml()
    {
        return <<<CUT
<schema version="4.0.0">
    <table name="upsell">
        <field name="upsell_id" type="int" unsigned="1" notnull="1" extra="auto_increment"/>
        <field name="title" type="varchar" len="255"/>
        <field name="conditions" type="text"/>
        <field name="bp_id" type="int"/>
        <field name="html" type="mediumtext"/>
        <field name="no_layout" type="tinyint"/>
        <field name="type" type="varchar" len="255"/>
        <field name="keep_qty" type="tinyint"/>
        <index name="PRIMARY" unique="1">
            <field name="upsell_id"/>
        </index>
    </table>
    <table name="upsell_report">
        <field name="upsell_report_id" type="int" unsigned="1" notnull="1" extra="auto_increment"/>
        <field name="dattm" type="datetime"/>
        <field name="upsell_id" type="int" unsigned="1" notnull="1" />
        <field name="invoice_id" type="int" unsigned="1" notnull="1"/>
        <field name="status" type="varchar" len="32"/>
        <field name="paid" type="int"/>
        
        <index name="PRIMARY" unique="1">
            <field name="upsell_report_id"/>
        </index>
        <index name="log_entry" unique="1">
            <field name="upsell_id"/>
            <field name="invoice_id"/>
        </index>
        <index name="invoice_id" unique="1">
            <field name="invoice_id"/>
        </index>
        <index name="dattm">
            <field name="dattm"/>
        </index>
    </table>
    
</schema>
CUT;
    }

    function init()
    {
        parent::init();
        Am_View_Helper_Icon::$src['upsell'] =
            'data:image/png;base64,' .
            'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAEGSURBVDjLpZM/LwRRFMXPspmEaGc1shHRaiXsJ5GIRixbCr6SikxIlqgJM5UohIiGdofovHf/PZVmYwZvTntPfjnn3txWCAFNNFE33L/ZKXYv+1dRgL3r7bu0PbucJp3e4GLjtsrXGq9wkA8SU7tPk87i/MwCzAyP5QNeytcnJl46XMuoNoGKDoVlTkQhJpAgmJqcBjnqkqPTXxN8qz9cD6vdHtQMxXOBt49y5XjzLB/3tau6kWewKiwoRu8jZFvn+U++GgCBlWFBQY4qr1ANcAQxgQaFjwH4TwYrQ5skYBOYKbzjiASOwCrNd2BBwZ4jAcowGJgkAuAZ2dEJhAUqij//wn/1BesSumImTttSAAAAAElFTkSuQmCC';
    }

    function onAdminMenu(Am_Event $e)
    {
        if ($p = $e->getMenu()->findOneById('products')) {
            $p->addPage([
                'id' => 'upsell',
                'module' => 'default',
                'controller' => 'admin-upsell',
                'action' => 'index',
                'label' => ___('Upsell'),
                'resource' => 'grid_product',
            ]);
        }
    }

    function onInvoiceBeforePayment(Am_Event $e)
    {
        $invoice = $e->getInvoice();
        if ($invoice->data()->get('UPSELL')) {
            return;
        }

        foreach ($invoice->getItems() as $item) {
            if ($this->getDi()->upsellTable->findUpsell($item, $invoice)) {
                Am_Mvc_Response::redirectLocation($this->getDi()->url('upsell',
                    ['id' => $invoice->getSecureId('UPSELL')], false));
            }
        }
    }

    function onInvoiceStarted(Am_Event_InvoiceStarted $event)
    {
        $invoice = $event->getInvoice();
        $this->getDi()->db->query("UPDATE ?_upsell_report set paid=1 where invoice_id=?", $invoice->pk());
    }

    function onLoadReports(Am_Event $event)
    {
        class_exists('Am_Report_Upsell', true);
    }

    function logUpsell($upsell_id, $invoice_id, $status, $paid = 0)
    {
        $this->getDi()->db->query(
            "INSERT INTO ?_upsell_report (dattm, upsell_id, invoice_id, `status`, paid) values (?,?,?,?,?) on DUPLICATE KEY UPDATE  `status`=?, paid=?",
            $this->getDi()->sqlDateTime, $upsell_id, $invoice_id, $status, $paid, $status, $paid
        );
    }
}

class AdminUpsellController extends Am_Mvc_Controller_Grid
{
    public function checkAdminPermissions(Admin $admin)
    {
        return $admin->hasPermission('grid_product');
    }

    function createGrid()
    {
        $ds = new Am_Query($this->getDi()->upsellTable);

        $grid = new Am_Grid_Editable('_us', ___("Upsell"), $ds, $this->getRequest(), $this->view);
        $grid->setPermissionId('grid_product');
        $grid->addField('title', ___('Title'));
        $grid->setForm([$this, 'createForm']);
        $grid->addCallback(Am_Grid_Editable::CB_VALUES_TO_FORM, function (&$v, $r)
        {
            if (!$r->isLoaded() && empty($v['html'])) {
                $v['html'] = <<<CUT
<p>Do you want to accept <strong>%title%</strong>?</p>
<p><a href="%url_accept%">Accept</a> or <a href="%url_reject%">Reject</a></p>

CUT;
            }
            $v['type'] = $v['type'] ?: 'replace';
        });

        $grid->setFormValueCallback('conditions', ['RECORD', 'unserializeList'], ['RECORD', 'serializeList']);

        return $grid;
    }

    function createForm()
    {
        $form = new Am_Form_Admin;

        $form->addText('title', ['class' => 'am-el-wide'])
            ->setLabel(___("Title"))
            ->addRule('required');

        $form->addMagicSelect('conditions', ['class' => 'am-combobox'])
            ->setLabel('Conditions')
            ->loadOptions($this->getDi()->billingPlanTable->getOptions());

        $form->addSelect('bp_id', ['class' => 'am-combobox'])
            ->setLabel(___('Upsell Product'))
            ->loadOptions($this->getDi()->billingPlanTable->getOptions());

        $form->addAdvRadio('type')
            ->setLabel(___('Upsell Type'))
            ->loadOptions([
                'replace' => 'Replace Product with Upsell',
                'add' => 'Add Upsell as Additional Item to Purchase',
            ]);
        $form->addAdvCheckbox('keep_qty')
            ->setLabel("Keep Qty for Upsell\nin case of upsell product has variable qty then set qty from conditional product to upsell");

        $placeholder_items =& $options['placeholder_items'];
        $placeholder_items[] = ['URL Accept Offer', '%url_accept%'];
        $placeholder_items[] = ['URL Reject Offer', '%url_reject%'];
        $placeholder_items[] = ['Offer Title', '%title%'];
        foreach ($this->getUserTagOptions() as $k => $v) {
            $placeholder_items[] = [$v, $k];
        }

        $form->addHtmlEditor('html')
            ->setLabel('Offer Text')
            ->setMceOptions($options);

        $form->addAdvCheckbox('no_layout')
            ->setLabel(___("Avoid using standard layout\nyou have to design entire page in the 'Offer Text' field"));

        return $form;
    }

    function getUserTagOptions()
    {
        $tagOptions = [
            '%user.name_f%' => 'User First Name',
            '%user.name_l%' => 'User Last Name',
            '%user.login%' => 'Username',
            '%user.email%' => 'E-Mail',
            '%user.user_id%' => 'User Internal ID#',
            '%user.street%' => 'User Street',
            '%user.street2%' => 'User Street (Second Line)',
            '%user.city%' => 'User City',
            '%user.state%' => 'User State',
            '%user.zip%' => 'User ZIP',
            '%user.country%' => 'User Country',
            '%user.status%' => 'User Status (0-pending, 1-active, 2-expired)',
        ];

        foreach ($this->getDi()->userTable->customFields()->getAll() as $field) {
            if (@$field->sql && @$field->from_config) {
                $tagOptions['%user.' . $field->name . '%'] = 'User ' . $field->title;
            }
        }

        return $tagOptions;
    }
}

class UpsellController extends Am_Mvc_Controller
{
    /**
     * @return  Am_Plugin_Upsell $plugin;
     */
    function getPlugin()
    {
        return $this->getDi()->plugins_misc->loadGet('upsell');
    }

    public function indexAction()
    {
        if (!$invoice = $this->getDi()->invoiceTable->findBySecureId($this->getParam('id'), 'UPSELL')) {
            throw new Am_Exception_InputError;
        }
        if ($invoice->data()->get('UPSELL')) {
            throw new Am_Exception_InputError;
        }

        foreach ($invoice->getItems() as $item) {
            if ($u = $this->getDi()->upsellTable->findUpsell($item, $invoice)) {
                break;
            }
        }

        $v = new Am_SimpleTemplate();
        $v->assign([
            'user' => $invoice->getUser(),
            'title' => $u->title,
            'url_accept' => $this->getDi()->url('upsell/accept',
                ['id' => $invoice->getSecureId('UPSELL-ACCEPT')], false),
            'url_reject' => $this->getDi()->url('upsell/reject',
                ['id' => $invoice->getSecureId('UPSELL-REJECT')], false),
        ]);

        $this->getPlugin()->logUpsell($u->pk(), $invoice->pk(), Am_Plugin_Upsell::UPSELL_STATUS_DISPLAYED);
        if ($u->no_layout) {
            echo $v->render($u->html);
            throw new Am_Exception_Redirect;
        } else {
            $view = $this->getDi()->view;
            $view->user = $invoice->getUser();
            $view->title = $u->title;
            $view->content = $v->render($u->html);
            $view->layoutNoMenu = $view->layoutNoLang = $view->layoutNoTitle = true;
            $view->display('layout.phtml');
        }
    }

    function acceptAction()
    {
        if (!$invoice = $this->getDi()->invoiceTable->findBySecureId($this->getParam('id'), 'UPSELL-ACCEPT')) {
            throw new Am_Exception_InputError;
        }
        if ($invoice->data()->get('UPSELL')) {
            throw new Am_Exception_InputError;
        }

        $invoice->data()->set('UPSELL', 1);

        foreach ($invoice->getItems() as $item) {
            if ($u = $this->getDi()->upsellTable->findUpsell($item, $invoice)) {
                break;
            }
        }

        $bp = $this->getDi()->billingPlanTable->load($u->bp_id);
        $product = $bp->getProduct();

        $qty = ($product->getBillingPlan()->variable_qty && $u->keep_qty) ? $item->qty : 1;

        switch ($u->type) {
            case 'add':
                $invoice->add($product, $qty);
                break;
            case 'replace':
            default:
                $invoice->deleteItem($item);
                $invoice->add($product, $qty);
                break;
        }

        $this->getPlugin()->logUpsell($u->pk(), $invoice->pk(), Am_Plugin_Upsell::UPSELL_STATUS_ACCEPTED);

        $invoice->calculate();

        if ($invoice->paysys_id == 'free' && !$invoice->isZero()) { // was free, now paid, take first public payment system
            foreach ($this->getDi()->paysystemList->getAllPublic() as $pd) {
                $p = $this->getDi()->plugins_payment->loadGet($pd->getId());
                if (!$p->isNotAcceptableForInvoice($invoice)) {
                    $invoice->paysys_id = $p->getId();
                    break;
                }
            }
        }

        $invoice->save();

        $payProcess = new Am_Paysystem_PayProcessMediator($this, $invoice);
        $payProcess->process();

        throw new Am_Exception_InternalError(
            sprintf('Error occurred while trying proccess invoice [%s]',
                filterId($invoice->public_id)));
    }

    function rejectAction()
    {
        if (!$invoice = $this->getDi()->invoiceTable->findBySecureId($this->getParam('id'), 'UPSELL-REJECT')) {
            throw new Am_Exception_InputError;
        }
        if ($invoice->data()->get('UPSELL')) {
            throw new Am_Exception_InputError;
        }

        $invoice->data()->set('UPSELL', 1);
        $invoice->save();

        foreach ($invoice->getItems() as $item) {
            if ($u = $this->getDi()->upsellTable->findUpsell($item, $invoice)) {
                break;
            }
        }

        $this->getPlugin()->logUpsell($u->pk(), $invoice->pk(), Am_Plugin_Upsell::UPSELL_STATUS_REJECTED);

        $payProcess = new Am_Paysystem_PayProcessMediator($this, $invoice);
        $payProcess->process();

        throw new Am_Exception_InternalError(
            sprintf('Error occurred while trying proccess invoice [%s]',
                filterId($invoice->public_id)));
    }
}

class Upsell extends Am_Record
{
}

class UpsellTable extends Am_Table
{
    protected $_key = 'upsell_id';
    protected $_table = '?_upsell';
    protected $_recordClass = 'Upsell';

    function findUpsell($invoiceItem, $invoice)
    {
        $bp_ids = array_map(function($_) { return $_->billing_plan_id; }, $invoice->getItems());

        foreach ($this->findBy() as $u) {
            if ($invoiceItem->item_type == 'product'
                && in_array($invoiceItem->billing_plan_id, explode(',', $u->conditions))
                && !in_array($u->bp_id, $bp_ids)
            ) {

                return $u;
            }
        }
    }
}

class Am_Report_Upsell extends Am_Report_Abstract
{

    const PERIOD_EXACT = 'exact';
    protected $only_paid;

    public function __construct()
    {
        $this->title = ___('Upsell Reports');
        $this->description = "";
    }

    public function _initConfigForm(Am_Form $form)
    {
        parent::_initConfigForm($form);
        $period = $form->addSelect('period')->setLabel(___('Period'))
            ->loadOptions(
                array_merge($this->getDi()->interval->getOptions(),
                    [self::PERIOD_EXACT => ___('Exact')]));

        $intervals = [];
        foreach ($this->getDi()->interval->getIntervals() as $k => $v) {
            [$b, $e] = $v;
            $b = amDate($b);
            $e = amDate($e);
            $intervals[$k] = ($b == $e) ? $b : sprintf('%s&mdash;%s', $b, $e);
        }
        $period->setAttribute('data-intervals', json_encode($intervals));

        $period_exact = self::PERIOD_EXACT;
        $script = <<<CUT
jQuery(function(){
jQuery('select[name=period]').change(function(){
    var str = jQuery(this).data('intervals')[jQuery(this).val()];
    jQuery(this).parent().find('.am-period-hint').remove();
    jQuery(this).after(jQuery('<span class="am-period-hint" style="margin-left:1em; color:#c2c2c2">').html(str));
    jQuery(this).closest('.am-form').find('input[name=start], input[name=stop]').
        closest('div.am-row').
        toggle(jQuery(this).val() == '{$period_exact}');
}).change();
})
CUT;
        $form->addScript()->setScript($script);

        $start = $form->addDate('start')->setLabel(___('Start'));
        $start->addRule('required');
        $stop = $form->addDate('stop')->setLabel(___('End'));
        $stop->addRule('required');
        $form->addRule('callback', 'Start Date cannot be later than the End Date', [$this, 'checkStopDate']);
        $form->addAdvCheckbox('only_paid')->setLabel(___('Include Only Paid Invoices'));
    }

    protected function getFormDefaults()
    {
        return [
            'start' => sqlDate('-1 month'),
            'stop' => sqlDate('now'),
        ];
    }

    public function checkStopDate($val)
    {
        $res = $val['stop'] >= $val['start'];
        if (!$res) {
            $elements = $this->getForm()->getElementsByName('start');
            $elements[0]->setError('Start Date cannot be later than the End Date');
        }
        return $res;
    }

    protected function getStartStop(array $values)
    {
        switch ($values['period']) {
            case self::PERIOD_EXACT :
                return [$values['start'], $values['stop']];
            default :
                return $this->getDi()->interval->getStartStop($values['period']);
        }
    }

    protected function processConfigForm(array $values)
    {
        [$start, $stop] = $this->getStartStop($values);
        $this->setInterval($start, $stop);

        $op = $this->getDi()->db->selectCol("SELECT title, upsell_id AS ARRAY_KEY
                FROM ?_upsell");

        $quant = new Am_Report_Quant_Map($op);
        $this->setQuantity($quant);
        $this->only_paid = $values['only_paid'];
    }

    public function setInterval($start, $stop)
    {
        $this->start = date('Y-m-d 00:00:00', strtotime($start));
        $this->stop = date('Y-m-d 23:59:59', strtotime($stop));
        return $this;
    }


    function getLines()
    {
        $ret = [];
        $ret[] = new Am_Report_Line('title', ___('Title'));
        $ret[] = new Am_Report_Line('displayed', ___('Displayed'));
        $ret[] = new Am_Report_Line('accepted', ___('Accepted'));
        $ret[] = new Am_Report_Line('rejected', ___('Rejected'));
        $ret[] = new Am_Report_Line('percent', ___('Accept Rate'));

        return $ret;
    }

    public function getOutput(Am_Report_Result $result)
    {
        return [new Am_Report_TableUpsell($result)];
    }

    protected function runQuery()
    {
        $table = new Am_Table($this->getDi()->db, '?_upsell_report', 'upsell_report_id');
        $q = new Am_Query($table, 'c');
        $q->leftJoin('?_upsell', 'u', 'c.upsell_id = u.upsell_id');
        $q->addField('u.title', 'title');
        $q->addField("SUM(1) as displayed");
        $q->addField("SUM(IF(c.`status` = 'accepted', 1, 0)) as accepted");
        $q->addField("SUM(IF(c.`status` = 'rejected', 1, 0)) as rejected");
        $q->addField('c.upsell_id', self::POINT_FLD);
        if ($this->only_paid) {
            $q->addWhere('c.paid=1');
        }
        $q->addWhere("dattm BETWEEN ? AND ?", $this->start, $this->stop);
        $q->groupBy('upsell_id');

        $this->stmt = $q->query();
    }

}

class Am_Report_TableUpsell extends Am_Report_Output
{
    protected $title = "Table";

    public function render()
    {
        $out = "<div class='am-grid-container'>\n";
        $out .= "<table class='am-grid'>\n";
        $out .= "<tr>\n";
        foreach ($this->result->getLines() as $line) {
            $out .= "<th align='right'>" . Am_Html::escape($line->getLabel()) . "</th>\n";
        }
        $out .= "</tr>\n";
        $totals = [];
        $n = 0;
        foreach ($this->result->getPoints() as $point) {
            if (!$point->hasValues()) {
                continue;
            }
            $out .= sprintf("<tr class='am-grid-row %s'>", $n++ % 2 ? 'odd' : 'even');
            $i = 0;
            foreach ($this->result->getLines() as $line) {
                if ($line->getKey() == 'percent') {
                    $out .= sprintf("<td>%s</td>", Am_Html::escape(sprintf("%.2f",
                            $point->getValue('accepted') / $point->getValue('displayed') * 100)) . "%");
                } else {
                    $out .= sprintf("<td>%s</td>",
                        Am_Html::escape($line->formatValue($point->getValue($line->getKey()))));
                }
                @$lines[$i] = $line;
            }
            $out .= "</tr>\n";
        }
        $out .= "</table>\n";
        $out .= "</div>";
        return $out;
    }
}

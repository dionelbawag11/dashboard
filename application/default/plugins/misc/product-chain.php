<?php

/**
 * @am_plugin_api 6.0
*/
class Am_Plugin_ProductChain extends Am_Plugin
{
    const PLUGIN_STATUS = self::STATUS_BETA;
    const PLUGIN_COMM = self::COMM_COMMERCIAL;
    const PLUGIN_REVISION = '6.3.20';

    protected $_configPrefix = 'misc.';

    function onGridProductInitForm(Am_Event_Grid $event)
    {
        $form = $event->getGrid()->getForm();

        $fs = $form->addAdvFieldset()
            ->setLabel(___('Product Chaining Rules'));

        $fs->addMagicSelect('_product_chain_on_active_add', array('multiple' => 1, 'class' => 'am-combobox'))
            ->setLabel(___('Add the following product(s) on Signup'))
            ->loadOptions($this->getDi()->productTable->getOptions());

        $fs->addMagicSelect('_product_chain_on_active', array('multiple' => 1, 'class' => 'am-combobox'))
            ->setLabel(___('Expire the following product(s) on Signup'))
            ->loadOptions($this->getDi()->productTable->getOptions());

        $fs->addMagicSelect('_product_chain_on_active_cancel', array('multiple' => 1, 'class' => 'am-combobox'))
            ->setLabel(___('Cancel the following product(s) on Signup'))
            ->loadOptions($this->getDi()->productTable->getOptions());

        $fs->addMagicSelect('_product_chain_on_expire', array('multiple' => 1, 'class' => 'am-combobox'))
            ->setLabel(___('Add the following product(s) on Expiration'))
            ->loadOptions($this->getDi()->productTable->getOptions());

        $fs->addMagicSelect('_product_chain_on_expire_expire', array('multiple' => 1, 'class' => 'am-combobox'))
            ->setLabel(___('Expire the following product(s) on Expiration'))
            ->loadOptions($this->getDi()->productTable->getOptions());
    }

    function onGridProductValuesFromForm(Am_Event_Grid $event)
    {
        $args = $event->getArgs();
        $product = $args[1];
        $product->data()->set('product_chain_on_expire', @$args[0]['_product_chain_on_expire']);
        $product->data()->set('product_chain_on_expire_expire', @$args[0]['_product_chain_on_expire_expire']);
        $product->data()->set('product_chain_on_active', @$args[0]['_product_chain_on_active']);
        $product->data()->set('product_chain_on_active_add', @$args[0]['_product_chain_on_active_add']);
        $product->data()->set('product_chain_on_active_cancel', @$args[0]['_product_chain_on_active_cancel']);
    }

    function onGridProductValuesToForm(Am_Event_Grid $event)
    {
        $args = $event->getArgs();
        $product = $args[1];
        $args[0]['_product_chain_on_expire'] = $product->data()->get('product_chain_on_expire');
        $args[0]['_product_chain_on_expire_expire'] = $product->data()->get('product_chain_on_expire_expire');
        $args[0]['_product_chain_on_active'] = $product->data()->get('product_chain_on_active');
        $args[0]['_product_chain_on_active_add'] = $product->data()->get('product_chain_on_active_add');
        $args[0]['_product_chain_on_active_cancel'] = $product->data()->get('product_chain_on_active_cancel');
    }

    function onSubscriptionAdded(Am_Event $e)
    {
        /* @var $user User */
        $user = $e->getUser();
        $origProduct = $e->getProduct();
        $active = $user->getActiveProductIds();
        if ($on_active_cancel = $origProduct->data()->get('product_chain_on_active_cancel')) {
            foreach ($this->getDi()->invoiceTable->selectObjects("
                SELECT i.* 
                FROM ?_invoice i, ?_invoice_item ii
                WHERE i.user_id = ?d
                AND ii.item_id IN (?a)
                AND ii.item_type = 'product'
                AND i.status = ?d
                GROUP BY i.invoice_id",
                $user->pk(), $on_active_cancel, Invoice::RECURRING_ACTIVE) as $invoice) {
                if(!($plugin = $this->getDi()->plugins_payment->loadGet($invoice->paysys_id, true)))
                    continue;
                if(method_exists($plugin, 'cancelAction')) {
                    $result = new Am_Paysystem_Result();
                    $result->setSuccess();
                    try {
                        $plugin->cancelAction($invoice, 'cancel-product-chain', $result);
                    } catch (Exception $e) {
                    }
                }
                else
                {
                    $invoice->setCancelled(true);
                }
            }
        }

        if ($on_active = $origProduct->data()->get('product_chain_on_active')) {
            foreach ($this->getDi()->accessTable->findBy(array(
                'user_id' => $user->pk(),
                'product_id' => $on_active,
                array('expire_date', '>=', sqlDate('now'))
            )) as $access) {
                $access->comment = sprintf('Product Chain: Signup (#%d - %s)', $origProduct->pk(), $origProduct->title);
                $access->expire_date = sqlDate('yesterday');
                $access->save();
            }
        }
        if ($on_active_add = $origProduct->data()->get('product_chain_on_active_add')) {
            foreach ($on_active_add as $pid) {
                if (in_array($pid, $active)) {
                    continue;
                }

                $product = $this->getDi()->productTable->load($pid);

                $access = $this->getDi()->accessRecord;
                $access->user_id = $user->pk();
                $access->product_id = $product->pk();
                $access->begin_date = sqlDate('now');

                $period = new Am_Period($product->getBillingPlan()->first_period);
                $period->addTo($access->begin_date);

                $access->expire_date = $period->addTo($access->begin_date);
                $access->comment = sprintf('Product Chain: Signup (#%d - %s)', $origProduct->pk(), $origProduct->title);
                $access->save();
            }
        }
    }

    function onSubscriptionDeleted(Am_Event $e)
    {
        /* @var $user User */
        $user = $e->getUser();
        $origProduct = $e->getProduct();
        $active = $user->getActiveProductIds();
        if ($on_expire = $origProduct->data()->get('product_chain_on_expire')) {
            foreach ($on_expire as $pid) {
                if (in_array($pid, $active)) {
                    continue;
                }

                $product = $this->getDi()->productTable->load($pid);

                $access = $this->getDi()->accessRecord;
                $access->user_id = $user->pk();
                $access->product_id = $product->pk();
                $access->begin_date = sqlDate('now');

                $period = new Am_Period($product->getBillingPlan()->first_period);
                $period->addTo($access->begin_date);

                $access->expire_date = $period->addTo($access->begin_date);
                $access->comment = sprintf('Product Chain: Expire (#%d - %s)', $origProduct->pk(), $origProduct->title);
                $access->save();
            }
        }

        if ($on_expire_expire = $origProduct->data()->get('product_chain_on_expire_expire')) {
            foreach ($this->getDi()->accessTable->findBy(array(
                'user_id' => $user->pk(),
                'product_id' => $on_expire_expire,
                array('expire_date', '>=', sqlDate('now'))
            )) as $access) {
                $access->comment = sprintf('Product Chain: Expire (#%d - %s)', $origProduct->pk(), $origProduct->title);
                $access->expire_date = sqlDate('yesterday');
                $access->save();
            }
        }
    }

}
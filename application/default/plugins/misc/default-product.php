<?php

/**
 * @am_plugin_api 6.0
*/
class Am_Plugin_DefaultProduct extends Am_Plugin
{
    const PLUGIN_STATUS = self::STATUS_BETA;
    const PLUGIN_REVISION = '6.3.20';

    protected $_configPrefix = 'misc.';

    function getTitle()
    {
        return ___('Default Product');
    }

    function _initSetupForm(Am_Form_Setup $form)
    {
        $form->addSelect('product_id')
            ->setLabel(___("Default Product\n" .
                'access to this product will be added if customer has not any ' .
                'other active subscription and vise versa access to this ' .
                'product will be removed if customer signup to any other product'))
            ->loadOptions(['' => '-- Please Select'] +
                $this->getDi()->productTable->getOptions());

        $form->addMagicSelect('scope')
            ->setLabel("Scope\n" .
                "take into account only these products, leave it empty to use all products")
            ->loadOptions($this->getDi()->productTable->getProductOptions());
    }

    function onSubscriptionAdded(Am_Event $e)
    {
        /* @var $user User */
        $user = $e->getUser();
        $this->rebuild($user);
    }

    function onSubscriptionDeleted(Am_Event $e)
    {
        /* @var $user User */
        $user = $e->getUser();
        $this->rebuild($user);
    }

    function onUserAfterInsert(Am_Event $e)
    {
        /* @var $user User */
        $user = $e->getUser();
        $this->rebuild($user);
    }

    public function onRebuild(Am_Event_Rebuild $event)
    {
        $batch = new Am_BatchProcessor([$this, 'doWork']);
        $context = $event->getDoneString();
        $batch->run($context) ? $event->setDone() : $event->setDoneString($context);
    }

    function doWork(& $context, Am_BatchProcessor $batch)
    {
        $pageCount = 500;
        $q = $this->getDi()->db->queryResultOnly("
            SELECT *
            FROM ?_user
            ORDER BY user_id
            LIMIT ?d, ?d", (int) $context, $pageCount);
        $count = 0;
        while ($row = $this->getDi()->db->fetchRow($q)) {
            $count++;
            $user = $this->getDi()->userRecord;
            $user->fromRow($row);

            $this->rebuild($user, false);
            $context++;
            if (!$batch->checkLimits())
                return;
        }
        if (!$count)
            return true; // finished!
    }

    function rebuild(User $user, $doCheck = true)
    {
        // Disable for subusers;
        if(!empty($user->subusers_parent_id)) return;

        if ($pid = $this->getConfig('product_id')) {
            $scope = $this->getScope();
            /* @var $product Product */
            $product = $this->getDi()->productTable->load($pid);
            $origActiveProductIds = array_intersect($scope, $user->getActiveProductIds());
            $activeProductIds = array_diff($origActiveProductIds, [$pid]);
            if (empty($activeProductIds) && !in_array($pid, $origActiveProductIds)) {
                /* @var $access Access */
                $access = $this->getDi()->accessRecord;
                $access->user_id = $user->pk();
                $access->product_id = $product->pk();
                $access->begin_date = sqlDate('now');

                $period = new Am_Period($product->getBillingPlan()->first_period);
                $period->addTo($access->begin_date);

                $access->expire_date = $period->addTo($access->begin_date);
                $access->save();

                if ($doCheck) {
                    $user->refresh();
                    $user->checkSubscriptions();
                }
            } elseif (!empty($activeProductIds)) {
                $this->getDi()->accessTable->deleteBy([
                    'user_id' => $user->pk(),
                    'product_id' => $product->pk(),
                    ['expire_date', '>=', sqlDate('now')]
                ]);
                if ($doCheck) {
                    $user->checkSubscriptions();
                }
            }
        }
    }

    function getScope()
    {
        $pid = $this->getConfig('product_id');
        $_ = $this->getConfig('scope');
        $scope = $_ ?
            $this->getDi()->productTable->extractProductIds($_) :
            $this->getDi()->db->selectCol("SELECT product_id FROM ?_product");
        $scope[] = $pid;
        return $scope;
    }
}
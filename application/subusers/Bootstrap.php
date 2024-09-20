<?php
/**
 *
 * @title Subusers (Reseller)
 * @desc Allows customers to resell your site subscriptions or
 * organize corporate subscription
 *
 *
 * - if there are free limits, display links to :
 *      * generate unique user signup link
 *
 * - also revoke access by cron daily
 *
 * - check handling of refunds / expirations of main users
 *
 */
class Bootstrap_Subusers extends Am_Module
{
    const PLUGIN_STATUS = self::STATUS_PRODUCTION;
    const PLUGIN_REVISION = '6.3.20';
    const SAVED_FORM_TYPE = 'profile-subuser';
    const ADMIN_PERM_ID = 'subusers';

    protected $_session;

    static function activate($id, $pluginType)
    {
        parent::activate($id, $pluginType);
        self::setUpSubuserProfileFormIfNotExist(Am_Di::getInstance()->db);
    }

    function onGetPermissionsList(Am_Event $e)
    {
        $e->addReturn(___("Can manage subusers"), self::ADMIN_PERM_ID);
    }

    function onSavedFormTypes(Am_Event $e)
    {
        $e->getTable()->addTypeDef([
            'type' => self::SAVED_FORM_TYPE,
            'title' => 'Subuser Profile Form',
            'class' => 'Am_Form_Profile',
            'defaultTitle' => 'Customer Profile',
            'defaultComment' => 'subuser profile form',
            'isSingle' => true,
            'isSignup' => false,
            'noDelete' => true,
            'urlTemplate' => 'profile',
        ]);
    }

    function getSession()
    {
        if(empty($this->_session))
            $this->_session = Am_Di::getInstance()->session->ns('am_subusers');
        return $this->_session;
    }

    function onInitBlocks(Am_Event $e)
    {
        $e->getBlocks()->add(
           'content/before',
            new Am_Block_Base(___("Parent User Info"), 'subusers-logged-in', $this, [$this, 'loginBlock']),
            Am_Blocks::TOP
        );
        if ($this->getDi()->auth->getUserId() &&
            $this->getDi()->user->subusers_parent_id &&
            $this->getDi()->config->get('hide_member-main-subscriptions')) {
            $e->getBlocks()->remove('member-main-subscriptions');
        }

        if ($this->getDi()->auth->getUserId() &&
            ($cnt = $this->getDi()->auth->getUser()->data()->get('subusers_count'))) {

            $e->getBlocks()->add('member/main/right', new Am_Block_Base(___('Subusers Package'), 'subusers', $this,
                function(Am_View $v) use ($cnt) {
                    $user = $v->di->user;
                    $products = $v->di->productTable->getProductTitles(array_keys($cnt));
                    $out = [];
                    foreach ($cnt as $pid => $row) {
                        $out[] = sprintf('<li>%s &ndash; <strong>%d</strong>%s</li>',
                            $v->escape($products[$pid]), $row['avail_count'],
                                $row['max_expire'] == Am_Period::MAX_SQL_DATE ?
                                    '' : ___(', until %s', amDate($row['max_expire'])));
                    }
                    return sprintf(<<<CUT
<ul class="am-widget-list">%s</ul>
<p><a href="%s">%s</a></p>
CUT
                        ,
                        implode("\n", $out),
                        $v->escape($v->url('subusers')),
                        $v->escape(___('Manage Subusers')));
                }), Am_Block::TOP);
        }
        if ($this->getDi()->auth->getUserId() &&
            $this->getDi()->subusersInviteTable->countByUserId($this->getDi()->auth->getUserId())) {

            $e->getBlocks()->add('member/main/right', new Am_Block_Base(___('Join Account Invites'), 'subusers', $this,
                function(Am_View $v) {
                    $out = [];
                    $invites = $this->getDi()->subusersInviteTable->findByUserId($v->di->user->pk());
                    foreach ($invites as $_) {
                        $parent = $v->di->userTable->load($_->parent_id);
                        $out[] = sprintf('<li>%s &ndash; <a href="%s">%s</a> <span class="am-list-subscriptions_divider">|</span> <a href="%s">%s</a></li>',
                                $parent->getName(),
                                $v->url('subusers/invite/accept', ['id' => $v->di->security->obfuscate($_->pk())]),
                                $v->escape(___('Accept')),
                                $v->url('subusers/invite/reject', ['id' => $v->di->security->obfuscate($_->pk())]),
                                $v->escape(___('Reject'))
                            );
                    }
                    return sprintf(<<<CUT
<ul class="am-widget-list">%s</ul>
CUT
                        ,
                        implode("\n", $out));
                }), Am_Block::TOP);
        }
    }

    function loginBlock()
    {
        if(is_array($parent_user = $this->getSession()->parent_user)) {
            $login = $parent_user['reseller_login'];
            $url = $this->getDi()->url('subusers/index/restore-session');
            $text = ___('You are logged on as a reseller user: <strong>%s</strong>. Click %shere%s to restore your session.', $login, "<a href=\"{$url}\">", "</a>");
            return <<<CUT
<div class="am-notice">$text</div>
CUT;
        }
    }

    public function onEmailTemplateTagSets(Am_Event $e)
    {
        $tagSets = $e->getReturn();
        $tagSets['reseller'] = [
                '%reseller.name_f%' => 'Reseller First Name',
                '%reseller.name_l%' => 'Reseller Last Name',
                '%reseller.login%' => 'Reseller Username',
                '%reseller.email%' => 'Reseller E-Mail',
                '%reseller.user_id%' => 'Reseller Internal ID#',
                '%reseller.street%' => 'Reseller Street',
                '%reseller.street2%' => 'Reseller Street (Second Line)',
                '%reseller.city%' => 'Reseller City',
                '%reseller.state%' => 'Reseller State',
                '%reseller.zip%' => 'Reseller ZIP',
                '%reseller.country%' => 'Reseller Country',
        ];

        foreach ($this->getDi()->userTable->customFields()->getAll() as $field) {
            if (@$field->sql && @$field->from_config) {
                $tagSets['reseller']['%reseller.' . $field->name . '%'] = 'Reseller ' . $field->title;
            }
        }

        $e->setReturn($tagSets);
    }

    function onLoadProfileForm(Am_Event $e)
    {
        if (!$this->getDi()->config->get('subusers_different_profile_form')) return;

        /* @var $user User */
        $user = $e->getUser();

        if ($user->subusers_parent_id && !$e->getRequest()->getParam('c'))
            $e->setReturn($this->getDi()->savedFormTable->findFirstByType(self::SAVED_FORM_TYPE));
    }

    function onUserBeforeMerge(Am_Event $e)
    {
        $target = $e->getTarget();
        $source = $e->getSource();

        if ($target->subusers_parent_id && $source->subusers_parent_id
                && $target->subusers_parent_id!=$source->subusers_parent_id) {

            throw new Am_Exception_InputError(___('You can not merge subusers of different user'));
        }

        if ( ($target->subusers_parent_id && !$source->subusers_parent_id)
            || (!$target->subusers_parent_id && $source->subusers_parent_id)) {

                throw new Am_Exception_InputError(___('You can not merge ordinary user with subuser and vice versa'));
        }
    }

    function onUserMerge(Am_Event $e)
    {
        $target = $e->getTarget();
        $source = $e->getSource();

        $this->getDi()->db->query('UPDATE ?_user SET subusers_parent_id=? WHERE subusers_parent_id=?',
            $target->pk(), $source->pk());
        $this->getDi()->db->query('UPDATE ?_subusers_subscription SET user_id=? WHERE user_id=?',
            $target->pk(), $source->pk());
    }

    function onLoadSignupForm(Am_Event $event)
    {
        if (!$this->getDi()->config->get('subusers_cannot_pay')) return;
        $user = $event->getUser();
        if (!$user || !$user->get('subusers_parent_id')) return;

        $parent = $this->getDi()->userTable->load($user->subusers_parent_id);
        $p_identity = trim($parent->getName()) ? $parent->getName() : $parent->login;

        throw new Am_Exception_InputError(___('Signup/payment functions is disabled for this user account. Your membership is managed by %s.', $p_identity));
    }

    function onUserAfterUpdate(Am_Event_UserAfterUpdate $event)
    {
        $user = $event->getUser();
        $old  = $event->getOldUser();
        if ($user->get('is_locked') != $old->get('is_locked')) {
            $this->getDi()->db->query("UPDATE ?_user SET is_locked=?d WHERE subusers_parent_id=?d",
                $user->get('is_locked'), $user->pk());
        }
        if ($user->subusers_parent_id) {
            $this->getDi()->subusersInviteTable->deleteByUserId($user->pk());
        }
    }

    function onUserSearchConditions(Am_Event $event)
    {
        $event->addReturn(new Am_Query_User_Condition_IsSubuser);
        $event->addReturn(new Am_Query_User_Condition_SubuserAssignedTo);
    }

    function onSetupEmailTemplateTypes(Am_Event $event)
    {
        $event->addReturn([
            'id' => 'subusers.registration_mail',
            'title' => '%site_title% Registration',
            'mailPeriodic' => Am_Mail::USER_REQUESTED,
            'vars' => [
                'user',
                'password' => 'Plain-Text Password',
                'reseller',
                'reseller_product' => 'Reseller Product',
                'user_product' => 'Sub-User Product',
            ],
        ], 'subusers.registration_mail');
    }

    function onDaily(Am_Event $event)
    {
        $q = new Am_Query_User();
        $q->add(new Am_Query_Condition_Data('subusers_count', 'IS NOT NULL'));
        foreach ($q->selectPageRecords(0, 100000) as $user) // max 100000 resellers supported
        {
            $this->checkAndUpdate($user);
        }
    }

    function onUserMenuItems(Am_Event $e)
    {
        $e->addReturn([$this, 'buildMenu'], $this->getId());
    }

    function buildMenu(Am_Navigation_Container $nav, User $user, $order, $config)
    {
        $nav->addPage([
            'id' => 'subusers',
            'controller' => 'index',
            'module' => 'subusers',
            'action' => 'index',
            'label' => ___('Subusers'),
            'order' => $order,
            'visible' => $user->data()->get('subusers_count') > 0,
        ]);
    }

    function onUserMenu(Am_Event $e)
    {
        $nav = $e->getMenu();
        $user = $e->getUser();

        if (!$user->data()->get('subusers_count') &&
                $this->getDi()->config->get('subusers_cannot_pay') &&
                $user->subusers_parent_id)
        {
            foreach ($nav->findAllById('/^add-renew/', true) as $page) {
                $page->setVisible(false);
            }
            if ($page = $nav->findOneById('payment-history')) {
                $page->setVisible(false);
            }
            if ($page = $nav->findOneById('cart')) {
                $page->setVisible(false);
            }
        }
    }

    function onGetMemberLinks(Am_Event $e)
    {
        $user = $e->getUser();
        if ($this->getDi()->config->get('subusers_cannot_pay') && $user->subusers_parent_id) {
            $r = $e->getReturn();
            foreach($r as $k => $v) {
                if(strpos($k, 'payment-history') !== false) {
                    unset($r[$k]);
                }
            }
            $e->setReturn($r);
        }
    }

    function onRebuild(Am_Event_Rebuild $e)
    {
        $batch = new Am_BatchProcessor([$this, 'batchProcess']);
        $context = $e->getDoneString();
        $this->_batchStoreId = 'rebuild-' . $this->getId() . '-' . $this->getDi()->session->getId();
        if ($batch->run($context)) {
            $e->setDone();
        } else {
            $e->setDoneString($context);
        }
    }

    function batchProcess(& $context, Am_BatchProcessor $batch)
    {
        $db = $this->getDi()->db;
        $context = $context ? intval($context) : 0;
        $q = $db->queryResultOnly(<<<CUT
            SELECT *
                FROM ?_user
                WHERE user_id > ?d
                ORDER BY user_id
CUT
            , $context);

        while ($r = $db->fetchRow($q))
        {
            $context = $r['user_id'];
            $u = $this->getDi()->userRecord;
            $u->fromRow($r);

            $this->checkAndUpdate($u);
            if (!$batch->checkLimits()) return;
        }
        return true;
    }

    function onGridUserInitGrid(Am_Event_Grid $event)
    {
        $event->getGrid()->actionGet('export')
            ->addField(new Am_Grid_Field('subusers_parent_id', 'Subusers Parent ID'));
    }

    function onGridUserInitForm(Am_Event_Grid $event)
    {
        $form = $event->getGrid()->getForm();
        $user = $event->getGrid()->getRecord();
        if ($user->data()->get('subusers_count') ||
            ($user->isLoaded() && $this->getDi()->userTable->countBy(['subusers_parent_id' => $user->pk()])))
        {
            $el = new Am_Form_Element_Html('_subusers', ['id' => '_subusers-0']);
            $url = $this->getDi()->url('subusers/admin/tab/user_id/' . $user->pk());
            $el->setHtml('<div>'.___('This customer is a reseller').'. <a href="'.$url.'">details...</a></div>')->setLabel(___('Subusers'));
            $form->insertBefore($el, $form->getElementById('general'));
        } elseif ($parent_id = $user->get('subusers_parent_id')) {
            $el = new Am_Form_Element_Html('_subusers', ['id' => '_subusers-0']);
            $parent = $this->getDi()->userTable->load($parent_id, false);
            $url = $this->getDi()->view->userUrl((int)$parent_id);
            $html = sprintf('<div>'. ___('This customer is a subuser of') .' <a href="%s" class="link">%s %s &lt;%s&gt;</a> (%s) &ndash; <a href="%s" class="link" onclick="return confirm(\'Are you sure?\')">' .
                ___('detach') . '</a></div>',
                Am_Html::escape($url),
                Am_Html::escape($parent->name_f),
                Am_Html::escape($parent->name_l),
                Am_Html::escape($parent->email),
                Am_Html::escape($parent->login),
                $this->getDi()->url('subusers/admin/detach', "user_id={$user->pk()}")
            );
            $el->setHtml($html)->setLabel(___('Subusers'));
            $form->insertBefore($el, $form->getElementById('general'));
        } else {
            $el = new HTML_QuickForm2_Container_Group(null, ['id' => '_subusers-0']);
            $el->setLabel(___('Parent User'));
            $el->addText('_subusers_parent', ['placeholder' => ___('Type Username or E-Mail')])
                ->setId('subusers-parent');
            $el->addScript()->setScript(<<<CUT
    jQuery("input#subusers-parent").autocomplete({
        minLength: 2,
        source: amUrl("/admin-users/autocomplete")
    });
CUT
        );
            $form->insertBefore($el, $form->getElementById('general'));
        }
    }

    public function onGridUserBeforeSave(Am_Event_Grid $event)
    {
        $input = $event->getGrid()->getForm()->getValue();
        if (!empty($input['_subusers_parent'])) {
            $parent = $this->getDi()->userTable->findFirstByLogin($input['_subusers_parent'], false);
            if ($parent) {
                if ($parent->pk() == $event->getGrid()->getRecord()->pk()) {
                    throw new Am_Exception_InputError("Cannot assign user to himself");
                }
                $event->getGrid()->getRecord()->subusers_parent_id = $parent->pk();
            } else {
                throw new Am_Exception_InputError("Parent User not found, username specified: " . Am_Html::escape($input['_subusers_parent']));
            }
        }
    }

    function onUserTabs(Am_Event $e)
    {
        if(!$user_id = $e->getUserId()) {
            return;
        }
        if (!$user = $this->getDi()->userTable->load($user_id, false)) {
            return;
        }
        if (!$user->data()->get('subusers_count')) {
            return;
        }
        /* @var $menu Am_Navigation_User */
        $menu = $e->getTabs();
        $menu->addPage([
            'id' => 'subusers',
            'controller' => 'admin',
            'module' => 'subusers',
            'action' => 'tab',
            'label' => ___('Subusers'),
            'order' => 250,
            'resource' => self::ADMIN_PERM_ID,
            'params' => ['user_id' => $user_id],
        ]);
    }

    function onGridProductInitForm(Am_Event_Grid $e)
    {
        $op = $this->getDi()->productTable->getOptions();
        $product = $e->getGrid()->getRecord();
        foreach ($op as $k => $v) {
            if ($product->pk() == $k) {
                unset($op[$k]);
            }
        }

        $fs = $e->getGrid()->getForm()->addAdvFieldset(null, ['id' => 'subusers'])
            ->setLabel('Subusers');

        $products = $product->isLoaded() ?
            $this->getDi()->db->select("SELECT * FROM ?_subusers_product_product WHERE product_id=?", $product->pk()) :
            [];
        if (!$products) {
            $products = [
                [
                    'subusers_count' => '',
                    'subusers_product_id' => '',
                ]
            ];
        }

        $add = 0;
        foreach ($products as $data) {
            $this->addSubuserEl($fs, $data, $op, $add++, $add%2 ? '' : 'am-row-highlight');
        }

        $fs->addHtml(null, ['id' => 'subusers-add-product'])
            ->setHtml(<<<CUT
<a href="javascript:;" class="local" id="subusers-add-product">Add Subusers Product</a>
CUT
                );

        $fs->addScript()
            ->setScript(<<<CUT
function subusersHighlight(\$node)
{
    \$node.children().not('#row-subusers-add-product').each(function(i, el) {
        (i+1) % 2 ?
            $(el).removeClass('am-row-highlight') :
            $(el).addClass('am-row-highlight');
    })
}

jQuery('#subusers').on('click', '#subusers-add-product', function(){
    var that = this;
    jQuery(this).closest('#subusers').find('.am-row').slice(0,1).each(function(i, el){
        var obj = $(el).clone();
        obj.find('.subusers-product_id').nextAll().remove();
        obj.find('.subusers-product_id').closest('div').append(' <a class="local subusers-remove-product red" style="float:right" href="javascript:;">Remove</a>');
        obj.find('input').val('');
        obj.find('select').val('');
        jQuery(that).closest('.am-row').before(obj);
    });
    subusersHighlight($('#subusers .fieldset'));
});
jQuery('#subusers').on('click', '.subusers-remove-product', function(){
    var obj = $(this).closest('.am-row');
    obj.remove();
    subusersHighlight($('#subusers .fieldset'));
});
CUT
            );
    }

    public function onGridProductAfterSave(Am_Event_Grid $e)
    {
        $product = $e->getGrid()->getRecord();
        $vars = $e->getGrid()->getCompleteRequest()->toArray();
        $this->getDi()->db->query("DELETE FROM ?_subusers_product_product WHERE product_id=?", $product->pk());
        $data = [];
        foreach ($vars['_subusers_count'] as $k => $v) {
            if ($v && $vars['_subusers_product_id'][$k]) {
                @$data[$vars['_subusers_product_id'][$k]] += $v;
            }
        }
        if ($data) {
            $st = [];
            foreach ($data as $pid => $cnt) {
               $st[] = sprintf("(%d, %d, %d)", $product->pk(), $pid, $cnt);
            }
            $st = implode(", ", $st);
            $this->getDi()->db->query("INSERT INTO ?_subusers_product_product (product_id, subusers_product_id, subusers_count) VALUES $st");
        }
    }

    function addSubuserEl($fieldSet, $data, $op, $addRemove = false, $addClass = '')
    {
        $group = $fieldSet->addGroup(null, ['class' => $addClass])
            ->setLabel(___("Subusers Access\n(keep empty for non-reseller products)"));
        $group->setSeparator(' ');

        $group->addInteger('_subusers_count[]')
            ->setValue($data['subusers_count']);

        $group->addSelect('_subusers_product_id[]', ['class' => 'subusers-product_id', 'style' => 'max-width:350px'])
            ->loadOptions(['' => '-- ' . ___('Please select') . ' --'] + $op)
            ->setValue($data['subusers_product_id']);

        if ($addRemove) {
            $group->addHtml()
                ->setHtml(<<<CUT
 <a class="local subusers-remove-product red" style="float:right" href="javascript:;">Remove</a>
CUT
                );
        }
    }

    function renderMemberBlock()
    {
        $user = $this->getDi()->user;
        $ret = "";
        if ($user->subusers_parent_id) // subuser
        {
            $pending = $this->getDi()->subusersSubscriptionTable->countBy([
                'user_id' => $user->pk(),
                'status'  => 0,
            ]);
            if ($pending)
            {
                $ret .= ___('Subscription expired, please contact your reseller to upgrade subscription');
            }
        } elseif ($subusers_count = $user->data()->get('subusers_count')) { // reseller
            $pending = 0;
            foreach ($subusers_count as $product_id => $v)
                if ($v['pending_count']) $pending+=$v['pending_count'];
            if ($pending)
            {
                $url = $this->getDi()->url('member/add-renew');
                $ret .= ___('There are %d pending subuser subscriptions. %sUpgrade your access%s',
                    $pending, "<a href='$url'>", '</a>');
            }
        }
        if ($ret) {
            return "<div class='am-block subusers-member-notice'>$ret</div>";
        }
    }

    /**
     * handle case of access with future start date
     *
     * @param Am_Event $event
     */
    function onSubscriptionChanged(Am_Event $e)
    {
        $user = $e->getUser();
        if ($user->data()->get('subusers_count')) {
            return;
        }

        if($added = $e->getAdded())
        {
            if($this->getDi()->db->selectCell(<<<CUT
                SELECT COUNT(*)
                    FROM ?_subusers_product_product
                    WHERE `product_id` IN (?a)
CUT
                ,  $added)) {

                $this->checkAndUpdate($user);
            }
        }
    }

    function onAccessAfterInsert(Am_Event $e)
    {
        $this->_onAccessChanged($e);
    }

    function onAccessAfterUpdate(Am_Event $e)
    {
        $this->_onAccessChanged($e);
    }

    function onAccessAfterDelete(Am_Event $e)
    {
        $this->_onAccessChanged($e);
    }

    function _onAccessChanged(Am_Event $event)
    {
        try {
            $user = $event->getAccess()->getUser();
        } catch (Am_Exception_Db_NotFound $e) {
            return;
        }
        $this->checkAndUpdate($user);
    }

    /**
     * Calculate available limits, enable and disable users
     * @param User $user
     */
    function checkAndUpdate(User $user)
    {
        $counts = $this->calculateCounts($user->pk());
        $this->workoutLimits($user->pk(), $counts);
        $this->workoutExpires($user->pk(), $counts);
        if (!count($counts)) $counts = null;
        $user->data()->set('subusers_count', $counts)->update();
    }

    /**
     *
     *  foreach (subuser access as a)
     *    if (!parent_access_active) remove;
     *
     *  foreach (reseller access as a)
     *    find subscribed subusers
     *       if subscribed > limit
     *          unsubscribe first d users
     *       elseif subscribed < limit && pending
     *          subscribe first d users
     *
     *  delete all subuser access
     *  foreach
     *
     * 1. Disable overlimit users
     * 2. Enable pending users if limit allows to
     * 3. Update counts if any work is done
     */
    protected function workoutLimits($user_id, array & $counts)
    {
        $changes = 0;
        foreach ($counts as $product_id => $c)
        {
            /////////  reseller ordered - subusers active
            $avail = $c['avail_count'] - $c['active_count'];
            if ($avail<0)
            {
                $this->disableSubusers($user_id, $product_id, - $avail);
                $changes++;
            } elseif (($avail>0) && ($c['pending_count']>0)) {
                $this->enableSubusers($user_id, $product_id, min($avail, $c['pending_count']));
                $changes++;
            }
        }
        if ($changes) {
            $counts = $this->calculateCounts($user_id);
        }
    }

    protected function workoutExpires($user_id, $counts)
    {
        foreach ($counts as $product_id => $c)
        {
            $aids = $this->getDi()->db->selectCol(<<<CUT
                SELECT access_id
                    FROM ?_access
                    WHERE transaction_id IN (
                        SELECT CONCAT('subusers.', ss.subusers_subscription_id)
                            FROM ?_subusers_subscription ss
                            LEFT JOIN ?_user u USING (user_id)
                            WHERE u.subusers_parent_id=?
                        )
                        AND product_id = ?
CUT
                , $user_id, $product_id);

            if ($aids) {
                $this->getDi()->db->query(<<<CUT
                UPDATE ?_access
                    SET expire_date = ?
                    WHERE access_id IN (?a);
CUT
                , $c['max_expire'], $aids);
            }
        }
    }

    protected function disableSubusers($user_id, $product_id, $toDisable)
    {
        // find last access records for $user_id-$product_id
        foreach ($this->getDi()->subusersSubscriptionTable->selectToDisable($user_id, $product_id, $toDisable) as $s)
            $s->disable();
    }

    protected function enableSubusers($user_id, $product_id, $toEnable)
    {
        // find unused access records for $user_id-$product_id
        foreach ($this->getDi()->subusersSubscriptionTable->selectToEnable($user_id, $product_id, $toEnable) as $s)
            $s->enable();
    }

    /**
     * @return array subusers_product_id => array('avail_count'=>x,'active_count'=>x,'pending_count'=>x)
     */
    protected function calculateCounts($user_id)
    {
        $ret = $this->getDi()->db->select("
        SELECT
            p.subusers_product_id as ARRAY_KEY,
                SUM(IFNULL(a.qty, 1) * p.subusers_count * IF(a.begin_date <= ?, 1, 0)) AS avail_count,
                MAX(a.expire_date) AS max_expire
            FROM ?_access a
                LEFT JOIN ?_subusers_product_product p USING (product_id)
            WHERE a.user_id=?d AND a.expire_date >= ?
            GROUP BY p.subusers_product_id
            HAVING p.subusers_product_id > 0
        ", $this->getDi()->sqlDate, $user_id, $this->getDi()->sqlDate);

        $ret2 = $this->getDi()->db->select("
        SELECT s.product_id AS ARRAY_KEY,
            SUM(IF(s.status>0, 1, 0)) AS active_count,
            SUM(IF(s.status=0, 1, 0)) AS pending_count
            FROM ?_subusers_subscription s
                INNER JOIN ?_user u USING (user_id)
            WHERE u.subusers_parent_id = ?d
            GROUP BY product_id
        ", $user_id);
        // array_merge_recursive
        foreach ($ret2 as $k => $v) {
            if (empty($ret[$k])) {
                $ret[$k] = $v;
            } else {
                $ret[$k] = array_merge($ret[$k], $v);
            }
        }
        foreach ($ret as &$v)
        {
            if (empty($v['avail_count'])) $v['avail_count'] = 0;
            if (empty($v['max_expire'])) $v['max_expire'] = null;
            if (empty($v['active_count'])) $v['active_count'] = 0;
            if (empty($v['pending_count'])) $v['pending_count'] = 0;
        }
        return $ret;
    }

    function onUserAfterDelete(Am_Event $e)
    {
        $id = $e->getUser()->pk();
        $this->getDi()->subusersSubscriptionTable->deleteBy(['user_id' => $id]);
        $this->getDi()->db->query("UPDATE ?_user SET subusers_parent_id = 0 WHERE subusers_parent_id = ?", $id);
    }

    function onDbUpgrade(Am_Event $e)
    {
        if (version_compare($e->getVersion(), '4.2.20') < 0)
        {
            echo "Set Up Subuser profile form...";
            $this->setUpSubuserProfileFormIfNotExist($this->getDi()->db);
            echo "Done<br>\n";
        }
        if (version_compare($e->getVersion(), '5.1.1') <= 0)
        {
            echo "Enable Registration Email (Backward Comptability)...";
            Am_Config::saveValue('subusers.registration_mail', 1);
            echo "Done<br>\n";
        }
        if ($products = $this->getDi()->productTable->findBy([['subusers_count', '>', 0]])) {
            foreach ($products as $p) {
                if ($p->subusers_product_id) {
                    $this->getDi()->db->query("INSERT INTO ?_subusers_product_product SET ?a",
                        [
                            'product_id' => $p->pk(),
                            'subusers_product_id' => $p->subusers_product_id,
                            'subusers_count' => $p->subusers_count
                        ]);
                }
            }
            $this->getDi()->db->query("UPDATE ?_product SET subusers_product_id = NULL, subusers_count = NULL;");
        }
    }

    protected static function setUpSubuserProfileFormIfNotExist(DbSimple_Interface $db)
    {
        $tbl = Am_Di::getInstance()->savedFormTable->getName();
        if (!$db->selectCell("SELECT COUNT(*) FROM {$tbl} WHERE type=?", self::SAVED_FORM_TYPE)) {
            $max = $db->selectCell("SELECT MAX(sort_order) FROM {$tbl}");
            $db->query("INSERT INTO {$tbl} (title, comment, type, fields, sort_order)
                SELECT 'Subuser Profile Form', 'subuser profile form', ?, fields, ?
                FROM {$tbl} WHERE type=? AND FIND_IN_SET(?,default_for)",
                self::SAVED_FORM_TYPE, ++$max, SavedForm::T_PROFILE, SavedForm::D_PROFILE);
        }
    }
}
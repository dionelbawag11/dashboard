<?php

class Am_Grid_Action_Group_UserAssignGroup extends Am_Grid_Action_Group_Abstract
{
    protected $needConfirmation = true;
    protected $form;
    protected $_vars;

    public function __construct($id = null, $title = null)
    {
        parent::__construct('user-assign-group', ___("Assign Group"));
    }

    public function handleRecord($id, $record)
    {
        $groups = $record->getGroups();
        $record->setGroups(array_merge($groups, $this->_vars['groups']));
    }

    public function getForm()
    {
        if (!$this->form) {
            $prefix = $this->grid->getId() . '_';
            $this->form = new Am_Form_Admin;

            $this->form->addMagicSelect("{$prefix}groups", ['class' => 'am-combobox'])
                ->setLabel(___("Assign Group"))
                ->loadOptions(Am_Di::getInstance()->userGroupTable->getSelectOptions())
                ->setJsOptions('{allowSelectAll: true}');

            $this->form->addSaveButton(___("Assign Group"));
        }
        return $this->form;
    }

    public function renderConfirmationForm($btn = null, $addHtml = null)
    {
        $this->getForm();
        $vars = $this->grid->getCompleteRequest()->toArray();
        $vars[$this->grid->getId() . '_confirm'] = 'yes';
        foreach ($vars as $k => $v)
            if ($this->form->getElementsByName($k))
                unset($vars[$k]);
        foreach(Am_Html::getArrayOfInputHiddens($vars) as $k => $v)
            $this->form->addHidden($k)->setValue($v);

        $url_yes = $this->grid->makeUrl(null);
        $this->form->setAction($url_yes);
        echo $this->renderTitle();
        echo (string)$this->form;
    }

    public function run()
    {
        if (!$this->getForm()->validate()) {
            echo $this->renderConfirmationForm();
        } else {
            $prefix = $this->grid->getId().'_';
            foreach ($this->getForm()->getValue() as $k => $v) {
                if (strpos($k, $prefix)===0)
                    $this->_vars[substr($k, strlen($prefix))] = $v;
            }
            return parent::run();
        }
    }
}

class Am_Grid_Action_Group_UserRemoveGroup extends Am_Grid_Action_Group_Abstract
{
    protected $needConfirmation = true;
    protected $form;
    protected $_vars;

    public function __construct($id = null, $title = null)
    {
        parent::__construct('user-remove-group', ___("Remove Group"));
    }

    public function handleRecord($id, $record)
    {
        $groups = $record->getGroups();
        $record->setGroups(array_diff($groups, $this->_vars['groups']));
    }

    public function getForm()
    {
        if (!$this->form) {
            $prefix = $this->grid->getId() . '_';
            $this->form = new Am_Form_Admin;

            $this->form->addMagicSelect("{$prefix}groups", ['class' => 'am-combobox'])
                ->setLabel(___("Remove Group"))
                ->loadOptions(Am_Di::getInstance()->userGroupTable->getSelectOptions())
                ->setJsOptions('{allowSelectAll: true}');

            $this->form->addSaveButton(___("Remove Group"));
        }
        return $this->form;
    }

    public function renderConfirmationForm($btn = null, $addHtml = null)
    {
        $this->getForm();
        $vars = $this->grid->getCompleteRequest()->toArray();
        $vars[$this->grid->getId() . '_confirm'] = 'yes';
        foreach ($vars as $k => $v)
            if ($this->form->getElementsByName($k))
                unset($vars[$k]);
        foreach(Am_Html::getArrayOfInputHiddens($vars) as $k => $v)
            $this->form->addHidden($k)->setValue($v);

        $url_yes = $this->grid->makeUrl(null);
        $this->form->setAction($url_yes);
        echo $this->renderTitle();
        echo (string)$this->form;
    }

    public function run()
    {
        if (!$this->getForm()->validate()) {
            echo $this->renderConfirmationForm();
        } else {
            $prefix = $this->grid->getId().'_';
            foreach ($this->getForm()->getValue() as $k => $v) {
                if (strpos($k, $prefix)===0)
                    $this->_vars[substr($k, strlen($prefix))] = $v;
            }
            return parent::run();
        }
    }
}

class Am_Grid_Action_Group_ResendConfirmationLink extends Am_Grid_Action_Group_Abstract
{
    protected $needConfirmation = true;

    public function handleRecord($id, $record)
    {
        $tpl = Am_Mail_Template::load('verify_email_signup');

        $u = $this->grid->getDi()->userRecord;
        $u->name_f = $record->name_f;
        $u->name_l = $record->name_l;
        $u->email = $record->email;

        $tpl->setUser($u);
        $tpl->setCode($record->code);
        $tpl->setUrl($record->url);

        $tpl->send($u);
    }

    public function getIds()
    {
        $ids = explode(",", $this->grid->getRequest()->get(Am_Grid_Editable::GROUP_ID_KEY));
        if (in_array(self::ALL, $ids)) return [self::ALL];
        return array_filter(array_map(function($_) {return preg_replace('/[^a-zA-Z0-9]/', '', $_);}, $ids));
    }
}

class Am_Grid_Action_Group_PasswordConfirmedDelete extends Am_Grid_Action_Group_Delete
{
    public function __construct($id = null, $title = null)
    {
        parent::__construct($id, $title);
        $this->setTarget('_top');
    }

    public function renderConfirmationForm($btn = null, $addHtml = null)
    {
        if (!$this->getSession()->login_ok) {
            $addHtml = ___('Enter admin password for confirmation') .
                ":<br /><input type='password' name='_admin_pass' size=20/><br /><br />" . $addHtml;
        } else {
            $addHtml = null;
        }
        return parent::renderConfirmationForm($btn, $addHtml);
    }

    public function run()
    {
        if (!$this->getSession()->login_ok)
        {
            $admin_pass = $this->grid->getCompleteRequest()->getPost('_admin_pass');
            if (!$admin_pass)
            {
                echo $this->renderConfirmation();
                return;
            } elseif (!$this->grid->getDi()->authAdmin->getUser()->checkPassword($admin_pass)) {
                echo "<div style='color: red'>".___('The password is entered incorrectly')."</div>";
                echo $this->renderConfirmation();
                return;
            }
        }
        $this->getSession()->login_ok = true;
        return parent::run();
    }

    function getSession()
    {
        static $session;
        if ($session) return $session;
        $session = $this->grid->getDi()->session->ns('am_admin_users_delete');
        $session->setExpirationSeconds(3600);
        return $session;
    }
}

class Am_Form_Admin_User extends Am_Form_Admin
{
    /** @var User */
    protected $record;

    function __construct($record)
    {
        $this->record = $record;
        parent::__construct('user-' . ($record->isLoaded() ? $record->pk() : 'new'));
    }

    function checkUniqLogin($login)
    {
        if (!preg_match(Am_Di::getInstance()->userTable->getLoginRegex(), $login))
            return ___('Username contains invalid characters - please use digits, letters, dash and underscore');

        // We need to check login only when user is not exists, or when he change his username.
        $user_id = $this->record ? $this->record->pk() : null;

        if(!$user_id || (strcasecmp($this->record->login, $login)!==0))
            if (!$this->record->getTable()->checkUniqLogin($login, $user_id))
                return ___('Username %s is already taken. Please choose another username', Am_Html::escape($login));
    }

    function checkUniqEmail(array $group)
    {
        $email = $group['email'];
        if (!Am_Validate::email($email))
            return ___('Please enter valid Email');

        // Do the same for email if case there are plugins that use email as username.
        // We need to check email only when user is not exists, or when he change his email.
        $user_id = $this->record ? $this->record->pk() : null;

        if(!$user_id || (strcasecmp($this->record->email, $email)!==0))
            if (!$this->record->getTable()->checkUniqEmail($email, $user_id))
                return ___('An account with the same email already exists.');
    }

    function init()
    {
        if ($this->record->isLoaded()) {
            $url = Am_Di::getInstance()->url('admin-users/data/user_id/'.$this->record->pk());
            $label = Am_Html::escape(___('Attached User Data (XML)'));
            $this->addEpilog(<<<CUT
<div style="overflow:hidden">
<a class="link user-data" target="_top" style="float:right" href="$url">$label</a>
</div>
CUT
                );
        }

        if ($this->record->isLoaded() &&
            $this->record->is_locked == 0 &&
            $this->record->disable_lock_until &&
            $this->record->disable_lock_until > Am_Di::getInstance()->sqlDateTime)
        {
             $this->addStatic(null, ['class' => 'am-row-wide am-row-highlight'])
                    ->setContent('<div style="text-align:center">' .
                        ___('Auto-locking for this customer is temporary disabled until %s',
                            amDatetime($this->record->disable_lock_until)) . '</span></div>');
        } elseif ($this->record->isLoaded() && $this->record->is_locked == 0) {
            $r = Am_Di::getInstance()->accessLogTable->findFirstByUserId($this->record->pk(), 'time DESC');
            if ($r && Am_Di::getInstance()->accessLogTable->isIpCountExceeded($this->record->pk(), $r->remote_addr)) {
                $url = Am_Di::getInstance()->url('admin-setup/loginpage');
                $url_disable = Am_Di::getInstance()->url('admin-users/disable-auto-lock', [
                    'id' => $this->record->pk(),
                    'b' => $_SERVER['REQUEST_URI'],
                ]);
                $this->addStatic(null, ['class' => 'am-row-wide am-row-highlight'])
                    ->setContent('<div style="text-align:center"><span class="red">' . ___('This user exceeded %sAccount Sharing Prevention%s limits and temporarily locked.', "<a href=\"$url\">", '</a>') . '</span> ' .
                        ___('You can temporary %sdisable auto-locking for this customer for 1 day%s and allow access for his account.',
                        "<a href=\"$url_disable\" target=\"_top\" class=\"link\">", '</a>') . '</span></div>');
            }
        } elseif ($this->record->isLoaded() && $this->record->is_locked == 1) {
            $this->addStatic(null, ['class' => 'am-row-wide am-row-highlight'])
                ->setContent('<div style="text-align:center">'
                    . ___('This user is Permanently Locked.')
                    . '</span></div>');
        }
        /* General Settings */
        $fieldSet = $this->addFieldset('general', ['id'=>'general'])
            ->setLabel(___('General'));

        $login = $fieldSet->addText('login', ['class' => 'am-el-wide'])
            ->setLabel(___('Username'))
            ->setId('login');
        $login->addRule('required');
        $login->addRule('callback2', '-error-', [$this, 'checkUniqLogin']);

        if ($this->record->isLoaded()) {
            $countSameIp = Am_Di::getInstance()->userTable->countBy([
                'remote_addr' => $this->record->remote_addr,
                ['user_id', '<>', $this->record->pk()]
            ]);

            $ban = Am_Di::getInstance()->banTable->findBan(['ip' => $this->record->remote_addr]);
            $banInfoHtml = $ban ?
                sprintf('<strong>%s</strong>', Am_Html::escape(___('This IP is Banned'))) :
                (Am_Di::getInstance()->authAdmin->getUser()->hasPermission(Am_Auth_Admin::PERM_BAN) ?
                    sprintf('<a class="link" href="%s">%s</a>',
                        Am_Di::getInstance()->url('admin-ban', [
                            '_ip_a' => 'insert',
                            '_ip_b' => Am_Di::getInstance()->view->userUrl($this->record->pk()),
                            'value' => $this->record->remote_addr,
                            'comment' => sprintf('%s (%s %s)', $this->record->login,
                                $this->record->name_f,
                                $this->record->name_l)]),
                        Am_Html::escape(___('Block This IP Address'))) :
                    '');

            $savedForm = $this->record->saved_form_id ? Am_Di::getInstance()->savedFormTable->load($this->record->saved_form_id, false) : false;
            $savedFormHtml = $savedForm ? "<div style='margin-top:.4em'>Origin: {$savedForm->title} ({$savedForm->comment})</div>" : '';

            $fieldSet->addStatic('_signup_info', null, ['label' => ___('Signup Info')])
                ->setContent(
                    sprintf('<div>%s%s%s<time title="%s" datetime="%s">%s</time> %s</div>%s%s',
                        filterIp($this->record->remote_addr),
                        ($this->record->user_agent ? sprintf(' (<span title="%s">%s&hellip;</span>)',
                            Am_Html::escape($this->record->user_agent),
                            Am_Html::escape(trim(substr($this->record->user_agent,0,12)))) : ''),
                        ___(' at '),
                        Am_Di::getInstance()->view->getElapsedTime($this->record->added),
                        date('c', amstrtotime($this->record->added)),
                        amDatetime($this->record->added),
                        $banInfoHtml,
                        ($countSameIp ? '<div>' . ___('There is %s users with same registration IP Address',
                        '<a class="link" href="' . Am_Di::getInstance()->url('admin-users',
                            ['_u_filter'=>$this->record->remote_addr]) . '">' . $countSameIp . '</a>') . '</div>' : ''),
                        $savedFormHtml)
                );

            $ban = Am_Di::getInstance()->banTable->findBan(['ip' => $this->record->last_ip]);
            $banInfoHtml = $ban ?
                sprintf(' <strong>%s</strong>', Am_Html::escape(___('This IP is Banned'))) :
                (Am_Di::getInstance()->authAdmin->getUser()->hasPermission(Am_Auth_Admin::PERM_BAN) ?
                    sprintf(' <a class="link" href="%s">%s</a>',
                        Am_Di::getInstance()->url('admin-ban', [
                            '_ip_a' => 'insert',
                            '_ip_b' => Am_Di::getInstance()->view->userUrl($this->record->pk()),
                            'value' => $this->record->last_ip,
                            'comment' => sprintf('%s (%s %s)', $this->record->login,
                                $this->record->name_f,
                                $this->record->name_l)]),
                        Am_Html::escape(___('Block This IP Address'))) :
                    '');

            $fieldSet->addStatic('_signin_info', null, ['label' => ___('Last Signin Info')])->setContent(
                sprintf("<div>%s</div>", $this->record->last_login ?
                    sprintf('%s%s%s<time title="%s" datetime="%s">%s</time> %s',
                        filterIp($this->record->last_ip),
                        ($this->record->last_user_agent ? sprintf(' (<span title="%s">%s&hellip;</span>)',
                            Am_Html::escape($this->record->last_user_agent),
                            Am_Html::escape(trim(substr($this->record->last_user_agent,0,12)))) : ''),
                        ___(' at '),
                        Am_Di::getInstance()->view->getElapsedTime($this->record->last_login),
                        date('c', amstrtotime($this->record->last_login)),
                        amDatetime($this->record->last_login),
                        $banInfoHtml) : ___('Never'))
            );
        }

        $label = Am_Html::escape(___('Add Comment'));
        $g = $fieldSet->addGroup(null, ['id' => 'comment'])
            ->setLabel(___("Comment\nfor admin reference"));
        $g->setSeparator(' ');
        $g->addTextarea('comment', ['class' => 'am-el-wide', 'style' => "display:none"])
            ->setAttribute('rows', 2);
        $g->addHtml()
            ->setHtml(<<<CUT
<a href="javascript:;" class="link local" id="user-add-comment">$label</a>
CUT
                );
        $g->addScript()
            ->setScript(<<<CUT
(function(){
    var show = jQuery("textarea[name=comment]").val().length > 0;
    jQuery("textarea[name=comment]").toggle(show);
    jQuery("#user-add-comment").toggle(!show);

    jQuery("#user-add-comment").click(function(){
        jQuery(this).hide();
        jQuery("textarea[name=comment]").show();
        jQuery("textarea[name=comment]").focus();
    });
})();
CUT
            );

        if (!$this->record->isLoaded()) {
            $g = $fieldSet->addGroup('', ['id' => 'pass-group'])
                ->setLabel(___('Password'));
            $g->addPassword('_pass', ['autocomplete' => 'new-password'])
                ->setLabel(___('Password'))
                ->addRule('required');
        } else {
            $g = $fieldSet->addGroup('', ['id' => 'pass-group'])
                ->setLabel(___('Password'));
            $g->addHtml()->setHtml('<a href="javascript:;" class="am-change-pass local">' . ___('change') . '</a><div style="display:none">');
            $g->addPassword('_pass', ['autocomplete'=>'new-password'])
                ->setLabel(___('Password'));
            $g->addHtml()->setHtml('</div>');
        }

        $nameField = $fieldSet->addGroup('', ['id' => 'name'], ['label' => ___("Name\nFirst and Last name")]);
        $nameField->setSeparator(' ');
        $nameField->addText('name_f', ['size'=>15]);
        $nameField->addText('name_l', ['size'=>15]);

        $gr = $fieldSet->addGroup('', [
            'class' => 'am-row-required',
            'id'=>'user-email-group'])
            ->setLabel(___('E-Mail Address'));
        $gr->setSeparator(' ');
        $gr->addText('email', ['size' => 30])->addRule('required');
        $gr->addRule('callback2', '-error-', [$this, 'checkUniqEmail']);

        if ($this->record && $this->record->isLoaded()) {
            $resendText = Am_Html::escape(___('Resend Signup E-Mail'));
            $sending = Am_Html::escape(___('sending'));
            $sent = Am_Html::escape(___('sent successfully'));
            $id = $this->record->pk();

            $gr->addStatic()->setContent(<<<CUT
<input type='button' value='$resendText' id='resend-signup-email' />
<script type='text/javascript'>
jQuery(function(){
jQuery("#resend-signup-email").click(function(){
    var btn = this;
    var txt = btn.value;
    btn.value += '...($sending)...';
    var url = amUrl('/admin-users/resend-signup-email',1);
    jQuery.post(url[0], jQuery.merge(url[1], [{name:'id', value:$id}]), function(){
        btn.value = txt + '...($sent)';
        setTimeout(function(){ btn.value = txt; }, 600);
    });
});
});
</script>
CUT
            );
        }
        $fieldSet->addAdvRadio('is_locked', ['id' => 'is_locked'])
            ->loadOptions([
                ''   => 'No',
                '1'  => '<span class="red">'.___('Yes, locked').'</span>',
                '-1' => '<em>'.___('Disable auto-locking for this customer').'</em>',
            ])->setLabel(___('Permanently Locked'));

        $fieldSet->addAdvCheckbox('is_approved', ['id' => 'is_approved'])
                 ->setLabel(___('Is Approved'));

        $fieldSet->addAdvRadio('unsubscribed', ['id' => 'unsubscribed'])
            ->setLabel(___("Is Unsubscribed?
if enabled, this will
unsubscribe the customer from:
* messages that you send from aMember Cp,
* autoresponder messages,
* subscription expiration notices"))
            ->loadOptions([
                '0'   => ___('No'),
                '1'  => ___('Yes, do not e-mail this customer for any reasons'),
            ]);

        $fieldSet->addCategory('_groups', ['id' => 'usergroups'], [
                'base_url' => 'admin-user-groups',
                'link_title' => ___('Edit Groups'),
                'title' => ___('User Groups'),
                'options' => Am_Di::getInstance()->userGroupTable->getOptions()])
            ->setLabel(___('User Groups'));

        $this->addText('phone')->setLabel(___('Phone Number'));
        $gr = $this->addGroup(null, ['id' => 'grp-mobile_number'])->setLabel(___("Mobile Phone Number"));
        $gr->setSeparator(' ');

        $select = $gr->addSelect('mobile_area_code', ['id' => 'am-mobile-area']);
        $phone = $gr->addText('mobile_number');
        $this->addScript()->setScript(<<<JS
    jQuery(document).ready(function(){
        jQuery("#am-mobile-area").select2({
            width: '80px',
            dropdownAutoWidth : true,
            templateSelection: function(state){
                if (!state.id) {
                    return state.text;
                }
                let re = /\(\s*([0-9+-]+)\s*\)/;
                let res = state.text.match(re);
                return res[1]??"";
            }
        });
    })
JS
        );
        $select->addOption("", "");
        foreach(Am_Di::getInstance()->countryTable->getPhoneCodeOptions() as $option){
            $select->addOption($option['value'], $option['key']);
        }

        $this->insertAdditionalFields();
        $this->insertAddressFields();
    }

    function addSaveButton($title = null)
    {
        if (!$this->record->isLoaded()) {
            $group = $this->addGroup();
            $group->addStatic()->setContent('<label>');
            $group->addAdvCheckbox('_registration_mail');
            $group->addStatic()->setContent(sprintf(' <strong>%s</strong></label><br /><br />',
                Am_Html::escape(___('Send Registration E-Mail to this user'))));
            $group->addElement(parent::addSaveButton($title));
        } else {
            parent::addSaveButton($title);
        }
    }

    function insertAddressFields()
    {
        $fieldSet = $this->addAdvFieldset('address', ['id' => 'address_info'])
            ->setLabel(___('Address Info'));
        $fieldSet->addText('street', ['class' => 'am-el-wide'])->setLabel(___('Street Address'));
        $fieldSet->addText('street2', ['class' => 'am-el-wide'])->setLabel(___('Street Address (Second Line)'));
        $fieldSet->addText('city', ['class' => 'am-el-wide'])->setLabel(___('City'));
        $fieldSet->addText('zip')->setLabel(___('ZIP Code'));

        $fieldSet->addSelect('country')->setLabel(___('Country'))
            ->setId('f_country')
            ->loadOptions(Am_Di::getInstance()->countryTable->getOptions(true));

        $group = $fieldSet->addGroup()->setLabel(___('State'));
        $state =$group->addSelect('state', '', ['intrinsic_validation'=>false])
            ->setId('f_state');
        /* @var $state HTML_QuickForm2_Select */
        $state->addFilter([$this, '_filterState']);
        if (!empty($this->record->country))
            $state->loadOptions(Am_Di::getInstance()->stateTable->getOptions($this->record->country, true));
        $group->addText('state')->setId('t_state')->setAttribute('disabled', 'disabled');
    }

    public function _filterState($state)
    {
        return preg_replace('#[^A-Za-z0-9-]#', '', $state);
    }

    function insertAdditionalFields()
    {
        $fields = Am_Di::getInstance()->userTable->customFields()->getAll();

        uksort($fields, [Am_Di::getInstance()->userTable, 'sortCustomFields']);
        $exclude = [];
        $c = $this;

        $toAdd = [];
        foreach ($fields as $k => $f) {
            if (!in_array($f->name, $exclude)
                && strpos($f->name, 'aff_')!==0
                && $f->type !== 'hidden') {

                $toAdd[] = $f;
            }
        }
        if (count($toAdd)>5) {
            $c = $this->addAdvFieldset('additional')
                ->setLabel(___('Additional Fields'));
        }

        foreach ($toAdd as $f) {
            $f->addToQf2($c);
        }
    }

    protected function renderClientRules(HTML_QuickForm2_JavascriptBuilder $builder)
    {
        $generate = ___("generate");
        $builder->addElementJavascript(<<<CUT
jQuery(document).ready(function(){
    jQuery(document).on('click', "a.am-change-pass", function(){
        jQuery(this).next().show().find('input').focus();
        jQuery(this).remove();
    });
    if (jQuery("input#_pass-0").val()) {
        jQuery("input#_pass-0").click();
    }
    var pass0 = jQuery("input#_pass-0").after(" <a href='javascript:;' class='local' id='generate-pass'>$generate</a>");
    jQuery(document).on('click', "a#generate-pass", function(){
        if (pass0.attr("type")!="text")
        {
            pass0.replaceWith("<input type='text' name='"+pass0.attr("name")
                    +"' id='"+pass0.attr("id")
                    +"' size='"+pass0.attr("size")
                    +"' />");
            pass0 = jQuery("input#_pass-0");
        }
        var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz";
        var pass = "";
        var len = 9;
        for(i=0;i<len;i++)
        {
            x = Math.floor(Math.random() * 62);
            pass += chars.charAt(x);
        }
        pass0.val(pass);
    });
});
CUT
        );
    }
}

class Am_Grid_Action_Group_MassSubscribe extends Am_Grid_Action_Group_Abstract
{
    protected $needConfirmation = true;
    protected $form;
    protected $_vars, $_products;

    public function __construct()
    {
        parent::__construct('mass_subscribe', ___('Mass Subscribe'));
        $this->setTarget('_top');
    }

    public function handleRecord($id, $record)
    {
        switch ($this->_vars['action']) {
            case 'access' :
                switch($this->_vars['access_type']) {
                    case 'exact' :
                        $begin_date = $this->_vars['begine_date'];
                        $expire_date = $this->_vars['expire_date'];
                        break;
                    case 'period' :
                        $invoice = $this->grid->getDi()->invoiceRecord;
                        $invoice->setUser($this->grid->getDi()->userTable->load($id));

                        $product = $this->grid->getDi()->productTable->load($this->_vars['product_id']);
                        $begin_date = $product->calculateStartDate($this->grid->getDi()->sqlDate, $invoice);

                        $p = new Am_Period($this->_vars['period']);
                        $expire_date = $p->addTo($begin_date);
                        break;
                }

                $a = $this->grid->getDi()->accessRecord;
                $a->begin_date = $begin_date;
                $a->expire_date = $expire_date;
                $a->product_id = $this->_vars['product_id'];
                $a->user_id = $id;
                $a->comment = $this->_vars['comment'];
                $a->insert();
                break;
            case 'payment' :
                $invoice = $this->grid->getDi()->invoiceRecord;
                $invoice->user_id = $id;
                $invoice->add($this->grid->getDi()->productTable->load($this->_vars['product_id']));

                $items = $invoice->getItems();
                $item = $items[0];
                $item->first_price = $item->first_total = $this->_vars['amount'];
                $item->second_price = $item->second_total = 0;
                $item->rebill_times = 0;
                $item->second_period = null;

                $invoice->first_subtotal = $invoice->first_total = $this->_vars['amount'];
                $invoice->second_subtotal = $invoice->second_total = 0;
                $invoice->rebill_times = 0;
                $invoice->second_period = null;
                $invoice->first_period = $item->first_period;

                $invoice->paysys_id = $this->_vars['paysys_id'];
                $invoice->comment = $this->_vars['comment'] ? $this->_vars['comment'] : 'mass-subscribe';
                $invoice->save();

                $tr = new Am_Paysystem_Transaction_Manual($this->grid->getDi()->plugins_payment->loadGet($invoice->paysys_id));
                $tr->setAmount($this->_vars['amount']);
                $tr->setTime(new DateTime($this->_vars['dattm']));
                $tr->setReceiptId('ms-'. uniqid() . '-' . $invoice->pk());
                if ($this->_vars['amount']) {
                    $invoice->addPayment($tr);
                } else {
                    $invoice->addAccessPeriod($tr);
                }
                break;
        }
    }

    public function getForm()
    {
        if (!$this->form) {
            $id = $this->grid->getId();
            $this->form = new Am_Form_Admin('mass-subscribe');
            $this->form->addSelect($id . '_product_id', ['class' => 'am-combobox-fixed'])
                ->setLabel(___('Product'))
                ->loadOptions($this->grid->getDi()->productTable->getOptions(false))
                ->addRule('required');

            $actionElName = $id.'_action';
            $this->form->addAdvRadio($actionElName)
                ->setLabel('Action')
                ->loadOptions([
                    'access' => ___('Add only Access Records'),
                    'payment' => ___('Add Invoice and Payment/Access Manually')
                ]);

            $form = $this->form;
            $fs = $this->form->addFieldset(null, ['id' => 'action-access']);

            $accessTypeElName = $id.'_access_type';
            $fs->addAdvRadio($accessTypeElName)
                ->setLabel(___('Access Period'))
                ->loadOptions([
                    'exact' => ___('Specify Exact Dates'),
                    'period' => ___('Extend Existing Subscription Period')
                ]);
            $dates = $fs->addGroup()
                ->setLabel(___('Start and Expiration Dates'))
                ->setId('period-exact');
            $dates->setSeparator(' ');
            $dates->addDate($id.'_begine_date');
            $dates->addDate($id.'_expire_date');
            $dates->addRule('callback', ___('Please specify access dates range'), function ($v) use ($form, $id) {
                $vars = $form->getValue();
                return ($v["{$id}_begine_date"] && $v["{$id}_expire_date"]) ||
                    $vars["{$id}_action"] != 'access' ||
                    $vars["{$id}_access_type"] != 'exact';
            });

            $fs->addPeriod($id.'_period')
                ->setLabel(___('Period'))
                ->setId('period-rules')
                ->addRule('callback', ___('This field is requred'), function ($v) use ($form, $id) {
                    $vars = $form->getValue();
                    return $v ||
                        $vars["{$id}_action"] != 'access' ||
                        $vars["{$id}_access_type"] != 'period';
                });

            $this->form->addScript()
                ->setScript(<<<CUT
jQuery(function(){
    jQuery('#period-rules-u').change(function(){
        jQuery('#period-rules-c').toggle(jQuery(this).val() != "lifetime");
    }).change();
    jQuery('[name=$accessTypeElName]').change(function(){
        jQuery('#row-period-exact').toggle(jQuery('[name=$accessTypeElName]:checked').val() == 'exact');
        jQuery('#row-period-rules').toggle(jQuery('[name=$accessTypeElName]:checked').val() == 'period');
    }).change();
    jQuery('[name=$actionElName]').change(function(){
        jQuery('#action-access').toggle(jQuery('[name=$actionElName]:checked').val() == 'access');
        jQuery('#action-payment').toggle(jQuery('[name=$actionElName]:checked').val() == 'payment');
    }).change();
})
CUT
                );

            $fs = $this->form->addFieldset(null, ['id' => 'action-payment']);

            $fs->addSelect($id.'_paysys_id')
              ->setLabel(___("Payment System"))
              ->loadOptions($this->grid->getDi()->paysystemList->getOptions());
            $fs->addText($id.'_amount')
                ->setlabel(___('Amount'));
            $fs->addDate($id.'_dattm')
                ->setLabel(___("Date Of Transaction"));

            $this->form->addText($id.'_comment', ['class'=>'am-el-wide'])
                ->setLabel(___("Comment\nfor admin reference"));

            $this->form->addDataSource(new HTML_QuickForm2_DataSource_Array([
                $accessTypeElName => 'exact',
                $id.'_dattm' => $this->grid->getDi()->sqlDate,
                $actionElName => 'access'
            ]));
            $this->form->addSaveButton(___('Mass Subscribe'));

            $this->form->addScript()
                ->setScript(<<<CUT
jQuery(function(){
    jQuery('#mass-subscribe').submit(function(){
        if (jQuery(this).valid()) {
            jQuery(this).find('[type=submit]').prop('disabled', true);
        }
    });
});
CUT
                );

        }
        return $this->form;
    }

    public function renderConfirmationForm($btn = null, $addHtml = null)
    {
        $this->getForm();
        $vars = $this->grid->getCompleteRequest()->toArray();
        $vars[$this->grid->getId() . '_confirm'] = 'yes';
        foreach ($vars as $k => $v)
            if ($this->form->getElementsByName($k))
                unset($vars[$k]);
        foreach(Am_Html::getArrayOfInputHiddens($vars) as $k => $v)
            $this->form->addHidden($k)->setvalue($v);

        $url_yes = $this->grid->makeUrl(null);
        $this->form->setAction($url_yes);
        echo $this->renderTitle();
        echo (string)$this->form;
    }

    public function run()
    {
        if (!$this->getForm()->validate()) {
            echo $this->renderConfirmationForm();
        } else {
            $prefix = $this->grid->getId().'_';
            foreach ($this->getForm()->getValue() as $k => $v) {
                if (strpos($k, $prefix)===0)
                    $this->_vars[substr($k, strlen($prefix))] = $v;
            }
            // disable emailing
            Am_Di::getInstance()->setService('mailTransport', new Am_Mail_Transport_Null);
            return parent::run();
        }
    }
}

class Am_Grid_Action_Group_AddUserNote extends Am_Grid_Action_Group_Abstract
{
    protected $needConfirmation = true;
    protected $form;
    protected $_vars;

    public function __construct()
    {
        parent::__construct('add_user_note', ___('Add User Note'));
        $this->setTarget('_top');
    }

    public function handleRecord($id, $record)
    {
        $di = $this->grid->getDi();
        $note = $di->userNoteRecord;

        $note->dattm = $di->sqlDateTime;
        $note->user_id = $record->user_id;
        $note->admin_id = $di->authAdmin->getUserId();
        $note->attachments = $note->serializeIds($this->_vars['attachments']);
        $note->content = $this->_vars['content'];
        $note->save();
    }

    public function getForm()
    {
        if (!$this->form) {
            $id = $this->grid->getId();
            $this->form = new Am_Form_Admin($this->getId());

            $this->form->addTextarea("{$id}_content", ['rows' => 10, 'class' => "am-no-label am-el-wide"])
                ->setLabel(___('Message'));

            $this->form->addUpload("{$id}_attachments", ['multiple' => 1], ['prefix' => 'user_note'])
                ->setJsOptions('{fileBrowser:false}')
                ->setLabel(___('Attach File'));

            $this->form->addSaveButton();
        }
        return $this->form;
    }

    public function renderConfirmationForm($btn = null, $addHtml = null)
    {
        $this->getForm();
        $vars = $this->grid->getCompleteRequest()->toArray();
        $vars[$this->grid->getId() . '_confirm'] = 'yes';
        foreach ($vars as $k => $v) {
            if ($this->form->getElementsByName($k)) {
                unset($vars[$k]);
            }
        }
        foreach(Am_Html::getArrayOfInputHiddens($vars) as $k => $v) {
            $this->form->addHidden($k)->setValue($v);
        }

        $url_yes = $this->grid->makeUrl(null);
        $this->form->setAction($url_yes);
        echo $this->renderTitle();
        echo (string)$this->form;
    }

    public function run()
    {
        if (!$this->getForm()->validate()) {
            echo $this->renderConfirmationForm();
        } else {
            $prefix = $this->grid->getId().'_';
            foreach ($this->getForm()->getValue() as $k => $v) {
                if (strpos($k, $prefix)===0)
                    $this->_vars[substr($k, strlen($prefix))] = $v;
            }
            return parent::run();
        }
    }
}

class Am_Grid_Action_Merge extends Am_Grid_Action_Abstract
{
    protected $title = "Merge %s";
    protected $privilege = 'merge';

    function run()
    {
        $form = new Am_Form_Admin('form-grid-merge');
        $form->setAttribute('name', 'merge');

        $user = $this->grid->getRecord();

        $form->addStatic()
            ->setContent(sprintf('<div>%s</div>', Am_Html::escape($user->login)))
            ->setLabel(___("Username of Source User\nmove information from this user to target user, this user will be deleted"));

        $login = $form->addText('login');
        $login->setId('login')
            ->setLabel(___("Target User\nmove information to"));
        $login->addRule('callback', ___('Can not find user with such username'), [$this, 'checkUser']);
        $login->addRule('callback', ___('You can not merge user with itself'), [$this, 'checkIdenticalUser']);

        $script = <<<CUT
        jQuery("input#login").autocomplete({
                minLength: 2,
                source: amUrl("/admin-users/autocomplete")
        });
CUT;

        $form->addStatic('', ['class' => 'am-no-label am-row-highlight'])
            ->setContent(
                 ___("WARNING! Once [Merge] button clicked, all invoices, payments, logs ".
                "and other information regarding 'Source User' will be moved ".
                "to the 'Target User' account. 'Source User' account will be deleted. ".
                "There is no way to undo this operation!")
        );

        $form->addScript('script')->setScript($script);

        foreach ($this->grid->getVariablesList() as $k) {
            $form->addHidden($this->grid->getId() . '_' . $k)->setValue($this->grid->getRequest()->get($k, ""));
        }

        $form->addSaveButton(___("Merge"));
        $form->setDataSources([$this->grid->getCompleteRequest()]);

        if ($form->isSubmitted() && $form->validate()) {
            $values = $form->getValue();
            $this->merge($this->grid->getRecord(), $this->getDi()->userTable->findFirstByLogin($values['login']));
            $this->grid->redirectBack();
        } else {
            echo $this->renderTitle();
            echo $form;
        }
    }

    public function checkUser($login)
    {
        $user = $this->getDi()->userTable->findFirstByLogin($login);
        return (boolean)$user;
    }

    public function checkIdenticalUser($login)
    {
        $user = $this->getDi()->userTable->findFirstByLogin($login);
        return $user->pk() != $this->grid->getRecord()->pk();
    }

    protected function merge(User $source, User $target)
    {
        //module should throw Exception in case of merge is not possible
        $event = new Am_Event(Am_Event::USER_BEFORE_MERGE, [
            'target' => $target,
            'source' => $source
        ]);
        $this->getDi()->hook->call(Am_Event::USER_BEFORE_MERGE, $event);

        $this->getDi()->db->query('UPDATE ?_access SET user_id=? WHERE user_id=?',
            $target->pk(), $source->pk());
        $this->getDi()->db->query('UPDATE ?_access_log SET user_id=? WHERE user_id=?',
            $target->pk(), $source->pk());
        $this->getDi()->db->query('UPDATE ?_invoice SET user_id=? WHERE user_id=?',
            $target->pk(), $source->pk());
        $this->getDi()->db->query('UPDATE ?_invoice_log SET user_id=? WHERE user_id=?',
            $target->pk(), $source->pk());
        $this->getDi()->db->query('UPDATE ?_invoice_payment SET user_id=? WHERE user_id=?',
            $target->pk(), $source->pk());
        $this->getDi()->db->query('UPDATE ?_invoice_refund SET user_id=? WHERE user_id=?',
            $target->pk(), $source->pk());
//        $this->getDi()->db->query('UPDATE ?_admin_log SET record_id=? WHERE record_id=?
//            AND tablename=?',
//            $target->pk(), $source->pk(), 'user');
        $this->getDi()->db->query('UPDATE ?_coupon_batch SET user_id=? WHERE user_id=?',
            $target->pk(), $source->pk());
        $this->getDi()->db->query('UPDATE ?_file_download SET user_id=? WHERE user_id=?',
            $target->pk(), $source->pk());
        $this->getDi()->db->query('UPDATE ?_upload SET user_id=? WHERE user_id=?',
            $target->pk(), $source->pk());

        $this->getDi()->db->query('UPDATE ?_user_note SET user_id=? WHERE user_id=?',
            $target->pk(), $source->pk());

        $this->getDi()->db->query('UPDATE ?_user_consent SET user_id=? WHERE user_id=?',
            $target->pk(), $source->pk());

        $event = new Am_Event(Am_Event::USER_MERGE, [
            'target' => $target,
            'source' => $source
        ]);
        $this->getDi()->hook->call(Am_Event::USER_MERGE, $event);

        $source->delete();
        $target->save();
        $target->checkSubscriptions(true);
        $this->log(
            ___('Merge with user %s <%s> (%s) [#%d]',
                $source->getName(),
                $source->email,
                $source->login,
                $source->pk()));
    }

    /**
     * @return Am_Di
     */
    protected function getDi()
    {
        return $this->grid->getDi();
    }
}

class Am_Grid_Action_LoginAs extends Am_Grid_Action_Url
{
    protected $privilege = 'login-as';
}

class Am_Grid_Filter_User extends Am_Grid_Filter_Abstract
{
    protected $title;

    public function __construct()
    {
        $this->title = '';
        parent::__construct();
    }

    public function getVariablesList()
    {
        $ret = parent::getVariablesList();
        $ret[] = 'search';
        $ret[] = 'search_load';
        return $ret;
    }

    protected function applyFilter()
    {
        // done in initFilter
    }

    protected function renderButton()
    {
        $title = Am_Html::escape(___('Advanced Search'));
        return parent::renderButton().
          "<a class='local am-advanced-search-toggle' style='margin:0 1em' href='javascript:;'>$title</a>";
    }

    public function  renderFilter()
    {
        $query = $this->grid->getDataSource();
        $conditions = $query->getConditions();
        $title = "";
        if (count($conditions)>1 || (count($conditions)==1 && !$conditions[0] instanceof Am_Query_User_Condition_Filter))
        {
            $selfUrl = $this->grid->escape($this->grid->makeUrl(null));
            if ($name = Am_Html::escape($query->getName())) {
                $deleteConfirm = json_encode(___("Delete Saved Search?"));
                $desc = "<strong>".___("Saved Search") . "</strong>: $name</b>";
                $root = Am_Di::getInstance()->url('');
                $id = $this->grid->getRequest()->getInt('search_load');
                $desc .= "&nbsp;<a href='$root/admin-users?_search_del=$id' class='link'
                    onclick='return confirm($deleteConfirm)' target=\"_top\">".___("Delete")."</a>";
            } else {
                $desc  = "<strong>".___("Filter")."</strong>: ";
                $desc .= $query->getDescription();
                $desc .= "&nbsp;<a href='javascript:;' class='link local am-save-advanced-search' style='white-space: nowrap'>".___("Save This Search")."</a>";
            }
            $title = "<div style='text-align:left;float:left;width:50%'>"
                . $desc
                . '</div>';
        }

        $filter = parent::renderFilter();
        $filter = preg_match('#(<div class="am-filter-wrap">)(.*)$#is', $filter, $matches);
        return $matches[1] . $title . $matches[2];
    }

    public function renderInputs()
    {
        return $this->renderInputText([
            'name' => 'filter',
            'size' => 30,
            'placeholder' => ___("Login/Name/Email/Invoice/Receipt/IP")]);
    }

    public function initFilter(Am_Grid_ReadOnly $grid)
    {
        parent::initFilter($grid);
        $query = $grid->getDataSource();
        $query->setPrefix('_u_search');
        /* @var $query Am_Query_User */
        if ($id = $grid->getCompleteRequest()->getInt('_search_del')){
            $query->deleteSaved($id);
            Am_Mvc_Response::redirectLocation($grid->getDi()->url('admin-users', false));
            exit();
        } elseif ($id = $grid->getRequest()->getInt('search_load')){
            $query->load($id);
        } elseif (is_string($this->vars['filter']) && $this->vars['filter']){
            $cond = new Am_Query_User_Condition_Filter();
            $cond->setFromRequest(['filter' => ['val' => trim($this->vars['filter'])]]);

            $query->add(Am_Di::getInstance()->hook->filter($cond, Am_Event::ADMIN_USERS_FILTER_INIT, [
                'query' => $query,
                'filter' => trim($this->vars['filter']),
            ]));
        } else {
            $query->setFromRequest($grid->getCompleteRequest());
        }
    }

    public function isFiltered()
    {
        return (bool)$this->grid->getDataSource()->getConditions();
    }

    public function renderStatic()
    {
        $grid = $this->grid;
        $hidden = Am_Html::renderArrayAsInputHiddens($grid->getFilter()->getAllButFilterVars());
        $f = str_replace('id="advanced-search"', 'id="advanced-search" style="display:none"', $grid->getDataSource()->renderForm($hidden));
        return <<<CUT
<!-- start of advanced search box -->
$f
<!-- end of advanced search box -->
CUT;
    }
}

class Am_Grid_Filter_NotConfirmed extends Am_Grid_Filter_Abstract
{
    protected function applyFilter()
    {
        $_ = $this->grid->getDataSource()->_friendGetArray();

        $_ = $this->filter($_, $this->getParam('filter'));
        $this->grid->getDataSource()->_friendSetArray($_);

        list($fieldname, $desc) = $this->grid->getDataSource()->_friendGetOrder();
        if ($fieldname) {
            $this->grid->getDataSource()->setOrder($fieldname, $desc);
        }
    }

    function renderInputs()
    {
        return $this->renderInputText([
            'name' => 'filter',
            'placeholder' => ___('E-mail'),
        ]);
    }

    protected function filter($array, $filter)
    {
        if (!$filter) return $array;

        foreach ($array as $k => $field) {
            if (false === stripos($field->email, $filter)) {

                unset($array[$k]);
            }
        }

        return $array;
    }
}

class Am_Grid_Field_Decorator_Additional extends Am_Grid_Field_Decorator_Abstract
{
    /** @var Am_CustomField */
    protected $f;

    function __construct(Am_CustomField $f)
    {
        $this->f = $f;
    }

    function render(& $out, $obj, $controller)
    {
        $field = $this->f;
        $val = $field->valueFromTable($out);
        switch($field->getType()) {
            case 'date':
                $res = amDate($val);
                break;
            case 'select':
            case 'radio':
            case 'checkbox':
            case 'multi_select':
                $val = (array)$val;
                foreach ($val as $k=>$v)
                    $val[$k] = @$field->options[$v];
                $res = implode(", ", $val);
                break;
            default:
                $res = $val;
        }
        $out = $controller->renderTd($res);
    }
}

class AdminUsersController extends Am_Mvc_Controller_Grid
{
    use Am_PersonalData;

    public function checkAdminPermissions(Admin $admin)
    {
        return $admin->hasPermission('grid_u');
    }

    public function preDispatch()
    {
        parent::preDispatch();
        $this->setActiveMenu($this->getParam('_u_a')=='insert' ? 'users-insert' : 'users-browse');
    }

    function cardAction()
    {
        $user = $this->getDi()->userTable->load($this->getParam('id'));
        $totalPaid = $this->getDi()->db->selectCell(<<<CUT
            SELECT ROUND(SUM(amount/base_currency_multi),2)
                FROM ?_invoice_payment
                WHERE user_id=?
CUT
            , $user->pk());
        $totalRefund = $this->getDi()->db->selectCell(<<<CUT
            SELECT ROUND(SUM(amount/base_currency_multi),2)
                FROM ?_invoice_refund
                WHERE user_id=?
CUT
            , $user->pk());

        $this->view->assign('totalPaid', $totalPaid);
        $this->view->assign('totalRefund', $totalRefund);
        $this->view->assign('user', $user);
        $this->view->display('admin/user-card.phtml');
    }

    function dataAction()
    {
        $this->getDi()->authAdmin->getUser()->checkPermission('grid_u', 'browse');
        $user = $this->getDi()->userTable->load($this->_request->getInt('user_id'));
        $this->getResponse()->setHeader('Content-type', 'text/xml');
        $x = new XMLWriter();
        $x->openMemory();
        $x->setIndent(true);
        $x->startElement('user-data-items');
        $this->writeEl($x, $user->data()->getAll());
        $x->endElement();
        echo $x->flush();
    }

    function writeEl(XMLWriter $x, $val)
    {
        if (is_scalar($val)) {
            $x->text($val);
        } elseif(is_array($val)) {
            foreach ($val as $k => $v) {
                $x->startElement('item');
                $x->writeAttribute('name', $k);
                $this->writeEl($x, $v);
                $x->endElement();
            }
        }
    }

    public function disableAutoLockAction()
    {
        $this->getDi()->authAdmin->getUser()->checkPermission('grid_u', 'edit');

        if (!$user = $this->getDi()->userTable->load($this->getParam('id'))) {
            throw new Am_Exception_InputError;
        }

        $user->updateQuick('disable_lock_until', sqlTime('+1 day'));

        $this->_response->redirectLocation($this->getParam('b', $this->view->userUrl($user->pk())));
    }

    public function deleteAction()
    {
        $this->getDi()->authAdmin->getUser()->checkPermission('grid_u', 'delete');

        if ($this->getRequest()->isPost()) {
            $user = $this->getDi()->userTable->load($this->getParam('id'));
            $user->delete();
            $this->getDi()->adminLogTable->log(
                sprintf('%s (%s, %s, %s)', ___('Delete User'), $user->getName(), $user->login, $user->email),
                'grid_u', $user->pk());
            $this->_response->redirectLocation($this->getDi()->url('admin-users',null,false));
        } else {
            $this->view->user = $this->getDi()->userTable->load($this->getParam('id'));
            $this->view->display('admin/user-delete.phtml');
        }
    }

    public function getNotConfirmedCount()
    {
        return $this->getDi()->db->selectCell("SELECT COUNT(*) FROM ?_store
            WHERE name LIKE 'signup_record-%' AND CHAR_LENGTH(blob_value)>10 AND expires>?",
            $this->getDi()->sqlDateTime);
    }

    public function notConfirmedAction()
    {
        $arr = [];
        foreach ($this->getDi()->db->select("SELECT `blob_value`, expires, name FROM ?_store
            WHERE name LIKE 'signup_record-%' AND CHAR_LENGTH(blob_value)>10 AND expires>?",
            $this->getDi()->sqlDateTime) as $row)
        {
            $v = unserialize($row['blob_value']);
            $rec = [];
            foreach ($v['values'] as $page)
            {
                $rec = array_merge($rec, $page);
            }
            $rec['code'] = str_replace('signup_record-', '', $row['name']);
            $rec['expires'] = $row['expires'];
            $rec['url'] = $v['opaque']['ConfirmUrl'];
            $link = Am_Html::escape($v['opaque']['ConfirmUrl']);
            $rec['link'] = ___('Give this link to customer if e-mail confirmation has not been received:') .
                '<br /><br /><pre>' . $link . '</pre><br />';
            if (empty($rec['login'])) $rec['login'] = null;
            if (empty($rec['name_f'])) $rec['name_f'] = null;
            if (empty($rec['name_l'])) $rec['name_l'] = null;
            $arr[] = (object)$rec;
        }

        $ds = new Am_Grid_DataSource_Array($arr);
        $grid = new Am_Grid_Editable('_usernc', ___("Not Confirmed Users"),
            $ds, $this->_request, $this->view, $this->getDi());
        $grid->setPermissionId('grid_u');
        $grid->addField('login', ___('Username'));
        $grid->addField('email', ___('E-Mail'));
        $grid->addField('name_f', ___('First Name'));
        $grid->addField('name_l', ___('Last Name'));
        $grid->addField('expires', ___('Expires'))
            ->setRenderFunction(function($r, $fn, $g, $fo) {
                $_ = (amstrtotime($r->$fn) - $this->getDi()->time)/60;
                if ($_ > 60) {
                    $left = ___('in %s hour(s)', floor($_/60));
                } else {
                    $left = ___('in %s minute(s)', floor($_));
                }

                return $g->renderTd(sprintf('%s <span style="margin-left:1em; opacity:.5;">%s</span>',
                    amDatetime($r->$fn), $left), false);
            });
        $grid->addField(new Am_Grid_Field_Expandable('link', ___('Link')))->setEscape(false);
        $grid->actionsClear();
        $grid->actionAdd(new Am_Grid_Action_Export)
            ->addField(new Am_Grid_Field('login', ___('Username')))
            ->addField(new Am_Grid_Field('email', ___('E-Mail')))
            ->addField(new Am_Grid_Field('name_f', ___('First Name')))
            ->addField(new Am_Grid_Field('name_l', ___('Last Name')))
            ->addField(new Am_Grid_Field('expires', ___('Expires')))
            ->addField(new Am_Grid_Field('url', ___('Confirmation Link')));
        $grid->actionAdd(new Am_Grid_Action_Group_ResendConfirmationLink(null, ___('Resend Confirmation Link')));

        $grid->setFilter(new Am_Grid_Filter_NotConfirmed);
        $this->view->content = $grid->runWithLayout('admin/layout.phtml');
    }

    public function autocompleteAction()
    {
        if (!$term = trim($this->getParam('term'))) {
            return null;
        }

        $id_cond = '';
        if (is_numeric($term)) {
            $id = (int)$term;
            $id_cond = " OR (t.user_id={$id})";
        }

        $term = "%{$term}%";

        $q = new Am_Query($this->getDi()->userTable);
        $q->addWhere(
        "(t.login LIKE ?) OR (t.email LIKE ?) OR (t.name_f LIKE ?) OR (t.name_l LIKE ?) "
        . "OR (CONCAT(t.name_f, ' ', t.name_l) LIKE ?) OR (CONCAT(t.name_l, ' ', t.name_f) LIKE ?)"
        . $id_cond,
            $term, $term, $term, $term, $term, $term);
        $this->getDi()->hook->call(Am_Event::ADMIN_USERS_AUTOCOMPLETE, [
            'query' => $q,
            'term' => $term
        ]);
        $qq = $q->query(0, 10);
        $ret = [];
        while ($r = $this->getDi()->db->fetchRow($qq)) {
            $ret[] = [
                'label' => sprintf('%s / "%s" <%s>', $r['login'], $r['name_f'] . ' ' . $r['name_l'], $r['email']),
                'value' => $r['login'],
                'url' => $this->view->userUrl($r['user_id']),
                'user_id' => $r['user_id']
            ];
        }
        if ($q->getFoundRows() > 10) {
            $ret[] = [
                'label' => sprintf("... %d more rows found ...", $q->getFoundRows() - 10),
                'value' => null,
                'url' => $this->url('admin-users', ['_u_filter' =>  $this->getParam('term')], false),
                'user_id' => null
            ];
        }
        $this->_response->ajaxResponse($ret);
    }

    public function indexAction()
    {
        if (in_array($this->grid->getCurrentAction(), ['edit','insert']))
            $this->layout = 'admin/user-layout.phtml';
        parent::indexAction();
    }

    public function createGrid()
    {
        $ds = new Am_Query_User;
        $datetime = $this->getDi()->sqlDate;
        $ds->addField("CONCAT(u.name_f, ' ', u.name_l)", '_name')
          ->addField('(SELECT COUNT(p.invoice_payment_id) FROM ?_invoice_payment p WHERE p.user_id = u.user_id)',  '_payments_count')
          ->addField('(SELECT ROUND(SUM((p.amount - IFNULL(p.refund_amount, 0))/p.base_currency_multi), 2)
              FROM ?_invoice_payment p WHERE p.user_id = u.user_id)', '_payments_sum')
            ->addField('(SELECT COUNT(r.invoice_refund_id) FROM ?_invoice_refund r WHERE r.user_id = u.user_id)',  '_refunds_count')
            ->addField('(SELECT ROUND(SUM(r.amount/r.base_currency_multi), 2)
              FROM ?_invoice_refund r WHERE r.user_id = u.user_id)', '_refunds_sum')
          ->addField("(SELECT MAX(expire_date) FROM ?_access ac WHERE ac.user_id = u.user_id)", '_expire')
          ->addField("(SELECT GROUP_CONCAT(title SEPARATOR ', ') FROM ?_access ac
              LEFT JOIN ?_product p USING (product_id)
              WHERE ac.user_id = u.user_id AND ac.begin_date<='$datetime' AND ac.expire_date>='$datetime')", '_products')
          ->addField("(SELECT GROUP_CONCAT(title SEPARATOR ', ') FROM ?_user_user_group uug
              LEFT JOIN ?_user_group ug USING (user_group_id)
              WHERE uug.user_id = u.user_id)", '_ugroup');
        $ds->setOrder("login");
        $grid = new Am_Grid_Editable('_u', ___("Browse Users"), $ds, $this->_request, $this->view);
        $grid->setRecordTitle([$this, 'getRecordTitle']);
        $_ = $grid->addField('login', ___('Username'));
        if ($this->getDi()->authAdmin->getUser()->hasPermission('grid_u', 'edit')) {
            $_->setRenderFunction([$this, 'renderLogin']);
        }
        $grid->addField('_name', ___('Name'));
        $_ = $grid->addField('email', ___('E-Mail Address'));
        if ($this->getDi()->authAdmin->getUser()->hasPermission(Am_Auth_Admin::PERM_EMAIL)) {
            $_->setRenderFunction([$this, 'renderEmail']);
        }
        $grid->addField('_payments_sum', ___('Payments'), true, 'right', [$this, 'renderPayments']);
        $grid->addField('status', ___('Status'))->setRenderFunction([$this, 'renderStatus']);
        $grid->actionAdd($this->createActionExport());
        $grid->actionGet('edit')->setTarget('_top')->showFormAfterSave(true);
        $grid->actionGet('insert')->setTarget('_top')->showFormAfterSave(true);
        $grid->setForm([$this, 'createForm']);
        $grid->addCallback(Am_Grid_Editable::CB_BEFORE_SAVE, [$this, 'beforeSave']);
        $grid->addCallback(Am_Grid_Editable::CB_AFTER_SAVE, [$this, 'afterSave']);
        $grid->addCallback(Am_Grid_Editable::CB_VALUES_TO_FORM, [$this, 'valuesToForm']);
        $grid->addCallback(Am_Grid_ReadOnly::CB_TR_ATTRIBS, [$this, 'getTrAttribs']);

        $grid->actionAdd($this->createActionCustomize());
        $grid->actionAdd(new Am_Grid_Action_Group_Callback('lock', ___("Lock"), [$this, 'lockUser']));
        $grid->actionAdd(new Am_Grid_Action_Group_Callback('unlock', ___("Unlock"), [$this, 'unlockUser']));
        $grid->actionAdd(new Am_Grid_Action_Group_Callback('approve', ___("Approve"), [$this, 'approveUser']));
        $grid->actionAdd(new Am_Grid_Action_Group_Callback('unsubscribe', ___("Unsubscribe from All Emails"), [$this, 'unsubscribeUser']));
        $grid->actionAdd(new Am_Grid_Action_Group_EmailUsers());
        $grid->actionAdd(new Am_Grid_Action_Group_MassSubscribe());
        $grid->actionAdd(new Am_Grid_Action_Group_PasswordConfirmedDelete());
        $grid->actionDelete('delete');
        $grid->actionAdd(new Am_Grid_Action_LoginAs('login', ___('Login as User'),
            '__ROOT__/admin-users/login-as?id=__ID__'))->setTarget('_blank');
        $grid->actionAdd(new Am_Grid_Action_Delete());
        $grid->actionAdd(new Am_Grid_Action_Merge());
        $grid->actionAdd(new Am_Grid_Action_Group_UserAssignGroup);
        $grid->actionAdd(new Am_Grid_Action_Group_UserRemoveGroup);
        $grid->actionAdd(new Am_Grid_Action_Group_AddUserNote);
        if(in_array($this->getDi()->config->get('account-removal-method'), ['anonymize', 'delete-request']))
        {
            $grid->actionAdd(new Am_Grid_Action_Group_Callback('group-anonymize', ___("Anonymize Personal Data"), function($id, User $user){
                $errors = $this->doAnonymize($user);
                if(!empty($errors)){
                    echo "<br/>".___("Unable to Delete Personal Data for user: %s", $user->login);
                    echo "<ul>".implode("", array_map(function($v){return "<li>".$v."</li>";}, $errors))."</ul>";
                }
            }));
            $grid->actionAdd(new Am_Grid_Action_Anonymize());
        }

        $nc_count = $this->getDi()->cache->load('getNotConfirmedCount');
        if ($nc_count === false)
        {
            $nc_count = $this->getNotConfirmedCount();
            $this->getDi()->cache->save($nc_count, 'getNotConfirmedCount', [], 60);
        }
        if ($nc_count)
        {
            $grid->actionAdd(new Am_Grid_Action_Url('not-confirmed',
                    ___("Not Confirmed Users") . " ($nc_count)",
                    $this->getDi()->url('admin-users/not-confirmed', false)))
                ->setType(Am_Grid_Action_Abstract::NORECORD)
                ->setCssClass('link')
                ->setTarget('_top');
        }

        $grid->actionAdd(new Am_Grid_Action_Url('import', ___('Import Users'), 'admin-import'))
            ->setType(Am_Grid_Action_Abstract::NORECORD)
            ->setTarget('_top')
            ->setCssClass('link')
            ->setPrivilegeId('insert');

        $grid->actionAdd(new Am_Grid_Action_Url('categories', ___('User Groups'),
            '__ROOT__/admin-user-groups'))
            ->setType(Am_Grid_Action_Abstract::NORECORD)
            ->setTarget('_top')
            ->setCssClass('link')
            ->setPrivilegeId('edit');

        $grid->setFilter(new Am_Grid_Filter_User());
        $grid->setEventId('gridUser');
        return $grid;
    }

    public function getRecordTitle(User $user = null)
    {
        return $user ? sprintf('%s - %s', ___('User'), implode(", ", array_map(['Am_Html', 'escape'], array_filter([$user->getName(), $user->login, $user->email])))) : ___('User');
    }

    public function getTrAttribs(& $ret, $record)
    {
        if ($record->isLocked()
            || (!$record->isApproved()))
        {
            $ret['class'] = isset($ret['class']) ? $ret['class'] . ' disabled' : 'disabled';
        }
    }

    protected function createActionCustomize()
    {
        $stateTitleField = new Am_Grid_Field('state_title', ___('State Title'), false);
        $stateTitleField->setGetFunction([$this, 'getStateTitle']);

        $countryTitleField = new Am_Grid_Field('country_title', ___('Country Title'), false);
        $countryTitleField->setGetFunction([$this, 'getCountryTitle']);

        $lastSigninInfoField = new Am_Grid_Field('last_signin', ___('Last Signin Info'), false);
        $lastSigninInfoField->setGetFunction([$this, 'getLastSigninInfo']);

        $gravatarField = new Am_Grid_Field('gravatar', ___('Gravatar'), false, null, [$this, 'renderGravatar'], '1%');

        $expireField = new Am_Grid_Field_Date('_expire', ___('Expire'));
        $expireField->setFormatDate();

        $productsField = new Am_Grid_Field_Expandable('_products', ___('Active Subscriptions'));
        $productsField->setPlaceholder(Am_Grid_Field_Expandable::PLACEHOLDER_SELF_TRUNCATE_END)
            ->setMaxLength(50)
            ->setGetFunction(function($r, $g, $f) {
                return strip_tags($r->$f);
            });

        $userGroupField = new Am_Grid_Field('_ugroup', ___('User Groups'), false);
        $isAffField = new Am_Grid_Field_Enum('is_affiliate', ___('Is Affiliate?'));
        $isAffField->setTranslations([0 => ___('No'), 1 => ___('Yes'), 2 => ___('Yes')]);

        $refundsField = new Am_Grid_Field('_refunds_sum', ___('Refunds'), true, 'right', [$this, 'renderRefunds']);
        $mobileField = (new Am_Grid_Field('mobile', ___('Mobile Number')))->setGetFunction(fn(User $user)=> $user->getMobile());

        $action = new Am_Grid_Action_Customize();
        $action->addField(new Am_Grid_Field('user_id', '#', true, '', null, '1%'))
            ->addField(new Am_Grid_Field('name_f', ___('First Name')))
            ->addField(new Am_Grid_Field('name_l', ___('Last Name')))
            ->addField(new Am_Grid_Field('street', ___('Street')))
            ->addField(new Am_Grid_Field('street2', ___('Street (Second Line)')))
            ->addField(new Am_Grid_Field('city', ___('City')))
            ->addField(new Am_Grid_Field('state', ___('State')))
            ->addField($stateTitleField)
            ->addField(new Am_Grid_Field('zip', ___('ZIP Code')))
            ->addField(new Am_Grid_Field('country', ___('Country')))
            ->addField($countryTitleField)
            ->addField(new Am_Grid_Field('phone', ___('Phone')))
            ->addField($mobileField)
            ->addField(new Am_Grid_Field_Date('added', ___('Added')))
            ->addField(new Am_Grid_Field('remote_addr', ___('Registration IP')))
            ->addField($productsField)
            ->addField($userGroupField)
            ->addField(new Am_Grid_Field('status', ___('Status')))
            ->addField(new Am_Grid_Field('unsubscribed', ___('Unsubscribed')))
            ->addField(new Am_Grid_Field('lang', ___('Language')))
            ->addField(new Am_Grid_Field('is_locked', ___('Is Locked')))
            ->addField(new Am_Grid_Field('comment', ___('Comment')))
            ->addField(new Am_Grid_Field('aff_id', ___('Affiliate Id#')))
            ->addField($isAffField)
            ->addField($expireField)
            ->addField($lastSigninInfoField)
            ->addField(new Am_Grid_Field_Date('last_login', ___('Last Signin (Date/Time)')))
            ->addField($gravatarField)
            ->addField($refundsField);
            //Additional Fields
        foreach ($this->getDi()->userTable->customFields()->getAll() as $field) {
            if (isset($field->from_config) && $field->from_config) {
                $f = $field->sql ?
                    new Am_Grid_Field($field->name, $field->title) :
                    new Am_Grid_Field_Data($field->name, $field->title, false);
                $f->addDecorator(new Am_Grid_Field_Decorator_Additional($field));
                $f->setRenderFunction([$this, 'renderAdditional']);
                $action->addField($f);
            }
        }

        $action->setSortByOptions([
            'login' => ___('Username'),
            'email' => ___('E-Mail'),
            'name_f' => ___('First Name'),
            'name_l' => ___('Last Name'),
            [
                'label' => ___('Full Name'),
                'fields' => ['name_l', 'name_f'],
            ],
            'added' => ___('Registration Date'),
            'user_id' => ___('User ID'),
        ]);

        return $action;
    }

    function renderAdditional($record, $fieldName, $controller, $field)
    {
        //@see Am_Grid_Field_Decorator_Additional
        return $field->get($record, $controller, $fieldName);
    }

    function renderGravatar($record, $fieldName, $controller, $field)
    {
        return sprintf('<td><div style="margin:0 auto; width:40px; height:40px; border-radius:50%%; overflow: hidden;box-shadow: 0 2px 4px #d0cfce;"><img src="%s" width="40" height="40" /></div></td>',
            '//www.gravatar.com/avatar/' . md5(strtolower(trim($record->email))) . '?s=40&d=mm');
    }

    function renderEmail($record, $fieldName, $grid, $field)
    {
        return $grid->renderTd(sprintf('<a class="link" target="_top" href="%s">%s</a>',
            $this->getDi()->url("admin-email", [
                    'search-type'=>'advanced',
                    'search'=>json_encode(['member_id_filter' => ['val' => $record->pk()]])
                ]
            ),
            $grid->escape($record->email)), false);
    }

    protected function createActionExport()
    {
        $stateTitleField = new Am_Grid_Field('state_title', ___('State Title'));
        $stateTitleField->setGetFunction([$this, 'getStateTitle']);

        $countryTitleField = new Am_Grid_Field('country_title', ___('Country Title'));
        $countryTitleField->setGetFunction([$this, 'getCountryTitle']);

        $lastSigninInfoField = new Am_Grid_Field('last_signin', ___('Last Signin Info'));
        $lastSigninInfoField->setGetFunction([$this, 'getLastSigninInfo']);

        $mobileField = new Am_Grid_Field('mobile', ___('Mobile Phone'));
        $mobileField->setGetFunction(fn(User $user)=> $user->getMobile());

        $action = new Am_Grid_Action_Export();
        $action->addField(new Am_Grid_Field('user_id', ___('User Id')))
                ->addField(new Am_Grid_Field('login', ___('Username')))
                ->addField(new Am_Grid_Field('email', ___('Email')))
                ->addField(new Am_Grid_Field('pass', ___('Password Hash')))
                ->addField(new Am_Grid_Field('name_f', ___('First Name')))
                ->addField(new Am_Grid_Field('name_l', ___('Last Name')))
                ->addField(new Am_Grid_Field('street', ___('Street')))
                ->addField(new Am_Grid_Field('street2', ___('Street (Second Line)')))
                ->addField(new Am_Grid_Field('city', ___('City')))
                ->addField(new Am_Grid_Field('state', ___('State')))
                ->addField($stateTitleField)
                ->addField(new Am_Grid_Field('zip', ___('ZIP Code')))
                ->addField(new Am_Grid_Field('country', ___('Country')))
                ->addField($countryTitleField)
                ->addField(new Am_Grid_Field('phone', ___('Phone')))
                ->addField($mobileField)
                ->addField(new Am_Grid_Field('added', ___('Added')))
                ->addField(new Am_Grid_Field('remote_addr', ___('Registration IP')))
                ->addField(new Am_Grid_Field('status', ___('Status')))
                ->addField(new Am_Grid_Field('_ugroup', ___('User Groups')))
                ->addField(new Am_Grid_Field('_products', ___('Active Subscriptions')))
                ->addField(new Am_Grid_Field('unsubscribed', ___('Unsubscribed')))
                ->addField(new Am_Grid_Field('lang', ___('Language')))
                ->addField(new Am_Grid_Field('is_locked', ___('Is Locked')))
                ->addField(new Am_Grid_Field('comment', ___('Comment')))
                ->addField(new Am_Grid_Field('aff_id', ___('Affiliate Id#')))
                ->addField(new Am_Grid_Field('_payments_sum', ___('Payments (amount of all payments made by user minus refunds)')))
                ->addField(new Am_Grid_Field('_expire', ___('Expire (maximum expiration date)')))
                ->addField($lastSigninInfoField)
                ->addField(new Am_Grid_Field_Date('last_login', ___('Last Signin (Date/Time)')));

        //Additional Fields
        foreach ($this->getDi()->userTable->customFields()->getAll() as $field) {
            if (isset($field->from_config) && $field->from_config) {
                if ($field->sql) {
                    if (in_array($field->type, ['multi_select','checkbox', 'radio'])){
                        $f = new Am_Grid_Field($field->name, $field->title . ' (Value)');
                        $f->setGetFunction([$this,'getMultiSelect']);
                        $action->addField($f);

                        $op = $field->options;
                        $fn = $field->name;
                        $f = new Am_Grid_Field($field->name . '_label', $field->title . ' (Label)');
                        $f->setGetFunction(function($obj, $controller, $field=null) use ($op, $fn){
                            return implode(',', array_map(function($el) use ($op) {
                                return isset($op[$el]) ? $op[$el] : $el;
                            }, (array)$obj->{$fn}));
                        });
                        $action->addField($f);
                    } elseif ($field->type == 'select') {
                        $f = new Am_Grid_Field($field->name, $field->title . ' (Value)');
                        $action->addField($f);

                        $op = $field->options;
                        $fn = $field->name;
                        $f = new Am_Grid_Field($field->name . '_label', $field->title . ' (Label)');
                        $f->setGetFunction(function($obj, $controller, $field=null) use ($op, $fn){
                            return isset($op[$obj->{$fn}]) ? $op[$obj->{$fn}] : $obj->{$fn};
                        });
                        $action->addField($f);
                    } else {
                        $action->addField(new Am_Grid_Field($field->name, $field->title));
                    }
                } else {
                    if (in_array($field->type, ['multi_select', 'checkbox'])){
                        //we use trailing __blob to distinguish multi select fields from data table
                        $mfield = new Am_Grid_Field($field->name . '__blob', $field->title . ' (Value)');
                        $mfield->setGetFunction([$this,'getMultiSelect']);
                        $action->addField($mfield);

                        $op = $field->options;
                        $fn = $field->name . '__blob';
                        $f = new Am_Grid_Field($field->name . '_label__blob', $field->title . ' (Label)');
                        $f->setGetFunction(function($obj, $controller, $field=null) use ($op, $fn){
                            return implode(',', array_map(function($el) use ($op) {
                                return isset($op[$el]) ? $op[$el] : $el;
                            }, (array)@unserialize($obj->{$fn})));
                        });
                        $action->addField($f);
                    } elseif (in_array($field->type, ['select', 'radio'])) {
                        //we use trailing __blob to distinguish multi select fields from data table
                        $mfield = new Am_Grid_Field($field->name . '__', $field->title . ' (Value)');
                        $action->addField($mfield);

                        $op = $field->options;
                        $fn = $field->name . '__';
                        $f = new Am_Grid_Field($field->name . '_label__', $field->title . ' (Label)');
                        $f->setGetFunction(function($obj, $controller, $field=null) use ($op, $fn){
                            return isset($op[$obj->{$fn}]) ? $op[$obj->{$fn}] : $obj->{$fn};
                        });
                        $action->addField($f);
                    } else {
                        //we use trailing __ to distinguish fields from data table
                        $action->addField(new Am_Grid_Field($field->name . '__', $field->title));
                    }
                }
            }
        }

        $action->setGetDataSourceFunc([$this, 'getDS']);
        return $action;
    }

    function getStateTitle($obj, $controller, $field=null)
    {
        static $state=[];

        if(empty($state[$obj->country.$obj->state])){
            $state[$obj->country.$obj->state] = $this->getDi()->stateTable->getTitleByCode($obj->country, $obj->state);
        }

        return $state[$obj->country.$obj->state];
    }

    function getCountryTitle($obj, $controller, $field=null)
    {
        static $country=[];

        if(empty($country[$obj->country])){
            $country[$obj->country] = $this->getDi()->countryTable->getTitleByCode($obj->country);
        }

        return $country[$obj->country];


    }

    function getLastSigninInfo($obj, $controller, $field=null)
    {
        return $obj->last_login ? $obj->last_ip . ___(' at ') . amDatetime($obj->last_login) : ___('Never');
    }

    function getMultiSelect($obj, $controller, $field=null)
    {
        return implode(',', is_array($obj->{$field}) ? $obj->{$field} : (array)@unserialize($obj->{$field}));
    }

    public function getDS(Am_Query $ds, $fields)
    {
        $i = 0;
        //join only selected fields
        foreach ($fields as $field) {
            $fn = $field->getFieldName();
            if (substr($fn, -6) == '__blob') { //multi select field from data table
                $i++;
                $field_name = substr($fn, 0, strlen($fn)-6);
                $ds = $ds->leftJoin("?_data", "d$i", "u.user_id = d$i.id AND d$i.table='user' AND d$i.key='$field_name'")
                    ->addField("d$i.blob", $fn);
            }
            if (substr($fn, -2) == '__') { //field from data table
                $i++;
                $field_name = substr($fn, 0, strlen($fn)-2);
                $ds = $ds->leftJoin("?_data", "d$i", "u.user_id = d$i.id AND d$i.table='user' AND d$i.key='$field_name'")
                    ->addField("d$i.value", $fn);
            }
        }
        return $ds;
    }

    public function lockUser($id, User $user)
    {
        $user = $this->getDi()->userTable->load($id);
        $user->lock(true);
    }

    public function unlockUser($id, User $user)
    {
        $user = $this->getDi()->userTable->load($id);
        $user->lock(false);
    }

    public function subscribeUser($id, User $user)
    {
        $user = $this->getDi()->userTable->load($id);
        $user->unsubscribed = 0;
        $user->save();
    }

    public function unsubscribeUser($id, User $user)
    {
        $user = $this->getDi()->userTable->load($id);
        $user->unsubscribed = 1;
        $user->save();
    }

    public function approveUser($id, User $user)
    {
        $user = $this->getDi()->userTable->load($id);
        $user->is_approved = 1;
        $user->update();
    }

    function renderLogin($record, $fieldName, $grid, $field)
    {
        $icons = "";
        if ($record->isLocked())
            $icons .= $this->view->icon('user-locked', ___('User is locked'));
        if (!$record->isApproved())
            $icons .= $this->view->icon('user-not-approved', ___('User is not approved'));
        if ($icons) $icons = '<div style="float: right;">' . $icons . '</div>';

        return $this->renderTd(sprintf('%s<a class="link" target="_top" href="%s">%s</a>',
            $icons,
            $grid->escape($grid->getActionUrl('edit', $record->user_id)),
            $grid->escape($record->login)), false);
    }

    function renderStatus(User $record)
    {
        $text = "";
        switch ($record->status)
        {
            case User::STATUS_PENDING:
                if ($record->_payments_count) {
                    if ((float)$record->_payments_sum > 0) {
                        $text = '<span class="user-status user-status-future">' . ___('Future') . '</span>';
                    } else {
                        $text = '<span class="user-status user-status-refunded">' . ___('Refunded') . '</span>';
                    }
                } else {
                    $text = '<span class="user-status user-status-pending">' . ___('Pending') . '</span>';
                }
                break;
            case User::STATUS_ACTIVE:
                $text = '<span class="user-status user-status-active">' . ___('Active') . '</span>';
                break;
            case User::STATUS_EXPIRED:
                $text = '<span class="user-status user-status-expired">' . ___('Expired') . '</span>';
                break;
        }
        return $this->renderTd($text, false);
    }

    function renderPayments(User $record)
    {
        if ($record->_payments_count)
        {
            $curr = new Am_Currency();
            $curr->setValue($record->_payments_sum);
            $text = $record->_payments_count . ' - ' . $curr->toString();
            $link = $this->getDi()->url("admin-user-payments/index/user_id/{$record->user_id}");
            $text = sprintf('<a class="link" target="_top" href="%s#payments">%s</a>', $link, $text);
        } else {
            $text = ___('Never');
        }

        return sprintf('<td style="text-align:right">%s</td>', $text);
    }

    function renderRefunds(User $record)
    {
        if ($record->_refunds_count)
        {
            $curr = new Am_Currency();
            $curr->setValue($record->_refunds_sum);
            $text = $record->_refunds_count . ' - ' . $curr->toString();
        } else {
            $text = ___('Never');
        }

        return sprintf('<td style="text-align:right">%s</td>', $text);
    }

    function createForm()
    {
        return new Am_Form_Admin_User($this->grid->getRecord());
    }

    function saveSearchAction()
    {
        $q = new Am_Query_User();
        $search = $this->_request->get('search');
        $q->unserialize($search['serialized']);
        if (!$q->getConditions())
            throw new Am_Exception_InputError("Wrong parameters passed: no conditions : " . htmlentities($this->_request->search['serialized']));
        if (!strlen($this->getParam('name')))
            throw new Am_Exception_InputError(___("No search name passed"));
        $name = $this->getParam('name');
        $id = $q->setName($name)->save();
        $this->_response->redirectLocation($this->getDi()->url('admin-users', ['_u_search_load'=>$id],false));
    }

    function valuesToForm(& $values, User $record)
    {
        $values['_groups'] = $record->getGroups();
        if (!$this->getDi()->config->get('manually_approve') && !isset($values['is_approved'])) {
            $values['is_approved'] = 1;
        }
    }

    function beforeSave(array &$values, User $record)
    {
        if (!empty($values['_pass'])) {
            $record->setPass($values['_pass']);
        }

        if (!$record->isLoaded()) {
            $record->is_approved = 1;
        }

        $record->setGroups(array_filter((array)@$values['_groups']));
    }

    function afterSave(array &$values, User $record)
    {
        if(($this->grid->getCurrentAction() == 'insert') && !empty($values['_registration_mail'])) {
            $record->sendRegistrationEmail();
        }

        //bind upload ids to current user
        $upload_ids = [];
        foreach ($this->getDi()->userTable->customFields()->getAll() as $f) {
            if (in_array($f->type, ['upload', 'multi_upload'])) {
                $upload_ids = array_merge($upload_ids,
                    (array)($f->sql ? $record->{$f->name} : $record->data()->get($f->name)));
            }
        }
        if ($upload_ids) {
            $this->getDi()->db->query(<<<CUT
                UPDATE ?_upload
                    SET user_id = ?
                    WHERE user_id IS NULL
                        AND admin_id = ?
                        AND upload_id IN (?a);
CUT
                , $record->pk(), $this->getDi()->authAdmin->getUserId(), $upload_ids);
        }
    }

    function loginAsAction()
    {
        if (!$this->getDi()->authAdmin->getUser()->hasPermission('grid_u', 'login-as'))
            throw new Am_Exception_AccessDenied();

        if (!$id = $this->getInt('id'))
            throw new Am_Exception_InputError("Empty or no id passed");

        $user = $this->getDi()->userTable->load($id);

        $this->getDi()->adminLogTable->log(
            sprintf('Login As User (%s)', $user->login),
            'grid_u',
            $user->pk(),
            $this->getDi()->authAdmin->getUserId()
        );

        $adapter = new Am_Auth_Adapter_User($user);
        $this->getDi()->auth->login($adapter, $this->getRequest()->getClientIp(), false);
        $this->_response->redirectLocation($this->getDi()->url('member', false));
    }

    function accessLogAction()
    {
        $c = new AdminLogsController($this->getRequest(), $this->getResponse(), $this->getInvokeArgs());
        $grid = $c->createAccess();
        $grid->removeField('member_login');
        $grid->getDataSource()->addWhere('t.user_id=?d', (int)$this->getParam('user_id'));
        $grid->runWithLayout('admin/user-layout.phtml');
    }

    function mailQueueAction()
    {
        $c = new AdminLogsController($this->getRequest(), $this->getResponse(), $this->getInvokeArgs());
        $grid = $c->createMailQueue();
        $user = $this->getDi()->userTable->load($this->getParam('user_id'));
        $grid->getDataSource()->addWhere('t.recipients = ? OR t.recipients LIKE ? OR t.recipients LIKE ? OR t.recipients LIKE ?', "{$user->email}", "%,{$user->email}", "{$user->email},%", "%,{$user->email},%");
        $grid->runWithLayout('admin/user-layout.phtml');
    }

    function notApprovedAction()
    {
        $this->_redirect('admin-users?_u_search[field-is_approved][val]=0');
    }

    function resendSignupEmailAction()
    {
        if (!$id = $this->_request->getInt('id'))
            throw new Am_Exception_InputError("Empty id");

        $user = $this->getDi()->userTable->load($id);
        $user->sendSignupEmail();
        $this->_response->ajaxResponse(['success' => true]);
    }
}
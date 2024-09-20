<?php

class Am_Form_Subuser extends Am_Form
{
    protected $record;
    protected $reseller;

    public function __construct(User $record, User $reseller)
    {
        $this->record = $record;
        $this->reseller = $reseller;
        parent::__construct();
    }

    public function init()
    {
        parent::init();

        $_ = $this->getDi()->config->get('subusers_fields', []);
        if (!in_array('email', $_)) {
            $_[] = 'email';
        }
        if (!in_array('groups', $_)) {
            $_[] = 'groups';
        }

        foreach ($_ as $f) {
            if (method_exists($this, 'setupElement' . ucfirst($f))) {
                $this->{'setupElement' . ucfirst($f)}();
            } elseif ($field = $this->getDi()->userTable->customFields()->get($f)) {
                $field->addToQF2($this);
            }
        }
    }

    function setupElementLogin()
    {
        $len = Am_Di::getInstance()->config->get('login_min_length', 6);
        $login = $this->addText('login')
            ->setLabel(___("Username\nit must be %d or more characters in length\nmay only contain letters, numbers, and underscores", $len));
        $login->addRule('required');
        $login->addRule('length', ___('Please enter valid Username. It must contain at least %d characters', $len), [$len, Am_Di::getInstance()->config->get('login_max_length', 64)]);
        $login->addRule('regex', !Am_Di::getInstance()->config->get('login_disallow_spaces') ?
            ___('Username contains invalid characters - please use digits, letters or spaces') :
            ___('Username contains invalid characters - please use digits, letters, dash and underscore'),
            Am_Di::getInstance()->userTable->getLoginRegex());
        $login->addRule('callback2', null, [$this, 'checkUniqLogin']);

        if (!Am_Di::getInstance()->config->get('login_dont_lowercase'))
            $login->addFilter('mb_strtolower');
    }

    function setupElementPass()
    {
        $gr = $this->addGroup()->setLabel(___('Password'));
        $pass = $gr->addPassword('_pass');
        if (!$this->record || !$this->record->isLoaded()) {
            $pass->addRule('required');
            $gr->addClass('am-row-required');
        }
        $label_generate = ___('generate');
        $this->addScript()->setScript(<<<CUT
jQuery(document).ready(function(){
    var pass0 = jQuery("input#_pass-0").after("&nbsp;<a href='javascript:;' id='generate-pass' class='local-link'>$label_generate</a>");
    jQuery("a#generate-pass").click(function(){
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

    function setupElementMobile_number()
    {
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
    }

    function setupElementName()
    {
        $nameField = $this->addGroup('', ['id' => 'name'], ['label' => ___('Name')]);
        $nameField->setSeparator(' ');
        $nameField->addText('name_f', ['placeholder' => ___('First Name')])->addRule('required');
        $nameField->addText('name_l', ['placeholder' => ___('Last Name')])->addRule('required');
        $nameField->addRule('required', ___('Please enter both First and Last names'), 2);
    }

    function setupElementName_f()
    {
        $this->addText('name_f')
            ->setLabel(___('First Name'))
            ->addRule('required');
    }

    function setupElementName_l()
    {
        $this->addText('name_l')
            ->setLabel(___('Last Name'))
            ->addRule('required');
    }

    function setupElementPhone()
    {
        $this->addText('phone')
            ->setLabel(___('Phone'));
    }

    function setupElementEmail()
    {
        $em = $this->addText('email')
            ->setLabel(___('E-Mail Address'));

        if ($this->getDi()->config->get('subusers_cannot_change_email') && $this->record->isLoaded()) {
            $em->toggleFrozen(true);
        } else {
            $em->addRule('required');
            $em->addRule('callback2', null, [$this, 'checkUniqEmail']);
        }
    }

    function setupElementStreet()
    {
        $this->addText('street')
            ->setLabel(___('Street'))
            ->addRule('required');
    }

    function setupElementCity()
    {
        $this->addText('city')
            ->setLabel(___('City'))
            ->addRule('required');
    }

    function setupElementZip()
    {
        $this->addText('zip')
            ->setLabel(___('Zip'))
            ->addRule('required');
    }

    function setupElementState()
    {
        $group = $this->addGroup(null, ['id' => 'grp-state'])->setLabel(___('State'));
        $stateSelect = $group->addSelect('state')
            ->setId('f_state')
            ->loadOptions($stateOptions = Am_Di::getInstance()->stateTable->getOptions(@$_REQUEST['country'], true));
        $stateText = $group->addText('state')->setId('t_state');
        $disableObj = $stateOptions ? $stateText : $stateSelect;
        $disableObj->setAttribute('disabled', 'disabled')->setAttribute('style', 'display: none');
        $group->addRule('required', ___('Please enter %s', ___('State')));
    }

    function setupElementCountry()
    {
        $country = $this->addSelect('country')->setLabel(___('Country'))
            ->setId('f_country')
            ->loadOptions(Am_Di::getInstance()->countryTable->getOptions(true));
        $country->addRule('required', ___('Please enter %s', ___('Country')));
    }

    function setupElementGroups()
    {
        if($this->record->pk()) {
            $options = $this->getDi()->subusersSubscriptionTable->getProductOptionsForUser($this->reseller, $this->record->pk());
            reset($options);
            if (count($options) == 1) {
                $sel = $this->addSelect('_groups[0]', ['id' => 'subuser-groups'])
                    ->setLabel(___('Groups'))
                    ->loadOptions($options)
                    ->setValue(key($options));

                $sel->persistentFreeze(true);
                $sel->toggleFrozen(true);
            } else {
                $sel = $this->addMagicSelect('_groups', ['id' => 'subuser-groups'])->setLabel(___('Groups'))
                    ->loadOptions($options);
            }
        } else {
            $options = $this->getDi()->subusersSubscriptionTable->getProductOptions($this->reseller, true);
            reset($options);
            if (count($options) == 1) {
                $sel = $this->addSelect('_groups[0]', ['id' => 'subuser-groups'])
                    ->setLabel(___('Groups'))
                    ->loadOptions($options)
                    ->setValue(key($options));

                $sel->persistentFreeze(true);
                $sel->toggleFrozen(true);
            } else {
                $sel = $this->addMagicSelect('_groups', ['id' => 'subuser-groups'])
                    ->setLabel(___('Groups'))
                    ->loadOptions($options);
            }
        }
    }

    function checkUniqLogin($login)
    {
        if (!preg_match($this->getDi()->userTable->getLoginRegex(), $login))
            return ___('Username contains invalid characters - please use digits, letters, dash and underscore');
        if ($this->record->getTable()->checkUniqLogin($login, $this->record ? $this->record->pk() : null) === 0)
            return ___('Username %s is already taken. Please choose another username', Am_Html::escape($login));
    }

    function checkUniqEmail($email)
    {
        if (!Am_Validate::empty_or_email($email))
            return ___('Please enter valid Email');
        if ($email && $this->record->getTable()->checkUniqEmail($email, $this->record ? $this->record->pk() : null) === 0) {
            $user = $this->getDi()->userTable->findFirstByEmail($email);
            if (!$user->subusers_parent_id) {
                return ___('An account with the same email already exists. You can %sinvite user%s to join your account', '<a href="' . $this->getDi()->url('subusers/invite', ['email' => $email]) . '">', '</a>');
            } else {
                return ___('An account with the same email already exists.');
            }
        }
    }

    function getDi()
    {
        return Am_Di::getInstance();
    }
}
<?php

/**
 * Class Am_Form_Setup_Subusers
 *
 * @help-id "PluginDocs/Modules/Subusers"
 */
class Am_Form_Setup_Subusers extends Am_Form_Setup
{
    function __construct()
    {
        parent::__construct('subusers');
        $this->setTitle(___('Sub Users'));
    }

    function initElements()
    {
        $this->addAdvCheckbox('subusers_cannot_pay')
            ->setLabel(___('Hide Payment Forms and History from SubUsers'));

        $this->addAdvRadio('subusers_cannot_delete')
            ->setLabel(___('Reseller cannot delete subuser accounts'))
            ->loadOptions([
                '0' => ___('Resellers can delete subusers'),
                '1' => ___('Resellers cannot delete subusers'),
                '2' => ___('Resellers can delete subusers only when limit is over'),
            ]);
        $this->setDefault('subusers_cannot_delete', 0);
        $this->addAdvCheckbox('subusers_soft_delete')
            ->setLabel(___("Use Soft Delete\njust detach subuser account instead of delete it completly"));
        $this->addAdvCheckbox('subusers_cannot_edit')
            ->setLabel(___('Reseller cannot edit subusers accounts after insertion'));

        $fOptions = [
            'login' => ___('Username'),
            'email' => ___('E-Mail'),
            'pass' => ___('Password'),
            'name' => ___('Name (Both First and Last)'),
            'name_f' => ___('First Name'),
            'name_l' => ___('Last Name'),
            'phone' => ___('Phone'),
            'mobile_number' => ___('Mobile Number'),
            'groups' => ___('Groups'),
            'country' => ___('Country'),
            'state' => ___('State'),
            'city' => ___('City'),
            'street' => ___('Street'),
            'zip' => ___('Zip'),
        ];

        foreach($this->getDi()->userTable->customFields()->getAll() as $field){
            if(strpos($field->name, '_')===0) continue;
            $fOptions[$field->name] = $field->title;
        }

        $this->addSortableMagicSelect('subusers_fields')
            ->setLabel(___("Reseller can manage the following subuser fields\n".
                "Email and Groups fields is always enabled"))
            ->loadOptions($fOptions)
            ->addRule('callback', ___('Email and Groups fields is required'), function($v){
                return in_array('email', $v) && in_array('groups', $v);
            });

        $this->setDefault('subusers_fields', ['name', 'login', 'email', 'pass', 'groups']);

        $this->addAdvCheckbox('subusers_cannot_change_email')
            ->setLabel(___('Subuser e-mail address can be changed by site admin only'));

        $this->addAdvCheckbox('subusers_can_login')->setLabel(___('Reseller can log-in as subuser'));

        $g = $this->addGroup();
        $g->setSeparator(' ');
        $g->setLabel(___("Use different profile form for subusers"));

        $g->addAdvCheckbox('subusers_different_profile_form');

        $savedForm = $this->getDi()->savedFormTable->findFirstByType(Bootstrap_Subusers::SAVED_FORM_TYPE);
        $url = $this->getDi()->url('admin-saved-form', [
            '_s_a' => 'edit',
            '_s_id' => $savedForm->pk(),
            '_s_b' => $this->getDi()->url('admin-setup/subusers', false)
        ]);

        $g->addHtml()->setHtml(sprintf('<a href="%s" id="subusers-form-edit" class="link" target="_blank">%s</a>',
            $url, Am_Html::escape(___('Edit Profile Form'))));

        $this->addScript()
            ->setScript(<<<CUT
jQuery(function($){
    $('#subusers_different_profile_form-0').change(function(){
        $('#subusers-form-edit').toggle(this.checked)
    }).change();
})
CUT
                );

        $this->addAdvCheckbox('hide_member-main-subscriptions')
            ->setLabel(___('Hide Active Subscriptions Widget for Subuser'));

        $this->addElement('email_checkbox', 'subusers.registration_mail')
             ->setLabel(___('Registration E-Mail to Subusers'));

        $this->addElement('email_link', 'subusers.invite_mail')
             ->setLabel(___('Invite E-Mail to Subusers'));
    }
}

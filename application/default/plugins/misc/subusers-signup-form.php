<?php

/**
 * @am_plugin_api 6.0
*/
class Am_Plugin_SubusersSignupForm extends Am_Plugin
{

    const
        PLUGIN_STATUS = self::STATUS_BETA,
        PLUGIN_COMM = self::COMM_COMMERCIAL,
        PLUGIN_REVISION = '6.3.20',
        URL_FORMAT_LOGIN = 'url-format-login',
        URL_FORMAT_ENCRYPTED = 'url-format-encrypted',
        CONFIG_URL_FORMAT = 'config-url-format',
        CONFIG_DEFAULT_FORM = 'config-default-form',
        CONFIG_RESELLER_FORM = 'config-reseller-form',
        CONFIG_RESELLER_KEY = 'config-reseller-key',
        CONFIG_ENABLE_FOR_ALL = 'config-enable-for-all',
        CONFIG_ENABLE_FOR_RESELLER = 'config-enable-for-reseller',
        CONFIG_SHOW_URL = 'config-show-url',
        REQUEST_URL_TYPE = 'request-url-type',
        STORE_SAVED_FORM_KEY = 'saved-form-reseller-';

    protected
        $_configPrefix = 'misc.',
        $reseller;

    function isConfigured()
    {
        return $this->getDi()->modules->isEnabled('subusers');
    }

    function init()
    {
        // Add routes. 
        switch ($this->getConfig(self::CONFIG_URL_FORMAT))
        {
            case self::URL_FORMAT_ENCRYPTED :
                $this->getDi()->router->addRoute('reseller_signup', new Am_Mvc_Router_Route(
                    'reseller/f/:fid/signup/:pid', array(
                    'module' => 'default',
                    'controller' => 'signup',
                    'action' => 'index',
                    'c' => '',
                    'pid'=>'',
                    self::REQUEST_URL_TYPE => self::URL_FORMAT_ENCRYPTED
                    )
                ));
                break;

            case self::URL_FORMAT_LOGIN :
            default:
                $this->getDi()->router->addRoute('reseller_signup', new Am_Mvc_Router_Route(
                    'reseller/u/:uid/signup/:pid', array(
                    'module' => 'default',
                    'controller' => 'signup',
                    'action' => 'index',
                    'c' => '',
                    'pid'=>'',
                    self::REQUEST_URL_TYPE => self::URL_FORMAT_LOGIN
                    )
                ));
                break;
        }
        if ($this->getConfig(self::CONFIG_SHOW_URL))
            $this->getDi()->blocks->add(
                'member/main/right', new Am_Block_Base(
                ___('Your Reseller Signup Forms URL'), 'reseller-signup-form-url', null, function (Am_View $v)
            {
                $formURL = Am_Di::getInstance()->plugins_misc->loadGet('subusers-signup-form')->getFormUrl($reseller=Am_Di::getInstance()->auth->getUser());
                $counts = $reseller->data()->get('subusers_count');
                $available_products = array();
                foreach($counts as $pid=>$data){
                    if($data['avail_count']) $available_products[] = $pid;
                }
                $links = array();
                foreach($available_products as $pid){
                    $product = Am_Di::getInstance()->productTable->load($pid);
                    $links[] = sprintf("<li><a href='%s/%s' target=_blank>%s</a>", $formURL,$pid, $product->title);
                }
                return implode("\n", $links);
            }));
    }

    function getFormUrl(User $user)
    {
        switch ($this->getConfig(self::CONFIG_URL_FORMAT))
        {
            case self::URL_FORMAT_ENCRYPTED :
                return $this->getDi()->url(array("reseller/f/%s/signup", $this->getResellerFormKey($user)),null,false,2);
                break;

            case self::URL_FORMAT_LOGIN :
                return $this->getDi()->url(array("reseller/u/%s/signup", $user->login),null,false,2);
                break;
        }
    }

    function _initSetupForm(\Am_Form_Setup $form)
    {
        $form->setTitle('Reseller Signup Form');

        $form->addAdvCheckbox(self::CONFIG_ENABLE_FOR_ALL)
            ->setLabel("Enable Signup Form for All Resellers\n"
                . "if disabled, signup form will be available for reseller\n"
                . "only if you enable this in reseller profile");

        $form->addSelect(self::CONFIG_URL_FORMAT, '', array('options' => array(
                    self::URL_FORMAT_LOGIN => 'Use reseller login in signup form url: reseller/u/RESELLER/signup',
                    self::URL_FORMAT_ENCRYPTED => 'Use unique key  in signup form url: reseller/f/fjxnio3ljf8/signup',
            )))
            ->setLabel('Signup Form Url Format');

        $form->addSelect(self::CONFIG_DEFAULT_FORM)
            ->setLabel('Default Signup Form for all resellers')
            ->loadOptions($this->getDi()->savedFormTable->getOptions(SavedForm::T_SIGNUP));
        $form->addAdvCheckbox(self::CONFIG_SHOW_URL)->setLabel("Show Signup Form URL on Reseller's Dashboard");
    }

    function getResellerFormKey(User $user)
    {
        $this->user = $user;
        $key = $user->data()->get(self::CONFIG_RESELLER_KEY);
        if (!$key)
            do
            {
                $key = $this->getDi()->security->randomString(10);
            }
            while (!$this->validateKey($key));

        $user->data()->set(self::CONFIG_RESELLER_KEY, $key)->update();
        return $key;
    }

    function validateKey($key)
    {

        if (!$key)
            return true;


        return !Am_Di::getInstance()->db->selectCell(
                "SELECT COUNT(*) "
                . "FROM ?_data "
                . "WHERE `table`='user' AND `key`=? AND `value` = ? AND id<>?", self::CONFIG_RESELLER_KEY, $key, $this->user->pk());
    }

    function onGridUserInitForm(Am_Event_Grid $event)
    {
        $form = $event->getGrid()->getForm();
        $this->user = $event->getGrid()->getRecord();
        if ($this->user && ($this->user->data()->get('subusers_count') ||
            ($this->user->isLoaded() && $this->getDi()->userTable->countBy(array('subusers_parent_id' => $this->user->pk())))))
        {
            if (!$this->getConfig(self::CONFIG_ENABLE_FOR_ALL))
                $form->addAdvCheckbox(self::CONFIG_ENABLE_FOR_RESELLER)->setLabel(
                    "Enable Signup Form for Reseller\n"
                    . "Reseller will be able to accept signups  throw special signup form url"
                );

            $form->addSelect(self::CONFIG_RESELLER_FORM)
                ->setLabel('Reseller Signup Form if enabled')
                ->loadOptions(array('' => '-- Please Select --') + $this->getDi()->savedFormTable->getOptions(SavedForm::T_SIGNUP));

            if ($this->getConfig(self::CONFIG_URL_FORMAT) == self::URL_FORMAT_ENCRYPTED)
            {
                $this->getResellerFormKey($this->user);
                $gr = $form->addGroup('')->setLabel('Encrypted key that will be used in signup form url');
                $gr->addHtml()->setHTML($this->getDi()->url('reseller/f/',null,false));
                $gr->addText(self::CONFIG_RESELLER_KEY)
                    ->addRule('callback', 'Key already used. Please select another one.', array($this, 'validateKey'));
                $gr->addHtml()->setHTML('/signup');
            }
        }
    }

    public
        function getResellerOptions()
    {
        $ret = array();
        foreach ($this->getDi()->userTable->selectObjects(
            "SELECT * "
            . "FROM ?_user u LEFT JOIN  ?_data d ON d.`table` = 'user' AND d.id=u.user_id AND d.`key` = 'subusers_count' "
            . "WHERE d.`value` IS NOT NULL") as $u)
        {
            $ret[$u->pk()] = sprintf("%s (%s %s, %s)", $u->login, $u->name_f, $u->name_l, $u->email);
        }
        return $ret;
    }

    public
        function onGridSavedFormInitForm(Am_Event_Grid $event)
    {
        $form = $event->getGrid()->getForm();
        $record = $event->getGrid()->getRecord();

        if (!$record || $record->type != SavedForm::T_SIGNUP)
            return;

        $form->addSelect('reseller_id', 'class=am-combobox', array('options' => array('' => '-- Please Select --') + $this->getResellerOptions()))
            ->setLabel('Associate this form with reseller');
    }

    public
        function onGridSavedFormAfterSave(Am_Event_Grid $event)
    {
        $input = $event->getGrid()->getForm()->getValue();
        $savedForm = $event->getGrid()->getRecord();
        $this->getDi()->store->set(
            self::STORE_SAVED_FORM_KEY . $savedForm->pk(), !empty($input['reseller_id']) ? $input['reseller_id'] : null
        );
    }

    public
        function onGridSavedFormValuesToForm(Am_Event_Grid $event)
    {
        $args = $event->getArgs();
        $record = $args[1];
        $args[0]['reseller_id'] = $this->getDi()->store->get(self::STORE_SAVED_FORM_KEY . $record->pk());
    }

    public
        function onGridSavedFormValuesFromForm(Am_Event_Grid $event)
    {
        $args = $event->getArgs();
        unset($args[0]['reseller_id']);
    }

    public
        function onGridUserAfterSave(Am_Event_Grid $event)
    {
        $input = $event->getGrid()->getForm()->getValue();
        $user = $event->getGrid()->getRecord();

        $user->data()
            ->set(self::CONFIG_ENABLE_FOR_RESELLER, $input[self::CONFIG_ENABLE_FOR_RESELLER])
            ->set(self::CONFIG_RESELLER_FORM, $input[self::CONFIG_RESELLER_FORM])
            ->set(self::CONFIG_RESELLER_KEY, $input[self::CONFIG_RESELLER_KEY])
            ->update();
    }

    function onLoadSignupForm(Am_Event $event)
    {
        $request = $event->getRequest();
        $formRecord = $event->getReturn();
        if ($reseller_id = $this->getDi()->store->get(self::STORE_SAVED_FORM_KEY . $formRecord->pk()))
        {
            $reseller = $this->getDi()->userTable->load($reseller_id);
            $this->form = $formRecord;
        }
        else if ($url_type = $request->getParam(self::REQUEST_URL_TYPE))
        {
            // Custom route; Need to find form
            switch ($url_type)
            {
                case self::URL_FORMAT_LOGIN :
                    $login = $request->getParam('uid');
                    $reseller = $this->getDi()->userTable->findFirstByLogin($login);
                    break;

                case self::URL_FORMAT_ENCRYPTED :
                    $key = $request->getParam('fid');
                    $reseller = $this->getDi()->userTable->findFirstByData(self::CONFIG_RESELLER_KEY, $key);
                    break;
                default:
                    throw new Am_Exception_InternalError('Unknown url format');
            }
        }

        if (!empty($reseller))
        {

            if (!$this->getConfig(self::CONFIG_ENABLE_FOR_ALL) && !$reseller->data()->get(self::CONFIG_ENABLE_FOR_RESELLER))
                throw new Am_Exception_InternalError('Form is not allowed for reseller');

            $subusers_count = $reseller->data()->get('subusers_count');

            $canAdd = 0;
            foreach ((array) $subusers_count as $product_id => $v)
                if ($v['avail_count'] > ($v['pending_count'] + $v['active_count']))
                    $canAdd++;

            if (!$canAdd)
                throw new Am_Exception_InputError(___('Registrations are not allowed. Please contact webmaster for details.'));

            // Default form should be returned if nothing found. 

            if (empty($this->form))
            {
                $this->form = $event->getReturn();

                $form_id = $reseller->data()->get(self::CONFIG_RESELLER_FORM);

                if (!$form_id)
                    $form_id = $this->getConfig(self::CONFIG_DEFAULT_FORM);

                if ($form_id)
                    $this->form = $this->getDi()->savedFormTable->load($form_id);
            }

            $this->reseller = $reseller;

            $event->setReturn($this->form);
        }
    }

    /**
     * 
     * @return Bootstrap_Subusers
     */
    function getModule()
    {
        return $this->getDi()->modules->loadGet('subusers');
    }

    
    
    
    function onSignupUserAdded(Am_Event $event)
    {
        
        if (!empty($this->reseller))
        {
            $user = $event->getUser();
            $user->subusers_parent_id = $this->reseller->pk();
            $user->update();
            $this->addAccess($event);
            $event->getForm()->getSessionContainer()->storeOpaque('am-order-data', array('redirect'=>$this->getDi()->url('thanks')));
        }
    }
    
    function onSignupUserUpdated(Am_Event $event)
    {
        
        if (!empty($this->reseller))
        {
            $this->addAccess($event);
            $event->getForm()->getSessionContainer()->storeOpaque('am-order-data', array('redirect'=>$this->getDi()->url('thanks')));
        }
        
    }
    function addAccess(Am_Event $event)
    {
            $user = $event->getUser();   
            if($product_id = $this->getDi()->request->get('product_id')){
                $products = array($product_id);
            }else{
                $products = array_keys($this->getDi()->subusersSubscriptionTable->getProductOptionsForUser($this->reseller, $user->pk()));
            }
            $this->getDi()->subusersSubscriptionTable->setForUser(
                $user->pk(), $products
            );
            $this->getModule()->checkAndUpdate($this->reseller);
    }

    
    /** 
     * 
     * @return User $reseller;
     */
    function getReseller(){
        return $this->reseller;
    }

}

Am_Di::getInstance()->hook->add(Am_Event::LOAD_BRICKS, function(Am_Event $e){
class Am_Form_Brick_ResellerProduct extends Am_Form_Brick
{
    protected $labels = array(
        'Membership Type',
        'Please choose a membership type',
    );
    protected $hideIfLoggedInPossible = self::HIDE_DONT;

    
    /**
     * 
     * @return Am_Plugin_SubusersSignupForm $plugin
     */
    public function getPlugin()
    {
        return Am_Di::getInstance()->plugins_misc->loadGet('subusers-signup-form');
    }
    
    
    public function insertBrick(HTML_QuickForm2_Container $form)
    {
        $r = $this->getPlugin()->getReseller();
        if(!$r) return;
        if($pid = Am_Di::getInstance()->request->getParam('pid')){
            $product = Am_Di::getInstance()->productTable->load($pid);
            $form->addHidden('product_id');
            $el = $form->addHTML()->setHTML($product->title);
            $form->addDataSource(new HTML_QuickForm2_DataSource_Array(array(
                'product_id' => $pid
                )));
            
        }else{
            $r = $this->getPlugin()->getReseller();
            $subusers_count = $r->data()->get('subusers_count');
            $options = array();
            foreach ((array) $subusers_count as $product_id => $v)
                if ($v['avail_count'] > ($v['pending_count'] + $v['active_count'])){
                    $product = Am_Di::getInstance()->productTable->load($product_id);
                    $options[$product_id] = $product->title;
                }
            $el = $form->addSelect('product_id')->loadOptions($options);
        }
        
        $el->setLabel($this->___('Membership Type')."\n".$this->___('Please choose a membership type'));
    }
    
    
    

    public function initConfigForm(Am_Form $form)
    {
    }
}

    
});

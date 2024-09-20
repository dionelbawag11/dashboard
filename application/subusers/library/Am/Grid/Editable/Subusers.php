<?php

class Am_Grid_Action_ImportCSV extends Am_Grid_Action_Abstract
{
    const UPLOAD_PREFIX = 'subuser-import';

    protected $type = self::NORECORD,
        $reseller,
        $fields = [],
        $cssClass = "link";

    function __construct($id = null, $title = null, User $reseller = null)
    {
        parent::__construct($id, $title);
        $this->reseller = $reseller;
        foreach($this->getDi()->userTable->customFields()->getAll() as $f) {
            $this->fields[$f->name] = $f;
        }
    }

    function run()
    {
        $errors = [];
        $importFields = [
            'email' => 'email',
            'name_f' => 'name_f',
            'name_l' => 'name_l',
        ];

        foreach($this->getDi()->config->get('subusers_fields', []) as $field){
            $importFields[$field] = $field;
        }
        unset($importFields['name']);
        unset($importFields['groups']);
        if (isset($importFields['mobile_number'])) {
            unset($importFields['mobile_number']);
            $importFields['mobile_area_code'] = 'mobile_area_code';
            $importFields['mobile_number'] = 'mobile_number';
        }

        $form = new Am_Form();
        $form->setAttribute('target', '_top');
        $form->setAttribute('enctype', 'multipart/form-data');
        $form->addHtml(null, ['class' => 'am-row-wide'])
            ->setHtml('<div>' . ___('File should contain CSV list of user records for import in the following format:<br />
<strong>%s</strong>', implode(',', $importFields)) . '</div>');
        $form->addFile('file[]', ['class' => 'styled'], ['prefix' => self::UPLOAD_PREFIX])
            ->setLabel(___('File'))
            ->addRule('required');

        $options = $this->getDi()->subusersSubscriptionTable->getProductOptions($this->reseller, true);
        reset($options);
        if (count($options) == 1)
        {
            $sel = $form->addSelect('groups[0]')
                ->setLabel(___('Groups'))
                ->loadOptions($options)
                ->setValue(key($options));

            $sel->persistentFreeze(true);
            $sel->toggleFrozen(true);
        } else {
            $form->addMagicSelect('groups')->setLabel(___('Groups'))
                ->loadOptions($options);
        }
        $form->addAdvCheckbox('skip_first_line')->setLabel(___("Skip First Line\nuse this options if you have column headers in first line of CSV file"));

        $form->addSaveButton(___('Do Import'));

        $this->initForm($form);

        if ($form->isSubmitted()) {
            $value = $form->getValue();

            $upload = new Am_Upload($this->getDi());
            $upload->setPrefix(self::UPLOAD_PREFIX)->setTemp(3600);
            $upload->processSubmit('file');
            $_ = $upload->getUploads();
            if (!$_ || !$_[0]) throw new Am_Exception_InputError(___('CSV File was not specified'));
            $file = $_[0];

            $pn = fopen($file->getFullPath(), 'r');
            if(isset($value['skip_first_line']) && $value['skip_first_line'])
                fgetcsv($pn);
            while($res = fgetcsv($pn)) {
                if (count($res) == count($importFields)) {
                    $imp = [];
                    foreach($importFields as $fieldName => $v) {
                        $imp[$fieldName] = trim(array_shift($res));
                    }

                    $user = $this->getDi()->userRecord;
                    if ($error = $this->checkUniqEmail($imp['email'])) {
                        $errors[] = $error;
                        continue;
                    }

                    if (!empty($imp['login']) && ($error = $this->checkUniqLogin($imp['login']))) {
                        $errors[] = $error;
                        continue;
                    }

                    $user->email =  $imp['email'];
                    $user->name_f =  $imp['name_f'];
                    $user->name_l =  $imp['name_l'];

                    !empty($imp['pass']) ?
                        $user->setPass($imp['pass']) :
                        $user->generatePassword();

                    !empty($imp['login']) ?
                        $user->login = $imp['login'] :
                        $user->generateLogin();

                    foreach(['login', 'email', 'pass', 'name_f', 'name_l'] as $k) {
                        unset($imp[$k]);
                    }

                    if (!empty($imp['mobile_area_code'])) {
                        //RU+7
                        if (preg_match('/^[A-Z]{2}\+[0-9]+$/', $imp['mobile_area_code'])) {
                            $user->mobile_area_code = $imp['mobile_area_code'];
                        //+7
                        } elseif (preg_match('/^\+([0-9]+)$/', $imp['mobile_area_code'], $m)) {
                            if ($country = $this->getDi()->countryTable->findFirstByPhoneCode($m[1])) {
                                $user->mobile_area_code = "$country->country+$m[1]";
                            }
                        //7
                        } elseif (is_numeric($imp['mobile_area_code'])) {
                            if ($country = $this->getDi()->countryTable->findFirstByPhoneCode($imp['mobile_area_code'])) {
                                $user->mobile_area_code = "$country->country+{$imp['mobile_area_code']}";
                            }
                        }

                        unset($imp['mobile_area_code']);
                    }

                    foreach($imp as $k=>$v){
                        $this->setValue($user, $k, $v);
                    }

                    $user->data()->set('signup_email_sent', 1);
                    $user->subusers_parent_id = $this->reseller->pk();
                    $user->is_approved = 1;

                    $user->save();

                    if ($et = Am_Mail_Template::load('subusers.registration_mail', $user->lang))
                    {
                        $et->setUser($user);
                        $et->setPassword($user->getPlaintextPass());
                        $et->setReseller($this->reseller);

                        //backward compatibality (reseller_name_f, reseller_name_l, reseller_email)
                        $et->setReseller_name_f($this->reseller->name_f);
                        $et->setReseller_name_l($this->reseller->name_l);
                        $et->setReseller_email($this->reseller->email);

                        if(!empty($value['groups']))
                        {
                            $userTitle = [];
                            foreach ($this->getDi()->productTable->loadIds($value['groups']) as $product)
                                $userTitle[] = $product->title;
                            $et->setUser_product(implode(', ', $userTitle));

                            $resellerTitle = [];
                            $ids = $this->getDi()->db->selectCol(
                                "SELECT product_id FROM ?_subusers_product_product WHERE subusers_product_id IN (?a)",
                                $value['groups']
                            );
                            foreach ($this->getDi()->productTable->loadIds($ids) as $product) {
                                $resellerTitle[] = $product->title;
                            }
                            $et->setReseller_product(implode(', ', $resellerTitle));
                        }
                        $et->send($user);
                    }

                    $this->getDi()->subusersSubscriptionTable->setForUser($user->pk(), $value['groups']);
                } else {
                    $errors[] = ___('Wrong columns count. Please use exact format for import file: %s', implode(',', $importFields));
                    break;
                }
            }
            fclose($pn);
            $this->getDi()->modules->get('subusers')->checkAndUpdate($this->reseller);

            if ($errors) {
                $out = '<ul class="am-errors">';
                foreach ($errors as $error) {
                    $out .= sprintf('<li>%s</li>', $error);
                }
                $out .= "</ul>";

                echo $out . $this->renderBackUrl() . '<br /><br />';
            } else {
                $this->grid->redirectBack();
            }
        } else {
            echo $this->renderTitle();
            echo $form;
        }
    }

    function setValue($user, $k, $v)
    {
        if (isset($this->fields[$k]) && in_array($this->fields[$k]->type, [
            'checkbox', 'multi_select'
            ])) {
            $v = explode(',', $v);
        }

        if (isset($this->fields[$k]) && empty($this->fields[$k]->sql)) {
            $user->data()->set($k, $v);
        } else {
            $user->set($k, $v);
        }
    }

    function getDi()
    {
        return Am_Di::getInstance();
    }

    function checkUniqLogin($login)
    {
        if (!preg_match($this->getDi()->userTable->getLoginRegex(), $login))
            return ___('Username [%s] contains invalid characters - please use digits, letters, dash and underscore', $login);
        if ($this->getDi()->userTable->checkUniqLogin($login) === 0)
            return ___('Username [%s] is already taken. Please choose another username', Am_Html::escape($login));
    }

    function checkUniqEmail($email)
    {
        if (!Am_Validate::email($email))
            return ___('Email [%s] is not valid', Am_Html::escape($email));
        if ($this->getDi()->userTable->checkUniqEmail($email) === 0)
            return ___('An account with the same email [%s] is already exists.', Am_Html::escape($email));
    }

    protected function initForm($form)
    {
        $form->setDataSources([$this->grid->getCompleteRequest()]);

        $vars = [];
        foreach ($this->grid->getVariablesList() as $k) {
            $vars[$this->grid->getId() . '_' . $k] = $this->grid->getRequest()->get($k, "");
        }
        foreach (Am_Html::getArrayOfInputHiddens($vars) as $name => $value) {
            $form->addHidden($name)->setValue($value);
        }
    }
}

class Am_Grid_Action_Detach extends Am_Grid_Action_Abstract
{
    protected $privilege = 'delete';
    protected $title;

    public function __construct($id = null, $title = null)
    {
        $this->title = ___("Detach %s");
        $this->attributes['data-confirm'] = ___("Do you really want to detach record?");
        parent::__construct($id, $title);
    }

    public function run()
    {
        if ($this->grid->getRequest()->get('confirm')) {
            return $this->detach();
        } else {
            echo $this->renderConfirmation();
        }
    }

    public function detach()
    {
        $user = $this->grid->getRecord();
        $parent = $this->grid->getDi()->userTable->load($user->subusers_parent_id);

        $this->grid->getDi()->subusersSubscriptionTable->deleteByUserId($user->pk());
        $user->subusers_parent_id = 0;
        $user->save();
        $user->checkSubscriptions();

        $this->grid->getDi()->modules->loadGet('subusers')->checkAndUpdate($parent);

        $this->log();
        $this->grid->redirectBack();
    }
}

class Am_Grid_Editable_Subusers extends Am_Grid_Editable
{
    protected $reseller;

    function __construct($id, $title, Am_Grid_DataSource_Interface_Editable $ds, Am_Mvc_Request $request, Am_View $view, Am_Di $di = null, User $reseller)
    {
        parent::__construct($id, $title, $ds, $request, $view, $di);
        $this->reseller = $reseller;
    }

    /**
     * @return Am_Grid_Editable_Subusers
     */
    static function factory(User $reseller, Am_Mvc_Request $request, Am_View $view, Am_Di $di)
    {
        $ds = new Am_Query_User_Subusers($reseller->pk());
        $ds->leftJoin('?_subusers_subscription', 'sgu');
        $ds->addField('GROUP_CONCAT(sgu.product_id)', 'groups');
        $ds->addField('GROUP_CONCAT(sgu.status)', 'groups_status');

        $grid = new self('_subusers', ___("Subusers"), $ds, $request, $view, $di, $reseller);
        $grid->setEventId('gridSubusersUser');

        $grid->addField('login', ___('Username'));
        $grid->addField('name_f', ___('First Name'));
        $grid->addField('name_l', ___('Last Name'));
        $grid->addField('email', ___('E-Mail Address'));
        $grid->addField('groups', ___('Groups'))->setRenderFunction([$grid, 'renderGroups']);

        $grid->setForm([$grid, '_createForm']);

        $grid->addCallback(Am_Grid_Editable::CB_BEFORE_INSERT, [$grid, 'beforeInsert']);
        $grid->addCallback(Am_Grid_Editable::CB_AFTER_INSERT, [$grid, 'afterInsert']);
        $grid->addCallback(Am_Grid_Editable::CB_AFTER_SAVE, [$grid, 'afterSave']);
        $grid->addCallback(Am_Grid_Editable::CB_BEFORE_SAVE, [$grid, 'beforeSave']);
        $grid->addCallback(Am_Grid_Editable::CB_AFTER_DELETE, [$grid, 'afterDelete']);
        $grid->addCallback(Am_Grid_Editable::CB_VALUES_TO_FORM, [$grid, '_valuesToForm']);

        if ($di->userTable->countBySubusersParentId($reseller->pk())>10) {
            $grid->setFilter(new Am_Grid_Filter_Text(null, [
                'login' => 'LIKE',
                'name_f' => 'LIKE',
                'name_l' => 'LIKE',
                'email' => 'LIKE'
            ]));
        }

        if($di->config->get('subusers_can_login')) {
            $grid->actionAdd(new Am_Grid_Action_Url('login', ___('Login as User'), '__ROOT__/subusers/index/login-as?id=__ID__'))->setTarget('_top');
        }

        $grid->actionGet('edit')->setTarget('_top');
        $grid->actionGet('delete')->setTarget('_top');
        $grid->actionGet('insert')->setTarget('_top');

        $subusers_count = $reseller->data()->get('subusers_count');

        $canAdd = 0;
        foreach ((array)$subusers_count as $product_id => $v) {
            if ($v['avail_count']>($v['pending_count']+$v['active_count'])) $canAdd++;
        }
        if (!$canAdd) {
            $grid->actionDelete('insert');
        }

        if ($canAdd) {
            $grid->actionAdd(new Am_Grid_Action_ImportCSV('import', ___('Import from CSV'), $reseller));
        }

        if ($di->config->get('subusers_cannot_delete') == 1) {
            $grid->actionDelete('delete');
        }
        if ($di->config->get('subusers_cannot_edit')) {
            $grid->actionDelete('edit');
        }

        if ($grid->actionGet('delete') && $di->config->get('subusers_soft_delete')) {
            $grid->actionDelete('delete');
            $grid->actionAdd(new Am_Grid_Action_Detach);
        }
        return $grid;
    }

    static function factoryAdmin(User $reseller, Am_Mvc_Request $request, Am_View $view, Am_Di $di)
    {
        $grid = self::factory($reseller, $request, $view, $di);
        $grid->actionDelete('edit');
        $grid->actionDelete('delete');

        $url = $view->userUrl('{user_id}');

        $grid->getField('login')
            ->addDecorator(new Am_Grid_Field_Decorator_Link($url, '_blank'));
        $grid->setEventId('gridSubusersAdmin');

        return $grid;
    }

    function renderGroups(User $record)
    {
        static $groupTitles;
        if (!$groupTitles)
            $groupTitles = $this->getDi()->subusersSubscriptionTable->getProductOptions($this->reseller);
        $gr = [];
        $record->groups_status = explode(',', $record->groups_status);
        foreach (explode(',', $record->groups) as $k => $id)
        {
            if (empty($id)) continue;
            $status = $record->groups_status[$k] ? ___('Active') : ___('Pending');
            $gr[] = @$groupTitles[$id] . ' (' . $status . ')';
        }
        return "<td>".
                implode(", ", $gr) .
                "</td>";
    }

    function _createForm(Am_Grid_Editable $grid)
    {
        return new Am_Form_Subuser($grid->getRecord(), $this->reseller);
    }

    function beforeInsert(array & $values, User $record, Am_Grid_Editable $grid)
    {
        $record->setForInsert($values);
        if($values['_pass']) {
            $record->setPass($values['_pass']);
        }
        if ($record->get('login') == '') {
            $record->generateLogin();
        }
        if ($record->get('pass') == '') {
            $record->generatePassword();
        }
        $record->data()->set('signup_email_sent', 1);
        $record->subusers_parent_id = $this->reseller->pk();
        $record->is_approved = 1;
    }

    function afterInsert(array & $values, User $record, Am_Grid_Editable $grid)
    {
        $sent = $this->getDi()->modules->loadGet('subusers')->getConfig('registration_mail');
        if($sent && ($et = Am_Mail_Template::load('subusers.registration_mail', $record->lang)))
        {
            $et->setUser($record);
            $et->setPassword($record->getPlaintextPass());
            $reseller = $this->getDi()->userTable->load($record->subusers_parent_id);
            $et->setReseller($reseller);

            //backward compatibality (reseller_name_f, reseller_name_l, reseller_email)
            $et->setReseller_name_f($reseller->name_f);
            $et->setReseller_name_l($reseller->name_l);
            $et->setReseller_email($reseller->email);

            if(!empty($values['_groups']))
            {
                $userTitle = [];
                foreach ($this->getDi()->productTable->loadIds($values['_groups']) as $product) {
                    $userTitle[] = $product->title;
                }
                $et->setUser_product(implode(', ', $userTitle));

                $resellerTitle = [];
                $ids = $this->getDi()->db->selectCol(
                    "SELECT product_id FROM ?_subusers_product_product WHERE subusers_product_id IN (?a)",
                    $values['_groups']
                );
                foreach ($this->getDi()->productTable->loadIds($ids) as $product) {
                    $resellerTitle[] = $product->title;
                }
                $et->setReseller_product(implode(', ', $resellerTitle));
            }
            $et->send($record);
        }
    }

    function beforeSave(array & $values, User $record)
    {
        if (in_array('pass',Am_Di::getInstance()->config->get('subusers_fields', [])) && $values['_pass']) {
            $record->setPass($values['_pass']);
        }
    }

    function afterSave(array &$values, User $record)
    {
        $this->getDi()->subusersSubscriptionTable->setForUser($record->pk(), $values['_groups']);
        $this->getDi()->modules->get('subusers')->checkAndUpdate($this->reseller);
    }

    function afterDelete()
    {
        $this->getDi()->modules->get('subusers')->checkAndUpdate($this->reseller);
    }

    function _valuesToForm(array &$values, User $record)
    {
        if ($record->isLoaded()) {
            $values['_groups'] = $this->getDi()->subusersSubscriptionTable->getForUser($record->pk());
        } else {
            $values['_groups'] = [];
        }
    }
}
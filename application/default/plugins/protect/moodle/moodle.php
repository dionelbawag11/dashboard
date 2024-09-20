<?php
/**
 * @table integration
 * @id moodle
 * @title Moodle
 * @visible_link http://www.moodle.org/
 * @hidden_link http://www.amember.com/p/Integration/Moodle/
 * @description Moodle is a course management system (CMS) - a free, Open
 * Source software package designed using sound pedagogical principles,
 * to help educators create effective online learning communities. You can
 * download and use it on any computer you have handy (including webhosts),
 * yet it can scale from a single-teacher site to a 40,000-student University.
 * @different_groups 1
 * @single_login 0
 * @type Learning Management System (LMS)
 * @logo_url moodle.png
 * @am_protect_api 6.0
 */
class Am_Protect_Moodle extends Am_Protect_Databased
{
    const PLUGIN_STATUS = self::STATUS_PRODUCTION;
    const PLUGIN_REVISION = '6.3.20';
    const MOODLE = 'moodle';

    protected $guessTablePattern = "user";
    protected $guessFieldsPattern = [
        'auth', 'confirmed', 'policyagreed', 'deleted', 'username', 'password', 'firstname', 'lastname', 'email', 'timecreated', 'timemodified',
    ];
    protected $groupMode = Am_Protect_Databased::GROUP_MULTI;
    private $_config = [];

    public function parseExternalConfig($path)
    {
        if (!is_file($config_path = $path . "/config.php") || !is_readable($config_path))
            throw new Am_Exception_InputError("This is not a valid " . $this->getTitle() . " installation");
        // Read config;
        $config = file_get_contents($config_path);
        $config = preg_replace(["/include_once/", "/require_once/", "/include/", "/require/"], "trim", $config);
        $config = preg_replace(["/\<\?php/", "/\?\>/"], "", $config);
        eval($config);
        if (!isset($CFG))
            throw new Am_Exception_InputError("This is not a valid " . $this->getTitle() . " installation");
        if(isset($CFG->dboptions['dbport'])&&$CFG->dboptions['dbport']!= '3306'){
            $port = $CFG->dboptions['dbport'];
        }
        return [
            'db' => $CFG->dbname,
            'prefix' => $CFG->prefix,
            'host' => $CFG->dbhost.(isset($port)?":".$port : ""),
            'user' => $CFG->dbuser,
            'pass' => $CFG->dbpass,
            'salt' => $CFG->passwordsaltmain,
            'moodleurl' => $CFG->wwwroot,
            'dataroot' => $CFG->dataroot,
        ];
    }

    function canAutoCreate()
    {
        return true;
    }

    function getOriginalGroups()
    {
        $ret = [];
        try
        {
            foreach ($this->getDb()->select("SELECT * from ?_groups") as $r)
            {
                $ret[$r['id']] = $r['name'];
            }
        }
        catch (Exception $e)
        {
            $ret = [];
        }
        return $ret;
    }

    public function getIntegrationFormElements(HTML_QuickForm2_Container $group)
    {
        $groups = $this->getManagedUserGroups();
        $options = [];
        foreach ($groups as $g) {
            $options[$g->getId()] = $g->getTitle();
        }
        $group->addSelect('gr', ['class' => 'am-combobox'], ['options' => $options])
            ->setLabel('Moodle Course');

        $group->addMagicselect('original_groups', [], ['options' => $this->getOriginalGroups()])
            ->setLabel("Moodle Group\n(optional)");
    }

    public function getIntegrationSettingDescription(array $config)
    {
        $groups = array_combine((array) $config['gr'], (array) $config['gr']);
        try
        {
            foreach ($this->getAvailableUserGroups() as $g)
            {
                $id = $g->getId();
                if (!empty($groups[$id]))
                    $groups[$id] = $g->getTitle();
            }
        } catch (Am_Exception_PluginDb $e)
        {

        }
        $ret = ___('Assign Course') . ' [' . implode(",", array_values($groups)) . ']';

        if (isset($config['original_groups']) && ($id = $config['original_groups'])) {
            $title = $this->getOriginalGroups();
            $g = [];
            foreach($id as $gid)
            {
                $g[] = $title[$gid] ?? "#$gid";
            }

            $ret .= ", Assign Groups [".  implode(', ', $g)."]";
        }
        return $ret;
    }

    function getConfiguredOriginalGroups()
    {
        $groups = [];
        foreach($this->getIntegrationTable()->findBy(['plugin' => $this->getId()]) as $integration){
            $vars = unserialize($integration->vars);
            if(isset($vars['original_groups']) && is_array($vars['original_groups']) && count($vars['original_groups']))
                $groups = array_merge($groups, $vars['original_groups']);
        }
        return array_filter(array_unique($groups));
    }

    function calculateOriginalGroups(User $user)
    {
        $groups = [];
        if ($user && $user->pk())
        {
            foreach ($this->getIntegrationTable()->getAllowedResources($user, $this->getId()) as $integration)
            {
                $vars = unserialize($integration->vars);
                if (isset($vars['original_groups']) && is_array($vars['original_groups']) && count($vars['original_groups'])) {
                    $groups = array_merge($groups, $vars['original_groups']);
                }
            }
        }
        return array_filter(array_unique($groups));
    }

    function moodleGetConfig($name, $default = null)
    {
        if (empty($this->_config))
        {
            foreach ($this->getDb()->selectPage($total, "SELECT name, value FROM ?_config") as $p)
            {
                $this->_config[$p['name']] = $p['value'];
            }
        }
        return array_key_exists($name, $this->_config) && $this->_config[$name] ? $this->_config[$name] : $default;
    }

    function moodleRandomString($length = 15)
    {
        $pool = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pool .= 'abcdefghijklmnopqrstuvwxyz';
        $pool .= '0123456789';
        $poollen = strlen($pool);
        mt_srand((double) microtime() * 1000000);
        $string = '';
        for ($i = 0; $i < $length; $i++)
        {
            $string .= substr($pool, (mt_rand() % ($poollen)), 1);
        }
        return $string;
    }

    public function onSetupForms(Am_Event_SetupForms $event)
    {
        include_once 'Am_Form_Protect_Moodle.php';
        $f = new Am_Form_Protect_Moodle($this);
        if($plugin_readme = $this->getReadme())
        {
            $plugin_readme = str_replace(
                ['%root_url%', '%root_surl%', '%root_dir%'],
                [ROOT_URL, ROOT_SURL, ROOT_DIR],
                $plugin_readme);
            $f->addEpilog('<div class="info"><pre>'.$plugin_readme.'</pre></div>');
        }
        $event->addForm($f);
    }

    public function afterAddConfigItems($form)
    {
        parent::afterAddConfigItems($form);
        // additional configuration items for the plugin may be inserted here
        if ($this->getConfig('version') < 26) {
            $form->addText("protect.{$this->getId()}.salt", ['class' => 'am-el-wide'])
                ->setLabel("Password Salt\n"
                    . "applicable only for moodle version 2.5 and less. "
                    . "Copy/Paste value of password hash from moodle/config.php\n"
                    . "from the line starting \$CFG->passwordsaltmain without surrounding 'quotes'");
        }
        $form->addText("protect.{$this->getId()}.moodleurl", ['class' => 'am-el-wide'])
            ->setLabel("WWW Root\n"
                . "copy/Paste value of wwwroot from moodle/config.php\n"
                . "from the line starting \$CFG->wwwroot without surrounding 'quotes'")
            ->addRule('required');
        $form->addText("protect.{$this->getId()}.dataroot", ['class' => 'am-el-wide'])
            ->setLabel("DATA Root\n"
                . "copy/Paste value of dataroot from moodle/config.php\n"
                . "from the line starting \$CFG->dataroot without surrounding 'quotes'")
            ->addRule('required');
        $form->addSelect("protect.{$this->getId()}.language")
            ->loadOptions([
                'en' => 'English',
                'da' => 'Dansk',
                'pt' => 'Portuguese',
                'es' => 'Spanish',
                'el' => 'Greek',
                'fr' => 'French',
            ])
            ->setLabel('Default User Language');

        if ($this->getDi()->plugins_misc->isEnabled('avatar')) {
            $form->addAdvCheckbox("protect.{$this->getId()}.use_avatar")->setLabel(___("Add user's avatar from aMember to Moodle"));
        }

        $form->addSelect("protect.{$this->getId()}.version", '', ['options' => [26 =>'2.6 or higher', 25 => '2.5 and less']])
            ->setLabel('Moodle version');

        if ($this->getConfig('version') >= 26){
            $this->addProfileFieldsConfig($form);
        }
     }

     function addProfileFieldsConfig(Am_Form_Setup_ProtectDatabased $form)
     {
         if($this->isConfigured())
         {
             $plAttrs = $this->getDb()->selectCol("SELECT id AS ARRAY_KEY, name FROM ?_user_info_field");
             $amFields = [
                 "" => "-- Please Select --",
                 "login" => 'aMember: Username (login)',
                 "email" => 'aMember: E-Mail Address (email)',
                 "name_f" => 'aMember: First Name (name_f)',
                 "name_l" => 'aMember: Last Name (name_l)',
                 "street" => 'aMember: Street Address (street)',
                 "street2" => 'aMember: Street Address, Second Line (street2)',
                 "city" => 'aMember: City (city)',
                 "state" => 'aMember: State (state)',
                 "zip" => 'aMember: ZIP Code (zip)',
                 "country" => 'aMember: Country (country)',
                 "phone" => 'aMember: Phone Number (phone)',
             ];
             foreach ($this->getDi()->userTable->customFields()->getAll() as $fk => $fv) {
                 $amFields[$fk] = sprintf('aMember: %s (%s)', $fv->title, $fk);
             }

             $fs = $form->addFieldset('additional_settings')
                 ->setLabel(___('%s Additional Settings', $this->getTitle()));
             $gr = $fs->addGroup()
                 ->setLabel("Moodle Fields Mapping\n"
                     . "Moodle custom field <--> aMember custom field");

             foreach ($plAttrs as $fId => $fTitle) {
                 $gr->addText("protect.{$this->getId()}.field_map." . $fId, ['class' => 'field-map']);
             }

             $fieldMap = $gr->addElement(new Am_Form_Element_Moodle_FieldMap("protect___{$this->getId()}___field_map"));
             $cfg = $this->getConfig('field_map');
             foreach ($plAttrs as $fId => $fTitle)
             {
                 $val = $cfg[$fId] ?? '';
                 $sel = '<select name="" onChange="jQuery(this).closest(\'div\').find(\'input[type=hidden]\').val(jQuery(this).val())" style="margin:5px 0 0 10px">';
                 foreach ($amFields as $k => $f)
                 {
                     $t = ($k == $val) ? "selected='selected'" : "";
                     $sel .= sprintf('<option value="%s" %s>%s</option>',
                         $k, $t, $f);
                 }
                 $sel .= "</select>";

                 $attr = [
                     'data-label'=> 'Moodle: ' . $fTitle . $sel,
                     'data-value' => $val
                 ];
                 $fieldMap->addOption('Moodle: ' . $fTitle, $fId, $attr);
             }

             $fieldMap->setJsOptions('{
                getOptionName : function (name, option) {
                    return name.replace(/\[\]$/, "") + "___" + option.value;
                },
                getOptionValue : function (option) {
                    return jQuery(option).data("value");
                }
            }');
             $fieldMap->setValue(array_keys(array_filter($this->getConfig('field_map', []))));

             $form->addScript()
                 ->setScript(<<<CUT
jQuery('.field-map').hide();
jQuery('.field-map').val('');
CUT
                 );
         }
     }

    public function getPasswordFormat()
    {
        return ($this->getConfig('version') >= 26 ? SavedPassTable::PASSWORD_PASSWORD_HASH : self::MOODLE);
    }

    public function cryptPassword($pass, &$salt = null, User $user = null)
    {
        if($this->getConfig('version')>=26) {
            return parent::cryptPassword($pass, $salt, $user);
        } else {
            return md5($pass . $this->getConfig('salt'));
        }
    }

    public function createTable()
    {
        $table = new Am_Protect_Table_Moodle($this, $this->getDb(), '?_user', 'id');
        $table->setFieldsMapping([
            [':manual', 'auth'],
            [':1', 'confirmed'],
            [':1', 'policyagreed'],
            [":0", 'deleted'],
            [":" . $this->moodleGetConfig('mnet_localhost_id'), 'mnethostid'],
            [Am_Protect_Table::FIELD_LOGIN, 'username'],
            [Am_Protect_Table::FIELD_PASS, 'password'],
            [Am_Protect_Table::FIELD_NAME_F, 'firstname'],
            [Am_Protect_Table::FIELD_NAME_L, 'lastname'],
            [Am_Protect_Table::FIELD_EMAIL, 'email'],
            [Am_Protect_Table::FIELD_ADDED_STAMP, 'timecreated'],
            [Am_Protect_Table::FIELD_ADDED_STAMP, 'timemodified'],
            ['street', 'address'],
            ['city', 'city'],
            ['country', 'country'],
            [':' . $this->moodleRandomString(15), 'secret'],
            [':' . $this->getConfig('language', 'en'), 'lang'],
            [[$this, 'isSuspended'], 'suspended'],
            [':' . $this->getConfig('defaultpreference_maildisplay', '0'), 'maildisplay'],
        ]);
        return $table;
    }

    function isSuspended($user, $record, $table)
    {
        return $user->is_locked > 0 ? 1 : 0;
    }

    public function getAvailableUserGroupsSql()
    {
        return "SELECT
            id as id,
            fullname as title,
            NULL as is_banned, #must be customized
            NULL as is_admin # must be customized
            FROM ?_course
            WHERE id <>1";
    }

    public function getAvailableUserGroups()
    {
        $groups = parent::getAvailableUserGroups();
        array_unshift($groups, new Am_Protect_Databased_Usergroup(['id'=>-1, 'title' => 'Add user to Moodle but do not enroll in course']));
        return $groups;
    }

    public function getSessionCookieName()
    {

    }

    function getUserIp()
    {
        return $this->getDi()->request->getClientIp();
    }

    function moodleRc4Encrypt($str)
    {
        return $this->moodle_Endecrypt('nfgjeingjk', $str, '');
    }

    function moodle_Endecrypt($pwd, $data, $case)
    {
        if ($case == 'de') {
            $data = urldecode($data);
        }

        $key[] = '';
        $box[] = '';
        $temp_swap = '';
        $pwd_length = 0;

        $pwd_length = strlen($pwd);

        for ($i = 0; $i <= 255; $i++) {
            $key[$i] = ord(substr($pwd, ($i % $pwd_length), 1));
            $box[$i] = $i;
        }

        $x = 0;

        for ($i = 0; $i <= 255; $i++) {
            $x = ($x + $box[$i] + $key[$i]) % 256;
            $temp_swap = $box[$i];
            $box[$i] = $box[$x];
            $box[$x] = $temp_swap;
        }

        $temp = '';
        $k = '';

        $cipherby = '';
        $cipher = '';

        $a = 0;
        $j = 0;

        for ($i = 0; $i < strlen($data); $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $temp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $temp;
            $k = $box[(($box[$a] + $box[$j]) % 256)];
            $cipherby = ord(substr($data, $i, 1)) ^ $k;
            $cipher .= chr($cipherby);
        }

        if ($case == 'de') {
            $cipher = urldecode(urlencode($cipher));
        } else {
            $cipher = urlencode($cipher);
        }

        return $cipher;
    }

    function getAdditionalCookie(Am_Record $record)
    {
        return $this->moodleRc4Encrypt($record->username);
    }

    function moodleGetCoursesContextId(Am_Record $record)
    {
        $contexts = $this->getDb()->selectCol('SELECT t2.id
            FROM ?_role_assignments AS t1 LEFT JOIN ?_context AS t2 ON t1.contextid = t2.id
            WHERE t1.userid = ? AND t2.contextlevel=?', $record->pk(), Am_Protect_Table_Moodle::contextlevel);
        $contexts = array_unique($contexts);
        return $contexts;
    }

    function moodleGetCaps($role_id)
    {
        $caps = [];
        foreach ($this->getDb()->selectPage($total, "
            SELECT *
            FROM ?_role_capabilities
            WHERE roleid=? AND contextid=1 ORDER BY capability", $role_id) as $cap)
        {

            $caps[$cap['capability']] = $cap['permission'];
        }
        return $caps;
    }

    function createSessionData(Am_Record $record, Am_Protect_SessionTable_Record $session)
    {
        $sessobj = new stdClass();
        $sessobj->cal_course_referer = 1;
        $sessobj->cal_show_global = true;
        $sessobj->cal_show_groups = true;
        $sessobj->cal_show_course = true;
        $sessobj->cal_show_user = true;
        $sessobj->cal_users_shown = $record->username;
        $sessobj->logincount = 0;

        $student_caps = $this->moodleGetCaps(Am_Protect_Table_Moodle::defaultroleid);
        $site_caps = $this->moodleGetCaps($this->moodleGetConfig('notloggedinroleid'));
        unset($site_caps['moodle/course:view']);
        unset($site_caps['moodle/legacy:guest']);

        $courses_caps = [];
        foreach ($this->moodleGetCoursesContextId($record) as $context_id)
        {
            $courses_caps[intval($context_id)] = $student_caps;
        }
        $courses_caps[1] = $site_caps;

        $userobj = new stdClass();
        foreach (get_object_vars($record) as $k => $v)
        {
            $userobj->{$k} = $v;
        }
        $userobj->lastaccess = time() + 1;
        $userobj->lastlogin = time();
        $userobj->currentlogin = time();
        $userobj->loggedin = true;
        $userobj->site = $this->getConfig('moodleurl');
        $userobj->sesskey = $this->moodleRandomString(10);
        $userobj->sessionIP = $this->getDi()->request->getClientIp();
        $userobj->capabilities = $courses_caps;

        $sess = 'SESSION|' . serialize($sessobj) . 'USER|' . serialize($userobj);

        return base64_encode($sess);
    }

    public function createSessionTable()
    {
        $table = new Am_Protect_SessionTable_Moodle($this, $this->getDb(), '?_sessions', 'sid');

        $sessCookieName = $this->moodleGetConfig('sessioncookie', '');

        $config = [
            Am_Protect_SessionTable::FIELD_SID => 'sid',
            Am_Protect_SessionTable::FIELD_UID => 'userid',
            Am_Protect_SessionTable::FIELD_CREATED => 'timecreated',
            Am_Protect_SessionTable::FIELD_CHANGED => 'timemodified',
            Am_Protect_SessionTable::FIELD_IP => 'firstip',
            Am_Protect_SessionTable::SESSION_COOKIE => 'MoodleSession' . $sessCookieName,
            Am_Protect_SessionTable::FIELDS_ADDITIONAL => [
                'state' => 0,
                'lastip' => [$this, 'getUserIp'],
                'sessdata' => [$this, 'createSessionData']
            ],
            Am_Protect_SessionTable::COOKIE_PARAMS => [
                Am_Protect_SessionTable::COOKIE_PARAM_EXPIRES => time()+$this->moodleGetConfig('sessiontimeout'),
                Am_Protect_SessionTable::COOKIE_PARAM_PATH => $this->moodleGetConfig('sessioncookiepath', '/'),
                Am_Protect_SessionTable::COOKIE_PARAM_DOMAIN => $_SERVER['HTTP_HOST'],
            ],
            Am_Protect_SessionTable::COOKIES_ADDITIONAL => [
                'MOODLEID1_'.$sessCookieName => [$this, 'getAdditionalCookie']
            ]
        ];
        $table->setTableConfig($config);
        return $table;
    }

    function isUseAvatar()
    {
        return $this->getConfig('use_avatar')
            && $this->getConfig('dataroot')
            && is_dir($this->getConfig('dataroot'));
    }

    function getAvatar($user, $record)
    {
        if (empty($user->avatar)) {
            $this->delete_avatar_if_exists($record);
            $user->data()->set("{$this->getId()}.avatar", null)->update();
            return 0;
        } else {
            if ($user->data()->get("{$this->getId()}.avatar") == $user->avatar) {
                return $record->picture;
            } else {
                $this->delete_avatar_if_exists($record);
                $user->data()->set("{$this->getId()}.avatar", $user->avatar)->update();
                return $this->create_avatar($user->avatar, $record);
            }
        }
    }

    protected function hash_from_string($content)
    {
        return sha1($content);
    }

    protected function pathname_hash($contextid, $filename)
    {
        return $this->get_pathname_hash($contextid, 'user', 'icon', 0, '/', $filename);
    }

    protected function get_pathname_hash($contextid, $component, $filearea, $itemid, $filepath, $filename)
    {
        return sha1("/$contextid/$component/$filearea/$itemid".$filepath.$filename);
    }

    protected function get_contentdir_from_hash($contenthash)
    {
        $l1 = substr($contenthash, 0, 2);
        $l2 = substr($contenthash, 2, 2);
        return "$l1/$l2";
    }

    protected function get_full_path_by_hash($contenthash)
    {
        $filedir = rtrim($this->getConfig('dataroot'), '/') . '/filedir';
        $dirpath = $filedir . '/' . $this->get_contentdir_from_hash($contenthash);
        return "$dirpath/$contenthash";
    }

    protected function delete_avatar_if_exists($record)
    {
        $context_id = $this->get_user_context_id($record);
        foreach ($this->getDb()->select("SELECT * FROM ?_files WHERE contextid=? AND filearea=? AND component=?", $context_id, 'icon', 'user') as $f) {
            if ($f->filesize > 0) {
                @unlink($this->get_full_path_by_hash($f->contenthash));
            }
        }
        $this->getDb()->query("DELETE FROM ?_files WHERE contextid=? AND filearea=?", $context_id, 'icon');
    }

    protected function create_avatar($id, $record)
    {
        $filedir = rtrim($this->getConfig('dataroot'), '/') . '/filedir';
        if (!file_exists($filedir)) {
            return 0;
        }

        $pictureid = null;
        $context_id = $this->get_user_context_id($record);
        $upload = $this->getDi()->uploadTable->load($id);

        $sizes = [
            'f1' => [
                'w' => 100,
                'h' => 100,
                't' => Am_Image::RESIZE_CROP,
            ],
            'f2' => [
                'w' => 35,
                'h' => 35,
                't' => Am_Image::RESIZE_CROP,
            ],
            'f3' => [
                'w' => 512,
                'h' => 512,
                't' => Am_Image::RESIZE_CROP,
            ],
        ];
        foreach ($sizes as $prefix => $def) {
            $i = new Am_Image($upload->getFullPath(), $upload->getType());
            $content = $i->resize($def['w'], $def['h'], $def['t'])->data('image/png');
            $contenthash = $this->hash_from_string($content);

            $dirpath = $filedir . '/' . $this->get_contentdir_from_hash($contenthash);
            if (!is_dir($dirpath)) {
                mkdir($dirpath, 0777, true);
            }
            $filesize = strlen($content);
            file_put_contents("$dirpath/$contenthash", $content);

            $filename = "{$prefix}.png";
            $this->getDb()->query("INSERT INTO ?_files SET ?a", [
                'contenthash' => $contenthash,
                'pathnamehash' => $this->pathname_hash($context_id, $filename),
                'contextid' => $context_id,
                'component' => 'user',
                'filearea' => 'icon',
                'itemid' => 0,
                'filepath' => '/',
                'filename' => $filename,
                'userid' => null,
                'filesize' => $filesize,
                'mimetype' => 'image/png',
                'status' => 0,
                'source' => null,
                'author' => null,
                'license' => null,
                'timecreated' => time(),
                'timemodified' => time(),
                'sortorder' => 0,
                'referencefileid' => null,
            ]);
            if (empty($pictureid)) {
                $pictureid = $this->getDb()->selectCell("SELECT LAST_INSERT_ID();");
            }
        }

        $this->getDb()->query("INSERT INTO ?_files SET ?a", [
            'contenthash' => $this->hash_from_string(''),
            'pathnamehash' => $this->pathname_hash($context_id, '.'),
            'contextid' => $context_id,
            'component' => 'user',
            'filearea' => 'icon',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => '.',
            'userid' => null,
            'filesize' => 0,
            'mimetype' => null,
            'status' => 0,
            'source' => null,
            'author' => null,
            'license' => null,
            'timecreated' => time(),
            'timemodified' => time(),
            'sortorder' => 0,
            'referencefileid' => null,
        ]);

        return $pictureid;
    }

    protected function get_user_context_id($record)
    {
        return $this->getDb()->selectCell("SELECT id FROM ?_context WHERE contextlevel=? AND instanceid=?", 30, $record->id);
    }
}

class Am_Protect_SessionTable_Moodle extends Am_Protect_SessionTable
{
    function sessionIsValid(Am_Record $session)
    {
        // If session have different User Agent or IP  do not login user into aMember.
        if (!is_null($this->_ua)
            && $session->get($this->_ua)
            && ($session->get($this->_ua) != $this->getUserAgent())) {

            return false;
        }

        return true;
    }
}

class Am_Protect_Table_Moodle extends Am_Protect_Table
{
    const contextlevel = 50;
    const defaultroleid = 5;

    private $_courses = [];
    private $_courseContext = [];

    function __construct(Am_Protect_Databased $plugin, $db = null, $table = null, $recordClass = null, $key = null)
    {
        parent::__construct($plugin, $db, $table, $recordClass, $key);
    }

    function getGroups(Am_Record $record)
    {
        $courses = $this->getPlugin()->getDb()->selectCol("
                        SELECT t2.instanceid
                        FROM ?_role_assignments AS t1
						LEFT JOIN ?_context AS t2
                        ON t1.contextid = t2.id
                        WHERE t1.userid = ? AND t2.contextlevel=?", $record->pk(), self::contextlevel);
        $courses = array_unique($courses);
        return $courses;
    }

    function moodleGetCourse($course_id)
    {
        if (!$this->_courses)
        {
            foreach ($this->getPlugin()->getDb()->selectPage($total, "SELECT * from ?_course") as $c)
            {
                $this->_courses[$c['id']] = $c;
            }
        }
        return $this->_courses[$course_id];
    }

    function moodleGetCourseRoleId($course)
    {
        if ($course['defaultrole']) {
            return $course['defaultrole'];
        } elseif ($roleid = $this->getPlugin()->moodleGetConfig('defaultcourseroleid')) {
            return $roleid;
        } else {
            return self::defaultroleid;
        }
    }

    function moodleQueryContextId($course_id)
    {
        return $this->getPlugin()->getDb()->selectCell('
            SELECT id
            FROM ?_context
            WHERE contextlevel=? AND instanceid=?
            ', self::contextlevel, $course_id);
    }

    function moodleGetCourseContextId($course_id)
    {
        if (array_key_exists($course_id, $this->_courseContext))
            return $this->_courseContext[$course_id];

        $context_id = $this->moodleQueryContextId($course_id);

        if (!$context_id)
            $this->getPlugin()->getDb()->query('INSERT INTO ?_context SET contextlevel=?, instanceid=?', self::contextlevel, $course_id);

        $this->_courseContext[$course_id] = $this->moodleQueryContextId($course_id);
        return $this->_courseContext[$course_id];
    }

    function moodleGetEnrolMethodId($course_id)
    {
        $method_id = $this->getPlugin()->getDb()->selectCell('
            SELECT id
            FROM ?_enrol
            WHERE status=0 AND enrol="manual" AND courseid=?', $course_id);
        return $method_id ? $method_id : 0;
    }

    function moodleAssignStudentRole(Am_Record $record, $course_id)
    {
        $course = $this->moodleGetCourse($course_id);

        $role_id = $this->moodleGetCourseRoleId($course);
        $context_id = $this->moodleGetCourseContextId($course_id);
        $enrol_id = $this->moodleGetEnrolMethodId($course_id);

        $this->getPlugin()->getDb()->query('
            INSERT INTO ?_role_assignments
            SET
            roleid=?, contextid=?, userid=?,
            timemodified=?, modifierid=0, itemid=0, sortorder=0
            ', $role_id, $context_id, $record->pk(), time());

        $this->getPlugin()->getDb()->query('
            INSERT IGNORE INTO ?_user_enrolments SET
            enrolid = ?,
            userid = ?,
            timecreated = ?,
            timestart = ?,
            timemodified = ?,
            modifierid = 0
            ', $enrol_id, $record->pk(), time(), time(), time() + 6);


        $this->moodleAddLog($record->pk(), $course_id, 'course', 'enrol', 'view.php?id=' . $course_id);
    }

    function moodleAddLog($user_id, $course, $module, $action, $url)
    {
        $this->getPlugin()->getDb()->query('
           INSERT INTO ?_log SET
           time = ?,
           userid = ?,
           ip = ?,
           course = ?,
           module = ?,
           cmid = 0,
           action = ?,
           url = ?,
           info = ?
        ', time(), $user_id, $this->getDi()->request->getClientIp(), $course, $module, $action, $url, $user_id);
    }

    function moodleUnassignStudentRole(Am_Record $record, $course_id)
    {
        $course = $this->moodleGetCourse($course_id);
        $role_id = $this->moodleGetCourseRoleId($course);
        $context_id = $this->moodleGetCourseContextId($course_id);
        $enrol_id = $this->moodleGetEnrolMethodId($course_id);

        $this->getPlugin()->getDb()->query("
            DELETE FROM ?_role_assignments
            WHERE roleid = ? AND contextid =? AND userid = ?
            ", $role_id, $context_id, $record->pk());

        $this->getPlugin()->getDb()->query("
            DELETE FROM ?_user_enrolments WHERE userid = ? and enrolid=?
            ", $record->pk(), $enrol_id);
    }

    function setGroups(Am_Record $record, $groups)
    {
        $oldGroups = $this->getGroups($record);

        $added = array_unique(array_diff($groups, $oldGroups));

        $deleted = array_unique(array_diff($oldGroups, $groups));
        if (!empty($added)) {
            foreach ($added as $course_id) {
                if ($course_id && $course_id > 0) {
                    $this->moodleAssignStudentRole($record, $course_id);
                }
            }
        }

        if (!empty($deleted)) {
            foreach ($deleted as $course_id) {
                if ($course_id && $course_id > 0) {
                    $this->moodleUnassignStudentRole($record, $course_id);
                }
            }
        }
        if ($_u = $this->findAmember($record)) {
            $this->updateOriginalGroups($record, $_u);
        }
    }

    function getOriginalGroups(Am_Record $record)
    {
        return $this->_db->selectCol('SELECT groupid FROM ?_groups_members WHERE userid=?', $record->pk());
    }

    function updateOriginalGroups(Am_Record $record, User $user)
    {
        $oldGroups = $this->getOriginalGroups($record);
        $newGroups = $this->getPlugin()->calculateOriginalGroups($user);
        $configuredGroups = $this->getPlugin()->getConfiguredOriginalGroups();

        $added = array_unique(array_diff($newGroups, $oldGroups));
        $deleted = array_intersect(array_unique(array_diff($oldGroups, $newGroups)), $configuredGroups);

        if ($deleted) {
            $this->_db->query("DELETE FROM ?_groups_members  WHERE userid=? AND groupid IN (?a)", $record->pk(),
                $deleted);
        }

        if ($added) {
            foreach ($added as $g) {
                $this->_db->query("
                    INSERT INTO ?_groups_members
                    (userid, groupid, timeadded)
                    VALUES
                    (?, ?, now())", $record->pk(), $g);
            }
        }
    }

    function insertFromAmember(User $user, SavedPass $pass, $groups)
    {
        $record = parent::insertFromAmember($user, $pass, $groups);
        if ($this->getPlugin()->isUseAvatar()) {
            if ($avatar = $this->getPlugin()->getAvatar($user, $record)) {
                $record->updateQuick('picture', $avatar);
            }
        }
        $this->updateCustomFields($user, $record);
        return $record;
    }

    function updateFromAmember(Am_Record $record, User $user, $groups)
    {
        if ($this->getPlugin()->isUseAvatar()) {
            $record->picture = $this->getPlugin()->getAvatar($user, $record);
        }
        parent::updateFromAmember($record, $user, $groups);
        $this->updateCustomFields($user, $record);
    }

    function updateCustomFields(User $user, Am_Record $record)
    {
        if($this->getPlugin()->getConfig('version')>=26)
        {
            $map = (array)$this->getPlugin()->getConfig('field_map');
            foreach($map as $mField=>$aField)
            {
                $aValue = $user->{$aField} ?? $user->data()->get($aField);

                if(!is_null($aValue))
                    $this->_db->query("INSERT INTO ?_user_info_data (userid, fieldid, `data`) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `data`=?",
                            $record->pk(), $mField, $aValue, $aValue
                        );
            }
        }
    }
}

class Am_Form_Element_Moodle_FieldMap extends Am_Form_Element_MagicSelect
{
    function  getValue()
    {
        return null;
    }
}
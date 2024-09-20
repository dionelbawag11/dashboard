<?php
/**
 * @table integration
 * @id vanilla
 * @title Vanilla Integration
 * @visible_link http://vanillaforums.org/
 * @description Vanilla is an open-source, standards-compliant, multi-lingual,
 * fully extensible discussion forum for the web. Anyone who has web-space
 * that meets the requirements  can download and use Vanilla for free.
 * @different_groups 1
 * @single_login 1
 * @type Bulletin Boards
 * @am_protect_api 6.0
 */
class Am_Protect_Vanilla extends Am_Protect_Databased
{
    const PLUGIN_STATUS = self::STATUS_PRODUCTION;
    const PLUGIN_REVISION = '6.3.20';

    protected $guessTablePattern = 'User';
    protected $guessFieldsPattern = [
        'UserID', 'Name', 'Password', 'HashMethod','DateFirstVisit',
    ];

    protected $groupMode = self::GROUP_MULTI;
    protected $versions = [2 => '2.x', 3 => '3.1'];
    protected $defaultVersion = 2;

    public function getGroupOptions()
    {
        try {
            $groups = $this->getAvailableUserGroups();
        } catch (Am_Exception_Db $e){ // to avoid errors while db is not yet configured
            $groups = [];
        }
        $options = [];
        foreach ($groups as $g)
        {
            $options[ $g->getId() ] = $g->getTitle();
        }
        return $options;
    }

    public function afterAddConfigItems($form)
    {
        parent::afterAddConfigItems($form);
        $form->addText("protect.{$this->getId()}.cookie_salt")
            ->setLabel($this->getTitle()." Cookie Salt\n" .
                        "\$Configuration['Garden']['Cookie']['Salt'] setting from Vanilla config");
        $form->addText("protect.{$this->getId()}.cookie_hash_method")
            ->setLabel($this->getTitle()." Cookie Hash Method\n" .
                        "\$Configuration['Garden']['Cookie']['HashMethod'] setting from Vanilla config");
        $form->addText("protect.{$this->getId()}.cookie_name")
            ->setLabel($this->getTitle()." Cookie Name\n" .
                        "\$Configuration['Garden']['Cookie']['Name'] setting from Vanilla config");
        $form->addMagicSelect("protect.{$this->getId()}.sticky_groups")
            ->setLabel("Sticky Groups\n" .
                "aMember will never remove these groups from user account")
            ->loadOptions($this->getGroupOptions());
        $form->addSelect("protect.{$this->getId()}.version", '', ['options' => $this->versions, 'default' => $this->defaultVersion])
            ->setLabel('Vanilla version');
    }

    public function getPasswordFormat()
    {
        return ($this->getConfig('version', $this->defaultVersion) == 2 ? SavedPassTable::PASSWORD_PHPASS : SavedPassTable::PASSWORD_PASSWORD_HASH);
    }

    public function createCookieValue($record, $exp=0,$time = 0)
    {
        if(!$exp) $exp = time() + 60*60*24*2; // 2 days
        $key_data = $record->UserID.'-'.$exp;

        $key  = $this->HashHMAC($key_data, $this->config['cookie_salt']);
        $hash = $this->HashHMAC($key_data, $key);
        $value = implode('|', [$key_data, $hash, ($time ? $time : time()), $record->UserID, $exp]);
        return $value;
    }

    public function loginUser(Am_Record $record, $password)
    {
        Am_Cookie::set($this->config['cookie_name'], $this->createCookieValue($record), 0, '/',$this->getDi()->request->getHttpHost());
        Am_Cookie::set($this->config['cookie_name']."-Volatile", $this->createCookieValue($record), 0, '/',$this->getDi()->request->getHttpHost());
    }

    public function logoutUser(User $user)
    {
        if(!$this->config['cookie_name']) return;
        Am_Cookie::set($this->config['cookie_name'], "", time()-36000, '/', $this->getDi()->request->getHttpHost());
        Am_Cookie::set($this->config['cookie_name']."-Volatile", "", time()-36000, '/', $this->getDi()->request->getHttpHost());
    }

    public function getLoggedInRecord()
    {
        if (!$this->config['cookie_name']) return;
        $cookie = @$_COOKIE [$this->config['cookie_name']];
        if (!$cookie) return;
        $vars = explode("\|", $cookie);
        if(!($user_id = (int)@$vars[3]))
            return;
        $exp = $vars[4];$time = $vars[2];
        $record = $this->getTable()->load($user_id, false);
        if (!$record) return;
        if($cookie != $this->createCookieValue($record, $exp, $time)) return;
        return $record;
    }

    function HashHMAC($Data, $Key)
    {
        $PackFormats = ['md5' => 'H32', 'sha1' => 'H40'];
        $HashMethod = $this->config['cookie_hash_method'];
        if (!isset($PackFormats[$HashMethod]))
            throw new Am_Exception("Cookie hash method in not defined in ".$this->getTitle());

        $PackFormat = $PackFormats[$HashMethod];
        // this is the equivalent of "strlen($Key) > 64":
        if (isset($Key[63])){
            //$Key = pack($PackFormat, $HashMethod($Key));
            switch ($HashMethod){
                case 'md5':
                    $Key = pack($PackFormat, md5($Key));
                    break;
                case 'sha1':
                    $Key = pack($PackFormat, sha1($Key));
                    break;
            }
        } else {
            $Key = str_pad($Key, 64, chr(0));
        }

        $InnerPad = (substr($Key, 0, 64) ^ str_repeat(chr(0x36), 64));
        $OuterPad = (substr($Key, 0, 64) ^ str_repeat(chr(0x5C), 64));

        $hash = '';
        switch ($HashMethod){
            case 'md5':
                $hash = md5($OuterPad . pack($PackFormat, md5($InnerPad . $Data)));
                break;
            case 'sha1':
                $hash = sha1($OuterPad . pack($PackFormat, sha1($InnerPad . $Data)));
                break;
        }
        return $hash;
    }

    public function parseExternalConfig($path)
    {
        if(!is_file($config_path = $path."/conf/config.php") ||
           !is_file($default_config_path = $path."/conf/config-defaults.php") ||
           !is_readable($config_path) ||!is_readable($default_config_path))
            throw new Am_Exception_InputError("Specified path is not a valid ".$this->getTitle()." installation");

        // Try to include config
        define('APPLICATION', 1);
        define('PATH_CACHE', '');

        include $default_config_path;
        include $config_path;

        if(!is_array($Configuration))
            throw new Am_Exception_InputError("Specified path is not a valid ".$this->getTitle()." installation");

        // Now set config fields as required by aMember;
        return [
            'db' => $Configuration['Database']['Name'],
            'prefix' => $Configuration['Database']['DatabasePrefix'],
            'host' => $Configuration['Database']['Host'],
            'user' => $Configuration['Database']['User'],
            'pass' => $Configuration['Database']['Password'],
            'cookie_salt' => $Configuration['Garden']['Cookie']['Salt'],
            'cookie_name' => $Configuration['Garden']['Cookie']['Name'],
            'cookie_hash_method' => $Configuration['Garden']['Cookie']['HashMethod']
        ];
    }

    public function  getAvailableUserGroupsSql()
    {
        return "SELECT RoleID as id, Name as title,
                (RoleID in (32,16)) as is_admin,
                (RoleID in (1)) as is_banned
                from ?_Role";
    }

    public function createTable()
    {
        $table = new Am_Protect_Table_Vanilla($this, $this->getDb(), '?_User', 'UserID');
        $table->setFieldsMapping([
            [Am_Protect_Table::FIELD_LOGIN, 'Name'],
            [Am_Protect_Table::FIELD_EMAIL, 'Email'],
            [Am_Protect_Table::FIELD_PASS, 'Password'],
            [Am_Protect_Table::FIELD_ADDED_SQL, 'DateInserted']
        ]);
        $table->setGroupsTableConfig([
            Am_Protect_Table::GROUP_TABLE => '?_UserRole',
            Am_Protect_Table::GROUP_GID => 'RoleID',
            Am_Protect_Table::GROUP_UID => 'UserID'
        ]);
        return $table;
    }
}

class Am_Protect_Table_Vanilla extends Am_Protect_Table
{
    protected function _setTableGroups(Am_Record $record, $groups)
    {
        $table = $this->_groupTableConfig[Am_Protect_Table::GROUP_TABLE];
        $uidField = $this->_groupTableConfig[Am_Protect_Table::GROUP_UID];
        $gidField = $this->_groupTableConfig[Am_Protect_Table::GROUP_GID];
        $fields = empty($this->_groupTableConfig[Am_Protect_Table::GROUP_ADDITIONAL_FIELDS]) ?
            [] :   $this->_groupTableConfig[Am_Protect_Table::GROUP_ADDITIONAL_FIELDS];
        // update groups
        $oldGroups = $this->getGroups($record);
        $added = array_unique(array_diff($groups, $oldGroups));
        $deleted = array_unique(array_diff($oldGroups, $groups, array_filter(array_map('trim', $this->getPlugin()->getConfig('sticky_groups', [])))));
        if ($deleted)
            $this->_db->query("DELETE FROM $table WHERE $uidField=? AND $gidField IN (?a)", $record->pk(), $deleted);
        $sql = [];
        $fk = $fv = '';
        if(!empty($fields)){
            $field_keys = array_keys($fields);
            $field_values = array_values($fields);
            array_walk($field_values, function(&$value, $key, $db) {$value = $db->escape($value);}, $this->_db);
            if(!empty($field_keys)){
                $fk = ", ".join(", ", $field_keys);
                $fv = ", ".join(", ", $field_values);
            }
        }
        foreach ($added as $group)
            if(!is_null($group)) $sql[] = "('$group', " . $record->pk() . $fv.")";
        if ($sql)
            $this->_db->query("INSERT INTO $table ($gidField, $uidField$fk) VALUES " . join(",", $sql));
    }

    public function insertFromAmember(User $user, SavedPass $pass, $groups)
    {
        $record = parent::insertFromAmember($user, $pass, $groups);
        $record->updateQuick('Permissions', null);
    }

    function updateFromAmember(Am_Record $record, User $user, $groups)
    {
        parent::updateFromAmember($record, $user, $groups);
        $record->updateQuick('Permissions', null);
    }
}
<?php

/**
 * Class represents records from table user
 * {autogenerated}
 * @property int $user_id
 * @property string $login
 * @property string $pass
 * @property string $email
 * @property string $name_f
 * @property string $name_l
 * @property string $street
 * @property string $street2
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $country
 * @property string $phone
 * @property datetime $added
 * @property string $remote_addr
 * @property int $status
 * @property int $unsubscribed
 * @property int $email_confirmed
 * @property int $phone_confirmed
 * @property datetime $email_confirmation_date
 * @property string $security_code
 * @property datetime $securitycode_expire
 * @property string $lang
 * @property int $i_agree
 * @property int $is_approved
 * @property int $is_locked
 * @property int $reseller_id
 * @property string $comment
 * @property int $aff_id
 * @property int $is_affiliate
 * @property string $aff_payout_type
 * @property int $aff_points
 * @property int $aff_points_total
 * @see Am_Table
 */
class User extends Am_Record_WithData
{
    /** @var string available after call to setPass() only */
    protected $_plaintextPass;
    /** @var bool if the password was changed, additional event handlers will be called after save */
    protected $_passwordChanged = false;
    /** @var bool true if password has been generated */
    protected $_passwordGenerated = false;

    protected $_groups = [];

    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_EXPIRED = 2;
    const NEED_SESSION_REFRESH = 'need_session_refresh';

    function __get($name)
    {
        if ($name == 'unsubscribe_link')
            return $this->getDi()->unsubscribeLink->get($this->email);

        return null;
    }

    function __isset($name)
    {
        return ($name == 'unsubscribe_link');
    }

    function delete()
    {
        $this->_delete = true;
        if ($this->user_id <= 0)
            throw new Am_Exception_InternalError('Could not delete user, user_id is null');

        if ($this->getDi()->invoiceTable->countBy(['user_id' => $this->pk(), 'status' => Invoice::RECURRING_ACTIVE])) {
            throw new Am_Exception_InputError('User has active recurring subscriptions. It is necessary to cancel all recurring subscription before delete user account.');
        }

        $this->getDi()->hook->call(Am_Event::USER_BEFORE_DELETE, ['user' => $this]);
        $this->getDi()->invoiceTable->deleteByUserId($this->user_id);
        $this->getDi()->accessTable->deleteByUserId($this->user_id);
        $this->getDi()->addressTable->deleteByUserId($this->user_id);
        $this->checkSubscriptions(false);
        $this->getDi()->hook->call(new Am_Event_SubscriptionRemoved($this));
        parent::delete();
        foreach (['?_access_log',
             '?_access_cache',
             '?_user_status',
             '?_user_user_group',
             '?_saved_pass',
             '?_user_consent',
             '?_user_note',
             '?_user_delete_request',
            ] as $table) {
            $this->getAdapter()->query("DELETE FROM $table WHERE user_id=?", $this->user_id);
        }
        $this->getDi()->couponBatchTable->deleteByUserId($this->user_id);
        foreach ($this->getDi()->uploadTable->findBy([
            'prefix' => Am_CustomFieldUpload::UPLOAD_PREFIX,
            'user_id' => $this->user_id
        ]) as $upload) {

            $upload->delete();
        }
        $this->getDi()->hook->call(new Am_Event_UserAfterDelete($this));
    }

    function insert($reload = true)
    {
        if (!isset($this->is_approved))
            $this->is_approved = !$this->getDi()->config->get('manually_approve');
        if (empty($this->remote_addr))
            $this->remote_addr = htmlentities(@$_SERVER['REMOTE_ADDR']);
        if (empty($this->user_agent))
            $this->user_agent = @$_SERVER['HTTP_USER_AGENT'];
        if (empty($this->added))
            $this->added = $this->getDi()->sqlDateTime;

        $this->getDi()->hook->call(new Am_Event_UserBeforeInsert($this));
        $ret = parent::insert($reload);
        if ($this->_passwordChanged) {
            $event = new Am_Event_SetPassword($this, $this->getPlaintextPass());
            $this->getDi()->savedPassTable->setPass($event);
            $this->getDi()->hook->call($event);
        }
        if ($this->_passwordGenerated) {
            $c = new Am_Crypt_Aes128($this->getDi()->security->siteKey());
            $pg = $c->encrypt($this->getPlaintextPass());

            $this->getDi()->store->set('pass-generated-' . $this->pk(), $pg, '+6 hours');
        }
        $this->getDi()->hook->call(new Am_Event_UserAfterInsert($this));
        $this->_passwordChanged = false;
        $this->getDi()->db->query("DELETE FROM ?_store WHERE name LIKE ? AND `value`=?",
            'signup_record-%', $this->email);
        if (!empty($this->_groups)) {
            $this->setGroups($this->_groups);
        }
        return $ret;
    }

    function update()
    {
        $oldU = new stdclass;
        $oldU->user_id = null;
        if ($this->getDi()->hook->have([
                'userBeforeUpdate', 'userAfterUpdate', 'subscriptionUpdated'
            ])
            || $this->getDi()->config->get('manually_approve')
            || $this->getDi()->config->get('enable-otp')
        ) { // do loading only if hooks are set
            if ($oldU = $this->getTable()->load($this->user_id, false)) {
                $oldU->data()->load();
            }
            if (!$oldU)
                $oldU = new self($this->getTable()); // avoid errors here
            $this->getDi()->hook->call(new Am_Event_UserBeforeUpdate($this, $oldU));

            if($this->getMobile() != $oldU->getMobile())
            {
                $this->mobile_confirmed = false;
                $this->mobile_confirmation_date = null;
            }

            if($this->email != $oldU->email)
            {
                $this->email_confirmed = false;
                $this->email_confirmation_date = null;
            }

        }
        $ret = parent::update();

        if ($this->_passwordChanged) {
            $this->data()->set(self::NEED_SESSION_REFRESH, true)->update();
            $event = new Am_Event_SetPassword($this, $this->getPlaintextPass());
            $this->getDi()->savedPassTable->setPass($event);
            $this->getDi()->hook->call($event);
            $this->sendChangepassEmail();
        }

        if ($oldU->user_id) {
            if ($this->is_approved && !$oldU->is_approved)
                $this->approve();

            $this->getDi()->hook->call(new Am_Event_UserAfterUpdate($this, $oldU));
        }
        if ($this->status && $oldU->user_id) {
            $this->getDi()->hook->call(new Am_Event_SubscriptionUpdated($this, $oldU));
        }
        $this->_passwordChanged = false;
        return $ret;
    }

    function approve()
    {
        foreach ($this->getDi()->invoiceTable->findByUserId($this->pk()) as $invoice)
            $invoice->approve();

        if (!$this->is_approved) {
            $this->is_approved = 1;
            $this->save();
        }
        $this->refresh();

        $this->sendSignupEmailIfNecessary();
    }

    protected function _prepareForSet(&$vars)
    {
        if (isset($vars['pass']))
            unset($vars['pass']);
        return parent::_prepareForSet($vars);
    }

    /**
     * @param string submitted password
     * @return bool true if ok, false if not
     */
    function checkPassword($pass)
    {
        if (!strlen($pass) ||
            !isset($this->pass) ||
            !strlen($this->pass)) {
            return false;
        }
        $ph = new PasswordHash(8, true);
        return $ph->CheckPassword($pass, $this->pass);
    }

    /**
     * Set new password
     * (important! - it does not save the password!)
     */
    function setPass($pass, $quick = false)
    {
        $this->_plaintextPass = $pass;
        $this->_passwordChanged = true;
        $this->pass = self::cryptPass($pass, $quick);
        $this->pass_dattm = $this->getDi()->sqlDateTime;
        $this->data()->set('force_change_password', null);
        return $this;
    }

    static function cryptPass($pass, $quick = false)
    {
        $ph = new PasswordHash($quick ? 4 : 8, true);
        return $ph->HashPassword($pass);
    }

    /** It is only exists after call of @method setPass() */
    function getPlaintextPass()
    {
        return $this->_plaintextPass;
    }

    function getSavedPass()
    {
        return $this->getDi()->savedPassTable->findByUserId($this->pk());
    }

    /**
     * Load generated password in plain-text format
     * it is only available 6 hours after signup and
     * supposed to be displayed on thanks page
     *
     * @return string|null
     */
    function getStoredPlaintextPassword()
    {
        $pg = $this->getDi()->store->get('pass-generated-' . $this->pk());
        if (!$pg) return null;
        $c = new Am_Crypt_Aes128($this->getDi()->security->siteKey());
        return $c->decrypt($pg);
    }

    function getLoginCookie()
    {
        if (!$this->remember_key) {
            $this->updateQuick('remember_key', sha1(rand()));
        }
        return sha1($this->user_id . $this->login . md5($this->pass) . $this->remember_key . $this->auth_key);
    }

    function generateLogin()
    {
        // usernames to try
        $try = [];
        if (!$this->getDi()->config->get('login_generate_only_random', false))
        {
            if (!empty($this->email) && preg_match("/^([a-zA-Z0-9_]+)\@/", $this->email, $regs))
            {
                $try[] = $regs[1];
            }
            $fn = strtolower(preg_replace('/[^\w\d_]/', '', $this->name_f ?? ''));
            $ln = strtolower(preg_replace('/[^\w\d_]/', '', $this->name_l ?? ''));


            if ($fn || $ln)
            {
                if ($fn && $ln)
                {
                    $try[] = $fn.'_'.$ln;
                } else
                {
                    $try[] = $fn.$ln;
                }
                $try[] = $try[count($try) - 1].rand(100, 999);
            }
        } else { // only random - generate pronounceable login with 2 possible digits at end
            $passEven = $this->getDi()->security->randomString(5, 'qwrtpsdfghklzxcvbnm');
            $passOdd = $this->getDi()->security->randomString(5, 'eyuoaj');
            $passAll = $this->getDi()->security->randomString(2, 'qwertyuopasdfghjklzxcvbnm123456789123456789123456789');

            $try[] =
                $passEven[0] . $passOdd[0] .
                $passEven[1] . $passOdd[1] .
                $passEven[2] . $passOdd[2] .
                $passEven[3] . $passOdd[3] .
                $passEven[4] . $passOdd[4] .
                $passAll[0] . $passAll[1];
        }
        $try = $this->getDi()->hook->filter($try, Am_Event::GENERATE_LOGIN, ['user' => $this]);

        $min_length = $this->getDi()->config->get('login_min_length');
        if ($min_length <= 0) $min_length = 8;
        $max_length = max(10, $this->getDi()->config->get('login_max_length', 64));
        foreach ($try as $login) {
            if (strlen($login) > $max_length)
                $login = substr($login, 0, $max_length);
            if ((strlen($login) >= $min_length) &&
                $this->getDi()->userTable->checkUniqLogin($login) &&
                !$this->getDi()->banTable->findBan(['login' => $login])) {

                $this->login = $login;
                return $this;
            }
        }

        // will generate it
        do {
            $pass = $this->getDi()->security->randomString(rand($min_length, $max_length), "qwertyuiopasdfghjklzxcvbnm");
        } while (!$this->getDi()->userTable->checkUniqLogin($pass));
        $this->login = $pass;
        return $this;
    }

    function generatePassword()
    {
        // a bit of configuration
        $min_length = max($this->getDi()->config->get('pass_min_length', 8), 8);
        $max_length = min($this->getDi()->config->get('pass_max_length', 12), 14);
        $all_g = "aeiyo";
        $all_gn = $all_g . "1234567890";
        $all_s = "bcdfghjkmnpqrstwxz";
        /// let's go
        $pass = "";
        $length = rand($min_length, $max_length);
        for ($i = 0; $i < $length; $i++) {
            if ($i % 2)
                if ($i < $min_length)
                    $pass .= $all_g[rand(0, strlen($all_g) - 1)];
                else
                    $pass .= $all_gn[rand(0, strlen($all_gn) - 1)];
            else
                $pass .= $all_s[rand(0, strlen($all_s) - 1)];
        }

        $pass = $this->getDi()->hook->filter($pass, Am_Event::GENERATE_PASSWORD, ['user' => $this]);

        $this->_passwordGenerated = true;
        $this->setPass($pass);
        return $this;
    }

    private function _trueIfActive($v)
    {
        return $v == self::STATUS_ACTIVE;
    }

    private function _trueIfExpired($v)
    {
        return $v == self::STATUS_EXPIRED;
    }

    function checkSubscriptions($updateCache = false)
    {
        if (!$this->user_id)
            throw new Am_Exception_InternalError("Could not do User->checkSubscriptions() : user_id is empty");

        if ($updateCache)
            $this->getDi()->resourceAccessTable->updateCache($this->user_id);

        $newStatus = $this->getDi()->accessTable->getStatusByUserId($this->user_id);
        $active = array_keys(array_filter($newStatus, [$this, '_trueIfActive']));

        $oldStatus = $this->getProductsStatus();
        $saved = array_keys(array_filter($oldStatus, [$this, '_trueIfActive']));

        $merged = array_unique(array_merge($active, $saved));
        $added = array_diff($merged, $saved);
        $deleted = array_diff($merged, $active);
        if ($active) {
            $newUserStatus = self::STATUS_ACTIVE;
        } elseif (array_filter($newStatus, [$this, '_trueIfExpired'])) {
            $newUserStatus = self::STATUS_EXPIRED;
        } else {
            $newUserStatus = self::STATUS_PENDING;
        }

        $statusChanged = ($newUserStatus != @$this->status);
        if ($statusChanged) {
            $this->updateQuick('status', $newUserStatus);
        }
        if ($added || $deleted || array_diff_assoc($newStatus, $oldStatus) || array_diff_assoc($oldStatus, $newStatus)) {
            $this->data()->set(self::NEED_SESSION_REFRESH, true)->update();
            $this->getDi()->userStatusTable->setByUserId($this->user_id, $newStatus);
            foreach ($added as $product_id) {
                $e = new Am_Event_SubscriptionAdded($this, $this->getDi()->productTable->load($product_id));
                $this->getDi()->hook->call($e);
            }
            foreach ($deleted as $product_id) {
                $e = new Am_Event_SubscriptionDeleted($this, $this->getDi()->productTable->load($product_id));
                $this->getDi()->hook->call($e);
            }

            $e = new Am_Event_SubscriptionChanged($this, $added, $deleted);
            $this->getDi()->hook->call($e);
        }
        if ($statusChanged) {
            $this->sendSignupEmailIfNecessary();
        }
    }

    /**
     * This function is called upon completion of payment
     * If user signup e-mail was not set before, it will be sent from there
     * It checks if :
     *   - email is enabled in config
     *   - user has at least one completed payment
     *   - the e-mail was not sent before
     *   - customer was approved
     */
    function sendSignupEmailIfNecessary(InvoicePayment $p = null)
    {
        if($this->_delete)
            return;
        if ($this->data()->get('signup_email_sent'))
            return; // was already sent
        if (!$this->isApproved())
            return; // is not yet approved

        //it is important to set this flag even if send_signup_mail is disabled currently
        //admin can enable this option later and user will get unexpected signup email
        if (!$this->getDi()->config->get('send_signup_mail')) {
            $this->data()->set('signup_email_sent', 1)->update();
            return;
        }

        $this->sendSignupEmail($p);
    }

    function sendSignupEmail(InvoicePayment $p = null)
    {
        if ($et = Am_Mail_Template::load('send_signup_mail', $this->lang)) {
            $et->setUser($this);
            //%password% placeholder will be available only in case auto_create
            //mode in payment system (user and payment created during same request)
            $pass = $this->getPlaintextPass();
            $et->setPassword($pass ? $pass : ___('what you entered when creating your account'));

            if (empty($p))
                $p = $this->getDi()->invoicePaymentTable->findFirstByUserId($this->user_id);
            if ($p)
                $et->setPayment($p);
            $et->send($this);
            $this->data()->set('signup_email_sent', 1)->update();
        }
    }

    function sendRegistrationToAdminEmail()
    {
        if ($et = Am_Mail_Template::load('registration_mail_admin')) {
            $et->setUser($this);
            $et->setPassword($this->getPlaintextPass());
            $et->sendAdmin();
        }
    }

    function sendRegistrationEmail()
    {
        if ($et = Am_Mail_Template::load('registration_mail', $this->lang)) {
            $et->setUser($this);
            $et->setPassword($this->getPlaintextPass());
            $et->send($this);
        }
    }

    function sendChangepassEmail()
    {
        if ($this->getDi()->config->get('changepass_mail') && ($et = Am_Mail_Template::load('changepass_mail', $this->lang))) {
            $et->setUser($this);
            $et->setPassword($this->getPlaintextPass());
            $et->send($this);
        }
    }

    function sendNotApprovedEmail()
    {
        if ($et = Am_Mail_Template::load('manually_approve', $this->lang)) {
            $et->setUser($this);
            $et->send($this);
        }
        if ($et = Am_Mail_Template::load('manually_approve_admin')) {
            $et->setUser($this);
            $et->send(Am_Mail_Template::TO_ADMIN);
        }
    }

    /**
     * @return bool true if user have any active subscriptions
     */
    function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    /**
     * Return true if customer paid at least once
     * @return bool
     */
    function isPaid()
    {
        return (bool) $this->getAdapter()->selectCell(
                "SELECT SUM(amount)>0
               FROM ?_invoice_payment p WHERE user_id=?d"
                , $this->user_id);
    }

    /**
     * @return true if user is approved
     */
    function isApproved()
    {
        return !empty($this->is_approved) && ($this->is_approved > 0);
    }

    function isLocked()
    {
        return !empty($this->is_locked) && ($this->is_locked > 0);
    }

    function lock($flag = true)
    {
        $this->is_locked = (int)$flag;
        $this->save();
    }

    /**
     * @return array of Access objects
     */
    function getAccessRecords()
    {
        return $this->getDi()->accessTable->findByUserId($this->user_id);
    }

    function getActiveProducts()
    {
        return $this->getDi()->productTable->selectObjects("SELECT p.* FROM ?_user_status us "
            . "LEFT JOIN ?_product p USING(product_id) WHERE user_id=?d "
            . "AND status=?d "
            . "AND p.product_id IS NOT NULL "
            . "ORDER BY sort_order", $this->user_id, self::STATUS_ACTIVE);
    }

    function getExpiredProducts()
    {
        return $this->getDi()->productTable->loadIds($this->getExpiredProductIds());
    }

    function getFutureProducts()
    {
        return $this->getDi()->productTable->loadIds($this->getFutureProductIds());
    }

    /**
     * Returns max expiration date
     * if product or array of products are specified, returns it
     * for given products only
     */
    function getExpire($productIdOrIds = [])
    {
        $productIdOrIds = (array) $productIdOrIds;
        $productIdOrIds = array_filter(array_map('intval', $productIdOrIds));
        return $this->getDi()->db->selectCell("SELECT MAX(expire_date)
            FROM ?_access
            WHERE user_id=?d { AND product_id IN (?a) }", $this->pk(), $productIdOrIds ? $productIdOrIds : DBSIMPLE_SKIP
        );
    }

    function getBegin($productIdOrIds = [])
    {
        $productIdOrIds = (array) $productIdOrIds;
        $productIdOrIds = array_filter(array_map('intval', $productIdOrIds));
        return $this->getDi()->db->selectCell("SELECT MIN(begin_date)
            FROM ?_access
            WHERE user_id=?d AND begin_date > ? { AND product_id IN (?a) }", $this->pk(), sqlDate('now'), $productIdOrIds ? $productIdOrIds : DBSIMPLE_SKIP
        );
    }

    function getRebill($productIdOrIds = [])
    {
        $productIdOrIds = (array) $productIdOrIds;
        $productIdOrIds = array_filter(array_map('intval', $productIdOrIds));
        return $this->getDi()->db->selectCell("SELECT
            MIN(i.rebill_date)
            FROM ?_access a
            LEFT JOIN ?_invoice_item ii
                ON a.invoice_id = ii.invoice_id
                AND a.product_id = ii.item_id
                AND ii.item_type = 'product'
            LEFT JOIN ?_invoice i
                ON a.invoice_id = i.invoice_id
            WHERE a.user_id=?d
            AND i.rebill_date IS NOT NULL
            AND i.status = ?
            AND i.rebill_date > ?
            AND ii.second_total > 0
            {AND a.product_id IN (?a)}", $this->pk(), Invoice::RECURRING_ACTIVE, sqlDate('now'), $productIdOrIds ? $productIdOrIds : DBSIMPLE_SKIP
        );
    }

    function getActiveProductsExpiration()
    {
        $ret = [];
        foreach ($this->getActiveProductIds() as $pid) {
            $ret[$pid] = $this->getExpire($pid);
        }
        return $ret;
    }

    function getActiveCategoriesExpiration()
    {
        $activePids = $this->getActiveProductIds();

        $cp_map = array_filter(
            $this->getDi()->productCategoryTable->getCategoryProducts(),
            function($pids) use ($activePids){
                return array_intersect($pids, $activePids);
            }
            );
        $out = [];
        foreach ($cp_map as $cip => $pids) {
            $out[$cip] = $this->getExpire($pids);
        }
        return array_filter($out);
    }

    function getFutureProductsBeginning()
    {
        $ret = [];
        foreach ($this->getFutureProductIds() as $pid) {
            $ret[$pid] = $this->getBegin($pid);
        }
        return $ret;
    }

    function getActiveProductsRebill()
    {
        $ret = [];
        foreach ($this->getActiveProductIds() as $pid) {
            $ret[$pid] = $this->getRebill($pid);
        }
        return $ret;
    }

    /** @return array of int active product# */
    function getActiveProductIds()
    {
        return $this->getAdapter()->selectCol("SELECT product_id FROM ?_user_status WHERE user_id=?d AND status=?d", $this->user_id, self::STATUS_ACTIVE);
    }

    /** @return array of int expired product# */
    function getExpiredProductIds()
    {
        return $this->getAdapter()->selectCol("SELECT product_id FROM ?_user_status WHERE user_id=?d AND status=?d", $this->user_id, self::STATUS_EXPIRED);
    }

    /** @return array of int future product# */
    function getFutureProductIds()
    {
        return $this->getAdapter()->selectCol("SELECT DISTINCT a.product_id
            FROM ?_access a
            LEFT JOIN ?_user_status us
            USING(user_id,product_id)
            WHERE a.user_id = ?
            AND a.begin_date>?
            AND (us.status IS NULL OR us.status<>?)", $this->user_id, sqlDate('now'), self::STATUS_ACTIVE);
    }

    /**
     * @return array product_id => status (@see self::STATUS_ACTIVE, ... constants)
     */
    function getProductsStatus()
    {
        return $this->getDi()->userStatusTable->getByUserId($this->user_id);
    }

    /// -- implementing IMailReceiver interface */
    public function getEmail()
    {
        return $this->email;
    }

    public function getName()
    {
        return trim(@$this->name_f . ' ' . @$this->name_l);
    }

    public function isUnsubscribed()
    {
        return (bool) $this->unsubscribed;
    }

    public function canUnsubscribe()
    {
        return true;
    }

    function getMobile()
    {
        if(!empty($this->mobile_number) && !empty($this->mobile_area_code))
        {
            [, $area_code] = explode("+", $this->mobile_area_code);
            return sprintf("+%s%s",$area_code, $this->mobile_number);
        }
        return null;
    }
    /** param array $groups - array of id# */
    function setGroups(array $groups)
    {
        if (empty($this->user_id)) {
            $this->_groups = $groups;
            return $this;
        }

        $this->getAdapter()->query("DELETE FROM ?_user_user_group
            WHERE user_id=?d {AND user_group_id NOT IN (?a)}", $this->user_id, $groups ? $groups : DBSIMPLE_SKIP);
        if ($groups) {
            $vals = [];
            foreach ($groups as $id)
                $vals[] = sprintf("(%d,%d)", $this->user_id, $id);
            $this->getAdapter()->query("INSERT IGNORE INTO ?_user_user_group
                (user_id, user_group_id)
                VALUES " . implode(", ", $vals));
        }
        $this->checkSubscriptions(true);
        return $this;
    }

    /** @return array of id# */
    function getGroups()
    {
        if (empty($this->user_id)) {
            return $this->_groups;
        }
        return $this->getAdapter()->selectCol(
                "SELECT DISTINCT user_group_id
            FROM ?_user_user_group
            WHERE user_id=?d", $this->user_id);
    }

    function setEmailAddressConfirmed()
    {
        /**
         * @todo Execute hook;
         */
        $this->email_confirmed = true;
        $this->email_confirmation_date = $this->getDi()->sqlDateTime;
        $this->save();
    }
    function setPhoneConfirmed()
    {
        $this->phone_confirmed = true;
        $this->phone_confirmation_date = $this->getDi()->sqlDateTime;
        $this->save();
    }

    function needForceChangePassword()
    {
        return $this->data()->get('force_change_password') ||
            ($this->getDi()->config->get('force_change_password')
                && $this->pass_dattm < sqlTime("-{$this->getDi()->config->get('force_change_password_period', 30)} day"));
    }

    function getOtpAdapter($type = null): ?Am_Otp_Adapter_Interface
    {
        $adapters = [
            new Am_Otp_Adapter_Mobile($this),
            new Am_Otp_Adapter_Email($this)
        ];

        /**
         * @todo hook to add new adapters
         *
         */
        if(!empty($type))
        {
            $adapter =  array_reduce($adapters, fn($c, $i) => $i->getId()==$type?$i:$c, null);

            if(empty($adapter))
                throw new Am_Exception_InternalError(___('Wrong Otp adapter passed.'));
        }else{
            /**
             * Return First convirmed;
             * @todo Implement setting in user profile later
             */
            $adapter=array_reduce($adapters, fn($c, $i) => $c ?: ($i->isVerified() ? $i: null), null);
        }

        return $adapter;
    }
}

/**
 * @method findFirstByLogin($login)
 * @method findFirstByEmail($login)
 */
class UserTable extends Am_Table_WithData
{
    protected $_key = 'user_id';
    protected $_table = '?_user';
    protected $_recordClass = 'User';
    protected $sort_order = [];
    protected $_customFieldsConfigKey = 'member_fields';

    public function clearPending($date, $incl_aff = false)
    {
        $q = $this->_db->queryResultOnly("SELECT u.*
            FROM ?_user u
                LEFT JOIN ?_access a ON a.user_id = u.user_id
                LEFT JOIN ?_invoice_payment ip ON ip.user_id = u.user_id
            WHERE u.status = 0
                {AND IFNULL(u.is_affiliate,0)=?}
                AND a.access_id IS NULL
                AND ip.invoice_payment_id is NULL
                AND u.added < ?
                AND u.subusers_parent_id = 0
            GROUP BY u.user_id", ($incl_aff ? DBSIMPLE_SKIP : 0), sqlTime($date));
        $_ = 0;
        while ($r = $this->_db->fetchRow($q)) {
            $u = $this->createRecord($r);
            $u->delete();
            $_++;
        }
        $this->getDi()->adminLogTable->log("$_ pending users removed");
    }

    public function clearExpired($date, $incl_aff = false)
    {
        // this function deletes only 500 records at time, to do not work over limits
        $q = $this->_db->queryResultOnly("SELECT u.*
            FROM ?_user u
                LEFT JOIN ?_access a ON a.user_id = u.user_id
                LEFT JOIN ?_invoice_payment ip ON ip.user_id = u.user_id
            WHERE u.status = 2
                {AND IFNULL(u.is_affiliate,0)=?}
            GROUP BY u.user_id
            HAVING GREATEST(
                IFNULL(MAX(ip.dattm), '2000-01-01'),
                IFNULL(MAX(a.expire_date), '2000-01-01')) < ?
            LIMIT 500
        ", ($incl_aff ? DBSIMPLE_SKIP : 0), sqlTime($date));
        $_ = 0;
        while ($r = $this->_db->fetchRow($q)) {
            $u = $this->createRecord($r);
            $u->delete();
            $_++;
        }
        $this->getDi()->adminLogTable->log("$_ expired users removed");
    }

    function getLoginRegex()
    {
        $regexp = $this->getDi()->config->get('login_disallow_spaces') ?
            '/^[-0-9a-zA-Z_]+$/D' :
            '/^([-0-9a-zA-Z_][-0-9a-zA-Z_ ]+[-0-9a-zA-Z_]|[-0-9a-zA-Z_]+)$/D';

        return $this->getDi()->hook->filter($regexp,
            Am_Event::GET_LOGIN_REGEX,
            ['login_disallow_spaces' => $this->getDi()->config->get('login_disallow_spaces')]);
    }

    function getStrongPasswordRegex()
    {
        $regexp = '/^(?=.*[0-9].*[0-9])(?=.*[-_!@#$%^&*().,=+`~{}\?].*[-_!@#$%^&*().,=+`~{}\?])(?=.*[A-Z].*[A-Z])/';
        return $this->getDi()->hook->filter($regexp, Am_Event::GET_STRONG_PASSWORD_REGEX);
    }

    /**
     * Check for username uniqueness only
     * @param string $login
     * @return bool True if record unique (no such login exists), false if not-unique
     */
    function checkUniqLogin($login, $user_id = null)
    {
        $this->getDi()->hook->call(Am_Event::PRE_CHECK_UNIQ_LOGIN, ['login' => $login, 'userId' => $user_id]);

        $u = $this->_db->selectCell("SELECT user_id
            FROM ?_user
            WHERE login=? { AND user_id <> ?d}", $login, $user_id ? $user_id : DBSIMPLE_SKIP);
        if ($u)
            return 0;

        $event = $this->getDi()->hook->call(new Am_Event_CheckUniqLogin(null, ['login' => $login, 'userId' => $user_id]));
        return $event->isUnique() ? -1 : false;
    }

    /**
     * Check for email uniqueness only
     * @param string $email
     * @return bool True if record unique (no such login exists), false if not-unique
     */
    function checkUniqEmail($email, $user_id = null)
    {
        $this->getDi()->hook->call(Am_Event::PRE_CHECK_UNIQ_EMAIL, ['email' => $email, 'userId' => $user_id]);

        $u = $this->_db->selectCell("SELECT user_id
            FROM ?_user
            WHERE email=? { AND user_id <> ?d}", $email, $user_id ? $user_id : DBSIMPLE_SKIP);
        if ($u)
            return 0;
        $event = $this->getDi()->hook->call(new Am_Event_CheckUniqEmail(null, ['email' => $email, 'userId' => $user_id]));
        return $event->isUnique() ? -1 : false;
    }

    function checkAllSubscriptionsFindChanged($limit = null)
    {
        $db = $this->_db;

        // update resource_access_cache
        $this->getDi()->resourceAccessTable->updateCache();
        $db->query("DELETE FROM ?_user_status WHERE user_id NOT IN (SELECT user_id FROM ?_user)");
        $db->query("DELETE FROM ?_access WHERE user_id NOT IN (SELECT user_id FROM ?_user)");
        $db->query("DROP TABLE IF EXISTS ?_user_status_temp");
        $db->query("CREATE TEMPORARY TABLE ?_user_status_temp (
            _status_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id int not null,
            product_id int not null,
            status tinyint not null,
            PRIMARY KEY (_status_id),
            INDEX(user_id,product_id))
            ");
        // create table with calculated records from "access"
        $db->query("
            INSERT INTO ?_user_status_temp (user_id, product_id, status)
            SELECT a.user_id,a.product_id,
        	CASE WHEN SUM(IF(a.expire_date>=?, 1, 0)) THEN 1
        		 WHEN SUM(IF(a.expire_date< ?, 1, 0)) THEN 2
				 ELSE 0
			END as status
            FROM ?_access a
            WHERE a.begin_date<=?
            GROUP BY user_id,product_id
            ", $this->getDi()->sqlDate, $this->getDi()->sqlDate, $this->getDi()->sqlDate);
        // now select differences
        $ids0 = $db->selectCol("
            SELECT DISTINCT ms.user_id
            FROM ?_user_status ms
                LEFT JOIN ?_user_status_temp mst
                    ON  ms.user_id =mst.user_id
                    AND ms.product_id=mst.product_id
                    AND ms.status =mst.status
            WHERE mst.user_id IS NULL
            GROUP BY ms.user_id,ms.product_id {LIMIT ?d}", is_null($limit) ? DBSIMPLE_SKIP : $limit);
        if (!is_null($limit))
            $limit = $limit - count($ids0);
        // select if there is no such a record in user_status table
        $ids1 = $db->selectCol("SELECT DISTINCT mst.user_id
            FROM ?_user_status_temp mst
                LEFT JOIN ?_user_status ms
                    ON  ms.user_id =mst.user_id
                    AND ms.product_id=mst.product_id
                    AND ms.status    =mst.status
            WHERE ms.user_id IS NULL
            GROUP BY mst.user_id,mst.product_id
            {LIMIT ?d}", is_null($limit) ? DBSIMPLE_SKIP : $limit);
        if (!is_null($limit))
            $limit = $limit - count($ids1);
        // select if user record has different "status" value
        $ids2 = $db->selectCol("SELECT DISTINCT m.user_id,
            CASE WHEN SUM(IF(mst.status=1, 1, 0)) THEN 1
        		 WHEN SUM(IF(mst.status=2, 1, 0)) THEN 2
				 ELSE 0
            END AS calcStatus, m.status
            FROM ?_user m
                LEFT JOIN ?_user_status_temp mst USING (user_id)
            GROUP BY m.user_id
            HAVING IFNULL(calcStatus,0) <> IFNULL(m.status, 0)
            {LIMIT ?d}", is_null($limit) ? DBSIMPLE_SKIP : $limit);
        return array_unique(array_merge($ids0, $ids1, $ids2));
    }

    /**
     * If this function changed, check also AdminRebuildController - it must be changed too
     */
    function checkAllSubscriptions()
    {
        while ($changed = $this->checkAllSubscriptionsFindChanged(1000)) {
            foreach ($changed as $user_id) {
                $u = $this->load($user_id, false);
                if ($u)
                    $u->checkSubscriptions(false); // checked in checkAllSubscriptionsFindChanged
            }
        }
    }

    /**
     * Find user record by email (if $login looks like an email)
     * or by username
     * @param string $login e-mail or username
     * @return User|null
     */
    function getByLoginOrEmail($login)
    {
        if (!strlen($login))
            return null;
        if (strpos($login, '@') !== false)
            return $this->findFirstByEmail($login);
        else
            return $this->findFirstByLogin($login);
    }

    /**
     * Find record by login, check password and return it if all OK
     * with login/password
     * sets $resultCode from Am_Auth_Result
     * @return User
     */
    function getAuthenticatedRow($login, $pass, & $code = null)
    {
        if (empty($login) || empty($pass)) {
            $code = Am_Auth_Result::INVALID_INPUT;
            return;
        }
        $u = $this->getByLoginOrEmail($login);
        if (!$u) {
            $code = Am_Auth_Result::USER_NOT_FOUND;
            return;
        }
        if (!$u->checkPassword($pass)) {
            $code = Am_Auth_Result::WRONG_CREDENTIALS;
            return;
        }
        $code = Am_Auth_Result::SUCCESS;
        return $u;
    }

    function getAuthenticatedCookieRow($cLogin, $cPass, & $code = null)
    {
        if (empty($cLogin) || empty($cPass)) {
            $code = Am_Auth_Result::INVALID_INPUT;
            return null;
        }
        $u = $this->getByLoginOrEmail($cLogin);
        if (!$u) {
            $code = Am_Auth_Result::USER_NOT_FOUND;
            return;
        }
        if ($u->getLoginCookie() !== $cPass) {
            $code = Am_Auth_Result::WRONG_CREDENTIALS;
            return null;
        }
        $code = Am_Auth_Result::SUCCESS;
        return $u;
    }

    function selectLast($num, $dateThreshold = null, $whereAdd = '')
    {
        return $this->selectObjects("SELECT m.*,
            ROUND(SUM((p.amount - IFNULL(p.refund_amount, 0))/p.base_currency_multi), 2) AS paid,
                COUNT(p.invoice_payment_id) AS payments_count,
                sf.title AS saved_form_title
                FROM ?_user m
                LEFT JOIN ?_invoice_payment p USING (user_id)
                LEFT JOIN {$this->getDi()->savedFormTable->getName()} sf ON m.saved_form_id=sf.saved_form_id
                WHERE 1=1 { AND added > ? } $whereAdd
                GROUP BY m.user_id
                ORDER BY m.user_id DESC LIMIT ?d",
            $dateThreshold ?: DBSIMPLE_SKIP, $num);
    }

    function getPersonalDataFieldOptions()
    {
        $pdFields = [
            'login' =>  ___('Username'),
            'email' =>  ___('E-mail Address'),
            'name_f' => ___('First Name'),
            'name_l' => ___('Last Name'),
            'street'    =>  ___('Address Line 1'),
            'street2'    =>  ___('Address Line 2'),
            'state'    =>  ___('State'),
            'city'    =>  ___('City'),
            'zip'    =>  ___('Zip'),
            'country'    =>  ___('Country'),
            'phone'    =>  ___('Phone'),
            'remote_addr'    =>  ___('Signup IP Address'),
            'added'    =>  ___('Date Added'),
            'last_login'    =>  ___('Last Login Date'),
            'user_agent'    =>  ___('User Agent'),
            'last_ip'    =>  ___('Last Login IP Address'),
            'last_user_agent'    =>  ___('Last Login User Agent'),
        ];
        foreach($this->customFields()->getAll() as $field)
        {
            $key = !empty($field->sql)?$field->name:"data.".$field->name;
            $pdFields[$key] = $field->title;
        }
        return $pdFields;
    }
}
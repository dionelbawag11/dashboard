<?php

//https://tools.ietf.org/html/rfc4226
//https://tools.ietf.org/html/rfc6238
//https://github.com/google/google-authenticator/wiki/Key-Uri-Format

/**
 * @am_plugin_api 6.0
*/
class Am_Plugin_TwoFactorHotp extends Am_Plugin_TwoFactor
{
    const PLUGIN_STATUS = self::STATUS_BETA;
    const PLUGIN_COMM = self::COMM_COMMERCIAL;
    const PLUGIN_REVISION = '6.3.20';

    const DIGITS = 6;

    protected static $map = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
        'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
        'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
        'Y', 'Z', '2', '3', '4', '5', '6', '7', // 31
        '='  // padding char
    ];

    function preauth(Am_Record $user, $ip)
    {
        return false;
    }

    function isValid(Am_Record $user, Am_Mvc_Request $r)
    {
        return $this->checkCode($user, preg_replace('/[^0-9]/', '', $r->getParam('pass')));
    }

    function _initTwoFactorForm($form, $user)
    {
        $form->addHtml(null, ['class' => 'am-no-label am-row-highlight'])
            ->setHtml('<div>' . ___("You can get this pass from the Google Authenticator mobile app.") . '</div>');
        $form->addText('pass', ['autofocus' => true])
            ->setLabel(___("One Time Password"));
    }

    function _initSetupForm(Am_Form_Setup $form)
    {
        parent::_initSetupForm($form);
        $form->setTitle(___('2Factor (HOTP)'));
        $form->addText('issuer', ['placeholder' => $this->getDi()->config->get('site_title')])
            ->setLabel("Issuer\nindicating the provider or service this account is associated with");
    }

    function isConfigured()
    {
        return true;
    }

    function checkCode($user, $code)
    {
        $counter = $this->getData($user, 'counter');

        if ($counter) { //hotp
            for ($i = $counter; $i < $counter + 20; $i++) {
                if ($code == $this->hotp($this->getData($user, 'secret'), $i)) {
                    $this->setData($user, 'counter', $i + 1);
                    $user->save();
                    return true;
                }
            }
        } else { //totp
            $counter = floor(time()/30);
            return $code == $this->hotp($this->getData($user, 'secret'), $counter);
        }
        return false;
    }

    function hotp($key, $counter)
    {
        $hash = hash_hmac('sha1', $this->intToByteString($counter), $key);
        foreach(str_split($hash, 2) as $hex) {
            $hmac[] = hexdec($hex);
        }
        $offset = $hmac[19] & 0xf;
        $code = ($hmac[$offset+0] & 0x7F) << 24 |
            ($hmac[$offset + 1] & 0xFF) << 16 |
            ($hmac[$offset + 2] & 0xFF) << 8 |
            ($hmac[$offset + 3] & 0xFF);
        return $code % pow(10, self::DIGITS);
    }

    public function intToByteString($int)
    {
        $result = [];
        while ($int != 0) {
            $result[] = chr($int & 0xFF);
            $int >>= 8;
        }
        return str_pad(implode(array_reverse($result)), 8, "\000", STR_PAD_LEFT);
    }

    function delete(Am_Record $user)
    {
        $this->setData($user, 'enabled', 0);
        $this->setData($user, 'secret', null);
        $this->setData($user, 'counter', null);
        $user->save();
        return null;
    }

    function register(Am_Record $user, $vars)
    {
        $this->setData($user, 'enabled', 1);
        $this->setData($user, 'secret', $vars['secret']);
        $user->auth_key = sha1(rand());
        //$this->setData($user, 'counter', 2); //hotp
        $user->save();
        return null;
    }

    function base32_encode($input, $padding = true)
    {
        if(empty($input)) return "";
        $input = str_split($input);
        $binaryString = "";
        for($i = 0; $i < count($input); $i++) {
            $binaryString .= str_pad(base_convert(ord($input[$i]), 10, 2), 8, '0', STR_PAD_LEFT);
        }
        $fiveBitBinaryArray = str_split($binaryString, 5);
        $base32 = "";
        $i=0;
        while($i < count($fiveBitBinaryArray)) {
            $base32 .= self::$map[base_convert(str_pad($fiveBitBinaryArray[$i], 5,'0'), 2, 10)];
            $i++;
        }
        if($padding && ($x = strlen($binaryString) % 40) != 0) {
            if($x == 8) $base32 .= str_repeat(self::$map[32], 6);
            else if($x == 16) $base32 .= str_repeat(self::$map[32], 4);
            else if($x == 24) $base32 .= str_repeat(self::$map[32], 3);
            else if($x == 32) $base32 .= self::$map[32];
        }
        return $base32;
    }
}

abstract class TwoFactorHotpController_Abstract extends Am_Mvc_Controller
{
    protected $layout;

    abstract function getAuth();
    abstract function _url();
    abstract function _issuer();

    function getUser()
    {
        return $this->getAuth()->getUser();
    }

    function indexAction()
    {
        $user = $this->getUser();

        $this->view->title = ___('2Factor Authentication');
        if ($this->getPlugin()->isEnabled($user)) {
            $this->_disable($user);
        } else {
            $this->_enable($user);
        }
    }

    function _enable($user)
    {
        $form = new Am_Form();
        $form->addProlog(<<<CUT
<style type="text/css">
<!--
    .am-form {
        max-width: 700px;
    }
    .am-form div.am-element-title {
        text-align:left;
    }
-->
</style>
CUT
        );
        $form->addCsrf();
        $form->addHidden('secret')
            ->setValue($s = $this->getRequest()->getParam('secret') ?: $this->getDi()->security->randomString(10));

        //{hotp|totp}
        $url = Am_Html::escape(
            "https://chart.apis.google.com/chart?" .
                http_build_query([
                    'chs' => '200x200',
                    'chld' => 'M|0',
                    'cht' => 'qr',
                    'chl' => "otpauth://totp/{$user->email}?" . http_build_query([
                        'secret' => $this->getPlugin()->base32_encode($s),
                        'algorithm' => 'SHA1',
                        'digits' => Am_Plugin_TwoFactorHotp::DIGITS,
                        'issuer' => $this->_issuer(),
                        //'counter' => 0 //totp
                    ])
                ]));
        $form->addHtml(null, ['class' => 'am-row-wide'])
            ->setHtml(<<<CUT
<div style="text-align:center">
<p>Install Google Authenticator mobile App and scan this QR code with it:</p>
<img src="{$url}" />
</div>
CUT
        );
        $form->addText("code", ['autofocus' => true])
            ->setLabel("Code\nfrom google authentificator app")
            ->addRule('required')
            ->addRule('callback2', null, function($c) use ($s) {
                //hotp
                //IOS || Android
//                return ($this->getPlugin()->hotp($s, 0) == $c || $this->getPlugin()->hotp($s, 1) == $c) ?
//                    null : 'Invalid Code';
                //totp
                $counter = floor(time()/30);
                return $this->getPlugin()->hotp($s, $counter) == $c ? null : 'Invalid Code';
            });
        $form->addSaveButton(___('Enable'));

        $errors = [];
        if ($form->isSubmitted() &&
            $form->validate() &&
            !($errors = $this->getPlugin()->register($user, $form->getValue()))) {

            $this->getAuth()->setUser($user);
            Am_Mvc_Response::redirectLocation($this->_url());
        } else {
            if ($errors)
                $form->setError(implode(', ', $errors));
            $this->view->content = $form;
            $this->view->display($this->layout);
        }
    }

    function _disable($user)
    {
        $form = new Am_Form();
        $form->addCsrf();
        $form->addStatic()
            ->setLabel(___('Status'))
            ->setContent(___('Enabled'));
        $form->addSaveButton(___('Disable'));
        if ($form->isSubmitted() &&
            $form->validate()) {

            $this->getPlugin()->delete($user);
            Am_Mvc_Response::redirectLocation($this->_url());
        } else {
            $this->view->content = $form;
            $this->view->display($this->layout);
        }
    }

    function getPlugin()
    {
        return $this->getDi()->plugins_misc->loadGet('two-factor-hotp');
    }
}

class TwoFactorHotpController extends TwoFactorHotpController_Abstract
{
    protected $layout = 'member/layout.phtml';

    function preDispatch()
    {
        $this->getDi()->auth->requireLogin();
    }

    function getAuth()
    {
        return $this->getDi()->auth;
    }

    function _url()
    {
        return $this->getDi()->url('two-factor-hotp', false);
    }

    function _issuer()
    {
        return $this->getPlugin()->getConfig('issuer') ?: $this->getDi()->config->get('site_title');
    }
}

class AdminTwoFactorHotpController extends TwoFactorHotpController_Abstract
{
    protected $layout = 'admin/layout.phtml';

    function checkAdminPermissions(Admin $admin)
    {
        return true;
    }

    function getAuth()
    {
        return $this->getDi()->authAdmin;
    }

    function _url()
    {
        return $this->getDi()->url('admin-two-factor-hotp', false);
    }

    function _issuer()
    {
        return ($this->getPlugin()->getConfig('issuer') ?: $this->getDi()->config->get('site_title')) . " | Admin";
    }
}
<?php

/**
 * @am_plugin_api 6.0
 */
class Am_Plugin_Autocoupons extends Am_Plugin
{
    const PLUGIN_STATUS = self::STATUS_PRODUCTION;
    const PLUGIN_COMM = self::COMM_FREE;
    const PLUGIN_REVISION = '6.3.20';

    protected $_configPrefix = 'misc.';

    static function getDbXml()
    {
        return <<<CUT
<schema version="4.0.0">
    <table name="coupon">
        <field name="expire" type="date" notnull="0" />
    </table>
</schema>
CUT;
    }

    function _initSetupForm(Am_Form_Setup $form)
    {
        $form->addSelect('batchId')
            ->setLabel(___("Default Coupon Batch\n" .
                "placeholder %coupon% will be replaced with coupon code from this batch"))
            ->loadOptions($this->getDi()->couponBatchTable->getOptions());
        $form->addAdvCheckbox('bind_to_user')
            ->setLabel("Bind Generated Coupon to User Account\n"
                . "only receiver of email with coupon will be able to use it");
        $gr = $form->addGroup()
            ->setLabel("Limited period of validity\n"
                . "for generated coupons");
        $gr->setSeparator(' ');

        $gr->addAdvCheckbox('set_expire');
        $gr->addPeriod('period');

        $form->addScript()
            ->setScript(<<<CUT
jQuery('[name$=set_expire][type=checkbox]').change(function(){
    jQuery(this).closest('div').find('input[type=text], select').toggle(this.checked)
}).change();
jQuery('option[value=lifetime]').remove();
CUT
);
    }

    function onGridCouponInitGrid(Am_Event_Grid $e)
    {
        $e->getGrid()
            ->addField(new Am_Grid_Field_Date('expire', ___('Expire')))
            ->setFormatDate();
    }

    private function getCouponCode($batchId = null, $user_id = null, $expire = null)
    {
        if (!$batchId)
        {
            $batchId = $this->getConfig('batchId');
        }
        $coupon = $this->getDi()->couponRecord;
        $coupon->batch_id = $batchId;
        $coupon->code = $this->getDi()->couponTable->generateCouponCode(8,$length);
        $coupon->user_id = $user_id;
        $coupon->expire = $expire;
        $coupon->save();

        return $coupon->code;
    }

    public function onMailTemplateBeforeParse(Am_Event $event)
    {
        /* @var $template Am_Mail_Template */
        $template = $event->getTemplate();
        $tConfig = $template->getConfig();
        $mailBody = (!empty($tConfig['bodyText'])) ? $tConfig['bodyText'] : $tConfig['bodyHtml'];
        if (preg_match_all('/%coupon(_([0-9]+))?%/', $mailBody, $matches))
        {
            $count = count($matches[0]);
            for($i=0; $i<$count;$i++)
            {
                if (isset($template['coupon' . $matches[1][$i]])) continue; //already set in template itself
                $coupon = $this->getCouponCode(
                    $matches[2][$i],
                    $this->getConfig('bind_to_user') ? $template->user->pk() : null,
                    $this->getConfig('set_expire') ? $this->expire(sqlDate('now')) : null
                );
                $template->{"setCoupon".$matches[1][$i]}($coupon);
            }
        }
    }

    public function onMailSimpleTemplateBeforeParse(Am_Event $event)
    {
        /* @var $template Am_SimpleTemplate */
        $template = $event->getTemplate();
        $body = $event->getBody();
        $subject = $event->getSubject();
        if (preg_match_all('/%coupon(_([0-9]+))?%/', $subject.' '.$body, $matches))
        {
            $count = count($matches[0]);
            for($i=0; $i<$count;$i++)
            {
                $coupon = $this->getCouponCode(
                    $matches[2][$i],
                    $this->getConfig('bind_to_user') ? $template->user['user_id'] : null,
                    $this->getConfig('set_expire') ? $this->expire(sqlDate('now')) : null
                );
                $template->{"coupon".$matches[1][$i]} = $coupon;
            }
        }
    }

    protected function expire($now)
    {
        $period = new Am_Period($this->getConfig('period'));

        return $period->addTo($now);
    }

    function onValidateCoupon(Am_Event $e)
    {
        $coupon = $e->getCoupon();
        if ($coupon->expire && $coupon->expire<sqlDate('now')) {

            $e->addReturn(___('This coupon is expired'));
        }
    }
}
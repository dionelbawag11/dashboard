<?php

/**
 * Plugin allow user to specify price for product on her own.
 * (make a donation of any sum)
 *
 * @am_plugin_api 6.0
 */
class Am_Plugin_Donation extends Am_Plugin
{
    const PLUGIN_STATUS = self::STATUS_BETA;
    const PLUGIN_COMM = self::COMM_COMMERCIAL;
    const PLUGIN_REVISION = '6.3.20';

}

Am_Di::getInstance()->hook->add(Am_Event::LOAD_BRICKS, function(Am_Event $e) {

    class Am_Form_Brick_Donation extends Am_Form_Brick
    {
        protected static $brickPosition = 900;
        protected $hideIfLoggedInPossible = self::HIDE_DONT;
        protected $labels = [
            'Donation Amount %s',
            'Make a Donation',
            'Support Us!',
            'Make donation recurring',
            'Please specify donation amount',
            'Please enter correct amount',
            'The minimum donation amount is %s'
        ];

        const RECURRING_NONE = 0;
        const RECURRING_OPTION = 1;
        const RECURRING_FORCE = 2;

        public function init()
        {
            if ($this->getConfig('product'))
                Am_Di::getInstance()->hook->add(Am_Event::SIGNUP_INVOICE_ITEMS, [$this, 'handleDonation']);
        }

        function handleDonation(Am_Event $e)
        {
            /* @var $invoice Invoice */
            $invoice = $e->getInvoice();
            $vars = $e->getVars();
            $p_id = $this->getConfig('product');
            foreach ($invoice->getItems() as $item) {
                if ($item->item_type == 'product' && $item->item_id == $p_id) {
                    if (!$vars['donation'][$p_id] && $this->getConfig('access') != 'allow_free') {
                        $invoice->deleteItem($item);
                    } else {
                        $item->first_price = $vars['donation'][$p_id];
                        $item->data()->set('orig_first_price', $item->first_price);

                        if ((float)$vars['donation'][$p_id] &&
                            ($this->getConfig('recurring') == self::RECURRING_FORCE ||
                                (isset($vars['recurring'][$p_id]) && $vars['recurring'][$p_id]))) {

                            $item->second_price = $item->first_price;
                            $item->data()->set('orig_second_price', $item->second_price);
                        } else {
                            $item->rebill_times = 0;
                            $item->second_price = 0;
                            $item->second_period = null;
                            $item->data()->set('orig_second_price', $item->second_price);
                        }
                        if ($this->getConfig('access') == 'no_access') {
                            $item->data()->set('no-access', true);
                        }
                    }
                    break;
                }
            }
        }

        function initConfigForm(Am_Form $form)
        {
            $form->addSelect('product', ['class' => 'am-combobox-fixed'])
                ->setLabel(___("Donation Product\n".
                    "Product which will be added to user's account"))
                ->loadOptions(['' => ''] + Am_Di::getInstance()->productTable->getOptions());

            $form->addSelect('access')
                ->setLabel('Access Record')
                ->loadOptions([
                    '' => ___('Add Access only if user choose some donation'),
                    'allow_free' => ___('Add Access even if user choose 0 donation amount'),
                    'no_access' => ___('Do not add any access')
                ]);

            $form->addAdvCheckbox('is_required')
                ->setLabel(___('Is Donation Required?'));

            $form->addSelect('recurring')
                ->setLabel(___("Recurring?\n" .
                    "Product must be recurring as well, First & Second periods will be taken from product config"))
                ->loadOptions([
                    self::RECURRING_NONE => ___('Not Recurring'),
                    self::RECURRING_OPTION => ___('User can choose to make donation recurring'),
                    self::RECURRING_FORCE => ___('Recurring')
                ]);

            $form->addSelect('layout', ['class'=>'donation-layout'])
                ->loadOptions([
                    '' => ___('Default'),
                    'smile' => ___('Emotional')
                ])->setLabel(___('Layout'));

            $form->addText('amount_min', ['placeholder' => '0.00', 'size' => 5])
                ->setLabel(___("Minimum amount of donation\n" .
                    "keep it empty if there is not any limit"));
            $form->addText('amount_max', ['placeholder' => '100.00', 'size' => 5])
                ->setLabel(___("Maximum amount of donation"));
            $form->addText('amount_default', ['placeholder' => '0.00', 'size' => 5])
                ->setLabel(___("Default amount of donation"));
            $form->addText('amount_step', ['placeholder' => '1.00', 'size' => 5])
                ->setLabel(___("Step amount of donation"));
            $form->addScript()
                ->setScript(<<<CUT
jQuery(function(){
    jQuery(document).on('change', '.donation-layout', function() {
        var context = jQuery(this).closest('form');
        jQuery('[name=amount_max]', context).closest('.am-row').toggle(jQuery(this).val()=='smile');
        jQuery('[name=amount_default]', context).closest('.am-row').toggle(jQuery(this).val()=='smile');
        jQuery('[name=amount_step]', context).closest('.am-row').toggle(jQuery(this).val()=='smile');
    })
    jQuery('.donation-layout').change();
})
CUT
                );
        }

        public function setConfigArray(array $config)
        {
            if (!empty($config['allow_free'])) {
                $config['access'] = 'allow_free';
                unset($config['allow_free']);
            }
            parent::setConfigArray($config);
        }

        function isAcceptableForForm(Am_Form_Bricked $form)
        {
            return $form instanceof Am_Form_Signup;
        }

        public function insertBrick(HTML_QuickForm2_Container $form)
        {
            if (!$this->getConfig('product'))
            {
                Am_Di::getInstance()->logger->error('Donation brick is not configured. Skipped...');
                return;
            }
            $name = self::$brickPosition ? 'product_id_' . self::$brickPosition : 'product_id';
            self::$brickPosition++;

            $product = Am_Di::getInstance()->productTable->load($this->getConfig('product'));
            $bp = $product->getBillingPlans();

            //@see AjaxController::_handleDonation
            $form->addHidden('donation_allow_free[' . $product->product_id . ']')
                ->setValue($this->getConfig('access') == 'allow_free');

            $form->addHidden('donation_force_recurring[' . $product->product_id . ']')
                ->setValue($this->getConfig('recurring') == self::RECURRING_FORCE);

            switch ($this->getConfig('layout')) {
                case 'smile' :
                    $this->addLayoutSmile($form, $name, $product, $bp[0]);
                    break;
                default:
                    $this->addLayoutDefault($form, $name, $product, $bp[0]);
            }
        }

        function addLayoutSmile(HTML_QuickForm2_Container $form, $name, Product $product, BillingPlan $bp)
        {
            $pid = $product->product_id . '-' . $bp->plan_id;

            $id = $name . '-donation';
            $gr = $form->addGroup('', ['class' => 'am-no-label', 'id'=>$id . '-group']);
            $form->addHidden($name)
                ->setId($id)
                ->setValue($pid)
                ->setAttribute('data-first_price', 0)
                ->setAttribute('data-second_price', 0);

            $gr->addHtml()
                ->setHtml('<div class="am-donation" id="wrap-' . $id . '">');

            switch ($this->getConfig('recurring')) {
                case self::RECURRING_FORCE :
                    $jsStm = 'jQuery(this).val()';
                    break;
                case self::RECURRING_OPTION :
                    $jsStm = "jQuery('#$id-recurring:checked').length ? jQuery(this).val() : 0";
                    break;
                default :
                    $jsStm = '0';
                    break;
            }

            $form->addScript()
                ->setScript(<<<CUT
jQuery('#$id-amount, #$id-recurring').change(function(){
    jQuery('#$id').attr('data-first_price', jQuery('#$id-amount').val()).change();
    jQuery('#$id').attr('data-second_price', $jsStm).change();
}).change();
CUT
                );

            $gr->addHidden('donation[' . $product->product_id . ']', ['id' => $id . '-amount']);
            $fieldid = $id . '-amount';

            $symbol = Am_Currency::render(50, $bp->currency);
            $symbol = preg_replace('/50(.0*)?/', '<span class="am-donation-total-amount"></span>', $symbol);
            $precision = Am_Currency::$currencyList[$bp->currency]['precision'];
            $title = $this->___('Donation Amount %s', $symbol);

            $min = $this->getConfig('amount_min') ?: 0;
            $max = $this->getConfig('amount_max') ?: 100;
            $step = $this->getConfig('amount_step') ?: 1;
            $_default = $this->getConfig('amount_default') ?: $min;
            if ($_default > $max) {
                $_default = $max;
            }
            if ($_default < $min) {
                $_default = $min;
            }
            $default = $_default;

            $values = [];
            $_ = $min;
            do {
                $values[] = (float)$_;
                if ($_default > $_ && $_default < ($_ + $step)) {
                    $default = $_;
                }
                $_+=$step;
            } while ($_ <= $max);

            if (($_-$step) != $max) {
                $values[] = $max;
            }

            if (!$this->getConfig('is_required') && $values[0] != 0) {
                array_unshift($values, 0);
            }

            $values = json_encode($values);

            $form->addDataSource(new HTML_QuickForm2_DataSource_Array([
                'donation' => [
                    $product->product_id  => $default
                ]
            ]));

            $gr->addStatic()
                ->setContent(<<<CUT
<div class="am-donation-smile-wrap">
    <div class="am-donation-smile">
        <div id="smile-$id">
            <div class="smileyface">
                <div class="eyes lefteye"></div>
                <div class="eyes righteye"></div>
                <div class="smile"></div>
            </div>
        </div>
    </div>
    <div class="am-donation-slider">
        <div class="am-donation-total">$title</div>
        <div id="slider-$id"></div>
    </div>
</div>
CUT
                );
            $form->addScript()
                ->setScript(<<<CUT
jQuery(function(){
    var values = {$values};
    var precision = {$precision};

    function rgba(r, g, b, a) {
       return 'rgba(' + r + ', ' + g + ', ' + b + ', ' + a + ')';
    }
    function updateSmile(rVal) {
        var val = Math.max($min, rVal);
        jQuery('#wrap-$id .smile').css('border-bottom-left-radius', parseInt(20 * (val-$min)/($max-$min)));
        jQuery('#wrap-$id .smile').css('border-bottom-right-radius', parseInt(20 * (val-$min)/($max-$min)));
        jQuery('#wrap-$id .smileyface').css('background', rgba(255, 230, 50, 1 * (val-$min)/($max-$min)));

        var minW = 6;
        var maxW = 8;
        var vw =  parseInt(minW + (maxW-minW) * (val-$min)/($max-$min));
        jQuery('#wrap-$id .eyes').css('width', vw + 'px');

        var minH = 8;
        var maxH = 12;
        var vh =  parseInt(minH + (maxH-minH) * (val-$min)/($max-$min));
        jQuery('#wrap-$id .eyes').css('height', vh + 'px');

        var min = 25;
        var max = 200;
        var v = parseInt(max - (max-min) * (val-$min)/($max-$min));
        jQuery('#wrap-$id .smile').css('border-color', rgba(v, v, v, 1));
        jQuery('#wrap-$id .eyes').css('background', rgba(v, v, v, 1));
        jQuery('#wrap-$id .smileyface').css('border-color', rgba(v, v, v, 1));
        jQuery('#wrap-$id .am-donation-total-amount').html(formatVal(rVal))
        jQuery('#$id-amount').val(rVal).change();
    }

    function formatVal(val)
    {
        return parseFloat(val).toFixed(precision);
    }

    updateSmile(jQuery('#$fieldid').val());
    jQuery( "#slider-$id" ).slider({
        range: 'min',
        max : values.length-1,
        min: 0,
        step: 1,
        value: values.indexOf(parseFloat(jQuery('#$fieldid').val())),
        slide : function (event, ui) {
            updateSmile(values[ui.value]);
        },
        change: function(event, ui) {
           updateSmile(values[ui.value]);
        }
    });
});
CUT
                );

            if ($this->getConfig('recurring') == self::RECURRING_OPTION)
            {
                $gr->addHtml()
                    ->setHtml('<div class="am-donation-recurring">');
                $gr->addCheckbox('recurring[' . $product->product_id . ']', [
                    'id' => "$id-recurring"
                ], ['content' => $this->___('Make donation recurring')]);
                $gr->addHtml()
                    ->setHtml('</div>');
            }

            $gr->addHtml()
                ->setHtml('</div>');
        }

        function addLayoutDefault(HTML_QuickForm2_Container $form, $name, Product $product, BillingPlan $bp)
        {
            $pid = $product->product_id . '-' . $bp->plan_id;
            $id = $name . '-donation';

            $gr = $form->addGroup('', ['id'=>$id . '-group'])
                ->setLabel($this->___('Make a Donation') . "\n" . $this->___('Support Us!'));

            $form->addHidden($name)
                ->setId($id)
                ->setValue($pid)
                ->setAttribute('data-first_price', 0)
                ->setAttribute('data-second_price', 0);

            switch ($this->getConfig('recurring')) {
                case self::RECURRING_FORCE :
                    $jsStm = 'jQuery(this).val()';
                    break;
                case self::RECURRING_OPTION :
                    $jsStm = "jQuery('#$id-recurring:checked').length ? jQuery(this).val() : 0";
                    break;
                default :
                    $jsStm = '0';
                    break;
            }

            $form->addScript()
                ->setScript(<<<CUT
jQuery('#$id-amount, #$id-recurring').change(function(){
    jQuery('#$id').attr('data-first_price', jQuery('#$id-amount').val()).change();
    jQuery('#$id').attr('data-second_price', $jsStm).change();
}).change();
jQuery(document).on('keyup', '#$id-amount', function() {
    jQuery(this).val(jQuery(this).val().replace(',', '.').replace(/[^0-9.]/, ''));
});
CUT
                );

            $dfield = $gr->addText('donation[' . $product->product_id . ']', ['size' => 5, 'id' => $id . '-amount', 'placeholder'=>'']);
            $gr->addHtml()->setHtml(" <span>{$bp->currency}</span>");

            //split Server (to Group) and Client (to Element) validation. Server (to Element) errors is not rendered within group element

            if ($this->getConfig('is_required')) {
                $gr->addClass('am-row-required');
                $dfield->addRule('required', $this->___('Please specify donation amount'), HTML_QuickForm2_Rule::CLIENT);
            }

            if ($min = $this->getConfig('amount_min')) {
                $dfield->addRule('gte', $this->___('The minimum donation amount is %s', $min), $min, HTML_QuickForm2_Rule::CLIENT);
            }

            $dfield->addRule('regex', $this->___("Please enter correct amount"), '/^\d+(|\.\d+)$/', HTML_QuickForm2_Rule::CLIENT);

            $gr->addRule('callback2', null, [$this, 'validate']);

            if ($this->getConfig('recurring') == self::RECURRING_OPTION)
            {
                $gr->addHtml()
                    ->setHtml('<div style="padding-top:1em">');
                $gr->addCheckbox('recurring[' . $product->product_id . ']', [
                    'id' => "$id-recurring"
                ], ['content' => $this->___('Make donation recurring')]);
                $gr->addHtml()
                    ->setHtml('</div>');
            }
        }

        function validate($v)
        {
            $pid = $this->getConfig('product');
            if ($this->getConfig('is_required') && empty($v['donation'][$pid])) {
                return $this->___('Please specify donation amount');
            }
            if (!empty($v['donation'][$pid]) && !preg_match('/^\d+(|\.\d+)$/', $v['donation'][$pid])) {
                return $this->___("Please enter correct amount");
            }

            if (($min = $this->getConfig('amount_min'))
                && !empty($v['donation'][$pid])
                && $v['donation'][$pid] < $min
            ) {
                return $this->___('The minimum donation amount is %s', $min);
            }
        }

        public function isMultiple()
        {
            return true;
        }
    }

});
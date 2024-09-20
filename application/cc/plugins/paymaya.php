<?php
/**
 * @table paysystems
 * @id paymaya
 * @title PayMaya
 * @visible_link https://www.paymaya.com/
 * @recurring cc
 * @am_payment_api 6.0
 */

class Am_Paysystem_Paymaya extends Am_Paysystem_CreditCard
{
    const PLUGIN_STATUS = self::STATUS_DEV;
    const PLUGIN_DATE = '$Date$';
    const PLUGIN_REVISION = '6.3.20';
    
    protected
        $defaultTitle = "PayMaya";
    protected
        $defaultDescription = "Credit Card Payment";
    protected $_pciDssNotRequired = true;
    
    const
        CONFIG_SECRET_KEY = 'secret-key',
        CONFIG_PUBLIC_KEY = 'public-key',
        CONFIG_MODE = 'mode',
        CONFIG_MODE_SANDBOX = 'sandbox',
        CONFIG_MODE_PRODUCTION = 'production',
        FORM_PAYMENT_TOKEN = 'payment-token-id',
        DATA_CUSTOMER_ID = 'paymaya-customer-id',
        DATA_CARD_ID = 'paymaya-card-id';
    
    public function getSupportedCurrencies()
    {
        return ['PHP'];
    }
    
    
    function _initSetupForm(Am_Form_Setup $form)
    {
        $form->addText(self::CONFIG_PUBLIC_KEY, ['class' => 'am-el-wide'])->setLabel(___('API Public Key'));
        $form->addSecretText(self::CONFIG_SECRET_KEY, ['class' => 'am-el-wide'])->setLabel(___('API Secret Key'));
        $form->addSelect(self::CONFIG_MODE)->setLabel(___('Mode'))->loadOptions([
            self::CONFIG_MODE_SANDBOX => 'Sandbox',
            self::CONFIG_MODE_PRODUCTION => 'Production'
        ]);
    }
    
    public function getFormOptions()
    {
        return [self::CC_CODE];
    }
    
    function createForm($actionName)
    {
        $form = new Am_FormCreditCard_Paymaya($this);
        
        $form->addHidden(self::FORM_PAYMENT_TOKEN)->addRule('required');
        $tokenFieldName = self::FORM_PAYMENT_TOKEN;
        $endpoint = $this->getEndpoint('/payments/v1/payment-tokens');
        $key = base64_encode($this->getConfig(self::CONFIG_PUBLIC_KEY) . ":");
        $form->addScript()->setScript(<<<CUT
function pad2(s) {
    while (s.length < 2) {s = "0" + s;}
    return s;
}
jQuery(document).ready(function(){
    jQuery("#cc").on('amFormSubmit', function(event){
        var callback = event.callback;
        event.callback = null;
        var form = jQuery("#cc");
        
        fetch('{$endpoint}', {
            method: 'POST',
            headers: {
                'Content-type' : 'application/json',
                'Authorization' : 'Basic {$key}'
            },
            body: JSON.stringify({
                "card" : {
                    'number'  : jQuery("input[name='cc_number']").val(),
                    'expMonth': pad2(jQuery("select[name='cc_expire[m]']").val()),
                    'expYear' : jQuery("select[name='cc_expire[y]']").val(),
                    'cvc'     : jQuery("input[name='cc_code']").val()
                }
            })
        }).then(function(res){
            return res.json();
        }).then(function(result){
            console.log(result);
            if(result.message){
                const validator = form.validate();
                let error = result.message;
                if(result.parameters)
                    for(i in result.parameters)
                        error =  error+ '<br/>' + result.parameters[i].description;
                validator.showErrors({
                'cc_number' : error
               });
               form.trigger('unlockui');
            }else
            {
                jQuery("input[name='{$tokenFieldName}']").val(result.paymentTokenId);
                jQuery("input[name='cc_number']").remove();
                jQuery("input[name='cc_code']").remove();
                jQuery("select[name='cc_expire[y]']").remove();
                jQuery("select[name='cc_expire[m]']").remove();
                callback();
            }
        });
        
        return false;
    });
});
CUT
        );
        return $form;
    }
    
    function getEndpoint($uri)
    {
        if ($this->getConfig(self::CONFIG_MODE, self::CONFIG_MODE_SANDBOX) == self::CONFIG_MODE_SANDBOX) {
            return "https://pg-sandbox.paymaya.com" . $uri;
        } else {
            return "https://pg.paymaya.com" . $uri;
        }
    }
    
    public function _doBill(Invoice $invoice, $doFirst, CcRecord $cc, Am_Paysystem_Result $result)
    {
        $user = $invoice->getUser();
        if (empty($user->data()->get(self::DATA_CUSTOMER_ID))) {
            $tr = new Am_Paysystem_Paymaya_Transaction_CreateCustomer($this, $invoice);
            $tr->run($result);
            if ($result->isFailure()) {
                return;
            }
        }
        
        $result->reset();
        
        if ($doFirst) {
            // First Transaction need to assign CC info to customer record
            $tr = new Am_Paysystem_Paymaya_Transaction_LinkCardToCustomer($this, $invoice,
                $cc->get(self::FORM_PAYMENT_TOKEN));
            $tr->run($result);
            
            if ($result->isFailure() || $result->isAction()) {
                return;
            }
            
            if ($invoice->first_total == 0) {
                $tr = new Am_Paysystem_Transaction_Manual($this);
                $tr->run($result);
                return;
            }
        }
        
        
    }
    
    function storeCreditCard(CcRecord $cc, Am_Paysystem_Result $result)
    {
    
    }
    
    /**
     * @param $endpoint
     * @return Am_HttpRequest
     * @throws HTTP_Request2_LogicException
     */
    function createRequest($endpoint)
    {
        $request = new Am_HttpRequest($endpoint, Am_HttpRequest::METHOD_POST);
        $request->setHeader('Content-type', 'application/json');
        $request->setAuth($this->getConfig(self::CONFIG_SECRET_KEY));
        return $request;
    }
    
    function directAction($request, $response, $invokeArgs)
    {
        if (!in_array($request->getActionName(), ['success', 'failure'])) {
            return parent::directAction($request, $response, $invokeArgs);
        }
        
        $invoice = $this->getDi()->invoiceTable->findBySecureId($request->getParam('invoice'),
            'PAY-' . strtoupper($request->getActionName()));
        if (empty($invoice)) {
            throw new Am_Exception_InputError("Unable to find invoice. Please get back ad try again");
        }
        $this->_setInvoice($invoice);
        
        switch ($request->getActionName()) {
            case 'success' :
                $result = new Am_Paysystem_Result();
                $tr = new Am_Paysystem_Paymaya_Transaction_Payment($this, $invoice, true);
                $tr->run($result);
                if ($result->isSuccess()) {
                    $response->setRedirect($this->getReturnUrl());
                    break;
                }
            
            case 'failure' :
                $response->setRedirect($this->getCancelUrl());
                break;
            
        }
        
    }
    
    public function processRefund(InvoicePayment $payment, Am_Paysystem_Result $result, $amount)
    {
        $tr = new Am_Paysystem_Paymaya_Transaction_Refund($this, $payment->getInvoice(), $payment->receipt_id, $amount);
        $tr->run($result);
    }
    
}


class Am_Paysystem_Paymaya_Transaction_CreateCustomer extends Am_Paysystem_Transaction_CreditCard
{
    public function __construct(Am_Paysystem_Paymaya $plugin, Invoice $invoice)
    {
        $request = $plugin->createRequest($plugin->getEndpoint('/payments/v1/customers'), Am_HttpRequest::METHOD_POST);
        
        $request->setBody(json_encode([
            "firstName" => $invoice->getUser()->name_f,
            "lastName" => $invoice->getUser()->name_l,
            "contact" => [
                "email" => $invoice->getUser()->email
            ]
        ]));
        parent::__construct($plugin, $invoice, $request, true);
    }
    
    
    public function parseResponse()
    {
        $this->parsedResponse = json_decode($this->response->getBody(), true);
    }
    
    function validate()
    {
        if (empty($this->parsedResponse['id'])) {
            $this->result->setFailed($this->parsedResponse['message']);
        } else {
            $this->result->setSuccess($this);
        }
        
    }
    
    function getUniqId()
    {
        return $this->parsedResponse['id'];
    }
    
    function processValidated()
    {
        $this->getInvoice()->getUser()->data()->set(Am_Paysystem_Paymaya::DATA_CUSTOMER_ID,
            $this->getUniqId())->update();
    }
}


class Am_Paysystem_Paymaya_Transaction_LinkCardToCustomer extends Am_Paysystem_Transaction_CreditCard
{
    public function __construct(Am_Paysystem_Paymaya $plugin, Invoice $invoice, $paymentToken)
    {
        $request = $plugin->createRequest($plugin->getEndpoint('/payments/v1/customers/' . $invoice->getUser()->data()->get(Am_Paysystem_Paymaya::DATA_CUSTOMER_ID) . '/cards'));
        
        $request->setBody(json_encode([
            'paymentTokenId' => $paymentToken,
            'isDefault' => true,
            'redirectUrl' => [
                'success' => $plugin->getPluginUrl('success', ['invoice' => $invoice->getSecureId('PAY-SUCCESS')]),
                'failure' => $plugin->getPluginUrl('failure', ['invoice' => $invoice->getSecureId('PAY-FAILURE')]),
                'cancel' => $plugin->getCancelUrl()
            ]
        ]));
        parent::__construct($plugin, $invoice, $request, true);
    }
    
    
    public function parseResponse()
    {
        $this->parsedResponse = json_decode($this->response->getBody(), true);
    }
    
    function validate()
    {
        if (empty($this->parsedResponse['cardTokenId'])) {
            $this->result->setFailed($this->parsedResponse['message']);
        } else {
            if ($this->parsedResponse['verificationUrl']) {
                $this->processValidated();
                $this->result->setAction(new Am_Paysystem_Action_Redirect($this->parsedResponse['verificationUrl']));
            } else {
                $this->result->setSuccess($this);
            }
        }
        
    }
    
    function getUniqId()
    {
        return $this->parsedResponse['cardTokenId'];
    }
    
    function processValidated()
    {
        $this->getInvoice()->data()->set(Am_Paysystem_Paymaya::DATA_CARD_ID, $this->getUniqId())->update();
    }
}

class Am_Paysystem_Paymaya_Transaction_Payment extends Am_Paysystem_Transaction_CreditCard
{
    public function __construct(Am_Paysystem_Paymaya $plugin, Invoice $invoice, $doFirst)
    {
        $request = $plugin->createRequest($plugin->getEndpoint(
            '/payments/v1/customers/'
            . $invoice->getUser()->data()->get(Am_Paysystem_Paymaya::DATA_CUSTOMER_ID)
            . '/cards/'
            . $invoice->data()->get(Am_Paysystem_Paymaya::DATA_CARD_ID)
            . '/payments'
        ));
        $request->setBody(json_encode([
            'totalAmount' => [
                'amount' => $doFirst ? $invoice->first_total : $invoice->second_total,
                'currency' => $invoice->currency
            ]
        ]));
        parent::__construct($plugin, $invoice, $request, $doFirst);
    }
    
    
    public function parseResponse()
    {
        $this->parsedResponse = json_decode($this->response->getBody(), true);
    }
    
    function validate()
    {
        if ($this->parsedResponse['status'] != 'PAYMENT_SUCCESS') {
            $this->result->setFailed('Declined');
        } else {
            $this->result->setSuccess($this);
        }
    }
    
    function getUniqId()
    {
        return $this->parsedResponse['id'];
    }
    
}

class Am_Paysystem_Paymaya_Transaction_Refund extends Am_Paysystem_Transaction_CreditCard
{
    public function __construct(Am_Paysystem_Paymaya $plugin, Invoice $invoice, $payment_id, $amount)
    {
        $this->payment_id = $payment_id;
        $this->amount = $amount;
        $request = $plugin->createRequest($plugin->getEndpoint(
            '/payments/v1/payments/' . $payment_id . '/refunds'
        ));
        
        $request->setBody(json_encode([
            'totalAmount' => [
                'amount' => $amount,
                'currency' => $invoice->currency
            ],
            'reason' => 'Refund'
        ]));
        parent::__construct($plugin, $invoice, $request, true);
    }
    
    
    public function parseResponse()
    {
        $this->parsedResponse = json_decode($this->response->getBody(), true);
    }
    
    function validate()
    {
        if ($this->parsedResponse['status'] != 'SUCCESS') {
            $this->result->setFailed('Failed');
        } else {
            $this->result->setSuccess($this);
        }
    }
    
    function getUniqId()
    {
        return $this->parsedResponse['id'];
    }
    
    function processValidated()
    {
        $this->getInvoice()->addRefund($this, $this->payment_id, $this->amount);
    }
    
}


class Am_FormCreditCard_Paymaya extends Am_Form_CreditCard
{
    public function init()
    {
        
        if ($this->formType == self::ADMIN_UPDATE) {
            $group = $this->addGroup()
                ->setLabel(___("Credit Card Number\n" .
                    "for example: 1111-2222-3333-4444"));
            $group->addStatic()->setContent('<div>');
            $group->addStatic('cc');
            $cc = $group->addText('cc_number',
                ['autocomplete' => 'off', 'size' => 22, 'maxlength' => 22, 'style' => 'display:none']);
            $group->addScript("")->setScript(<<<CUT
jQuery(function(){
    jQuery("input#cc_number-0").closest(".am-element").click(function(){
        var input = jQuery("input#cc_number-0").detach();
        jQuery(this).empty().append(input.show());
        input.focus();
        jQuery(this).unbind('click');
    });
});
CUT
            );
            $group->addStatic()->setContent('</div>');
        } else {
            $this->addText('cc_number', ['autocomplete' => 'off', 'size' => 22, 'maxlength' => 22])
                ->setLabel(___("Credit Card Number\n" .
                    'for example: 1111-2222-3333-4444'));
        }
        
        $this->addElement(new Am_Form_Element_CreditCardExpire('cc_expire', null, ['dont_require' => true]))
            ->setLabel(___("Card Expire\n" .
                'Select card expiration date - month and year'));
        
        
        $code = $this->addPassword('cc_code', ['autocomplete' => 'off', 'size' => 4, 'maxlength' => 4])
            ->setLabel(___("Credit Card Code\n" .
                'The "Card Code" is a three- or four-digit security code ' .
                'that is printed on the back of credit cards in the card\'s ' .
                'signature panel (or on the front for American Express cards)'));
        // if free trial set _TPL_CC_INFO_SUBMIT_BUT2
        $buttons = $this->addGroup();
        $buttons->setSeparator(' ');
        $buttons->addSubmit('_cc_', ['value' => $this->payButtons[$this->formType], 'class' => 'am-cta-pay']);
        if ($this->formType == self::USER_UPDATE) {
            $buttons->addStatic()
                ->setContent(sprintf('<a href="javascript:;" onclick="goBackToMember()">%s</a>', ___("Back")));
            $this->addScript("")->setScript("function goBackToMember(){ window.location = amUrl('/member'); }");
        }
        $this->plugin->onFormInit($this);
    }
    
}
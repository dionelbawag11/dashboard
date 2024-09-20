<?php

/**
 * @title Bundle Discount
 * @desc plugin allow admin to set up some discount if user buy defined number of products at once
 * @tags sign up, up sell
 * @setup_url admin-bundle-discount-adv
 * @am_plugin_api 6.0
 */
class Am_Plugin_BundleDiscountAdv extends Am_Plugin
{
    const PLUGIN_STATUS = self::STATUS_BETA;
    const PLUGIN_COMM = self::COMM_COMMERCIAL;
    const PLUGIN_REVISION = '6.3.20';

    const VERSION = 2;

    const ADMIN_PERM_ID = 'bundle-discount-adv';

    protected $_configPrefix = 'misc.';

    static function getDbXml()
    {
        return <<<CUT
<schema version="4.0.0">
    <table name="bundle_discount_adv">
        <field name="bd_id" type="int" notnull="1" extra="auto_increment"/>
        <field name="min" type="int"/>
        <field name="skip_first" type="int"/>
        <field name="product_ids" type="varchar" len="255"/>
        <field name="apply_product_ids" type="varchar" len="255"/>
        <field name="discount" type="decimal" len="12,2"/>
        <field name="discount_type" type="enum" len="'percent','number'"/>
        <field name="comment" type="varchar" len="255"/>
        <field name="is_recurring" type="tinyint" notnull="1"/>
        <field name="is_disabled" type="tinyint" notnull="1"/>
        <field name="version" type="tinyint" notnull="1" default="1"/>
        <index name="PRIMARY" unique="1">
            <field name="bd_id" />
        </index>
    </table>
</schema>
CUT;
    }

    function onGetPermissionsList(Am_Event $e)
    {
        $e->addReturn(___('Bundle Discount'), self::ADMIN_PERM_ID);
    }

    public function onAdminMenu(Am_Event $e)
    {
        if ($page = $e->getMenu()->findOneBy('id', 'products')) {
            $page->addPage([
                'id' => 'bundle-discount-adv',
                'module' => 'default',
                'controller' => 'admin-bundle-discount-adv',
                'label' => 'Bundle Discount',
                'resource' => self::ADMIN_PERM_ID,
            ]);
        }
    }

    function onInvoiceGetCalculators(Am_Event_InvoiceGetCalculators $e)
    {
        foreach ($this->getDi()->bundleDiscountAdvTable->matchAll($e->getInvoice()) as $bd)
        {
            $e->insertBeforeTax(new Am_Invoice_Calc_BundleDiscountAdv($bd));
        }
    }

    function onDbUpgrade(Am_Event $e)
    {
        $this->getDi()->db->query(<<<CUT
            UPDATE ?_bundle_discount_adv
                SET apply_product_ids = product_ids, version = 2
                WHERE version = 1;
CUT
        );
    }
}

class Am_Grid_Action_TestBundleDiscountAdv extends Am_Grid_Action_Abstract
{
    protected $type = Am_Grid_Action_Abstract::NORECORD;
    protected $title = 'Test Discount Rules';
    protected $cssClass = "link";

    public function run()
    {
        $f = $this->createForm();
        $f->setDataSources([$this->grid->getCompleteRequest()]);
        echo '<h1>' . ___('Test Discount Rules') . '</h1>';
        if ($f->isSubmitted() && $f->validate() && $this->process($f->getValue()))
            return;
        echo $f;
    }

    function process(array $vars)
    {
        $vars['user'] = filterId($vars['user']);
        $user = Am_Di::getInstance()->userTable->findFirstByLogin($vars['user']);
        if (!$user)
            throw new Am_Exception_InputError("User {$vars['user']} not found");
        /* @var $invoice Invoice */
        $invoice = Am_Di::getInstance()->invoiceTable->createRecord();
        $invoice->setUser($user);
        if ($vars['coupon'])
        {
            $invoice->setCouponCode($vars['coupon']);
            $error = $invoice->validateCoupon();
            if ($error)
                throw new Am_Exception_InputError($error);
        }
        foreach ($vars['product_id'] as $plan_id => $qty)
        {
            $p = Am_Di::getInstance()->billingPlanTable->load($plan_id);
            $pr = $p->getProduct();
            $invoice->add($pr, $qty);
        }

        echo "<pre>";
        echo "Choosen Discount Rules:\n";

        foreach (Am_Di::getInstance()->bundleDiscountAdvTable->matchAll($invoice) as $bd)
        {
            printf("* %s&nbsp;%s %s\n",
                $bd->discount,
                ($bd->discount_type == BundleDiscountAdv::DISCOUNT_PERCENT ?
                    '%' :
                    Am_Di::getInstance()->config->get('currency')),
                $bd->comment
            );
        }

        echo "\n\n";

        $invoice->paysys_id = 'manual';
        $invoice->calculate();

        $invoice->invoice_id = '00000';
        $invoice->public_id = 'TEST';
        $invoice->tm_added = sqlTime('now');

        echo $invoice->render();
        echo
        "\nBilling Terms: " . $invoice->getTerms() .
        "\n" . str_repeat("-", 70) . "\n";
        echo "</pre>";
        return true;
    }

    protected function createForm()
    {
        $f = new Am_Form_Admin;
        $f->addText('user')->setLabel('Enter username of existing user')
            ->addRule('required', 'This field is required');
        $f->addText('coupon')->setLabel('Enter coupon code or leave field empty');

        $f->addElement(new Am_Form_Element_ProductsWithQty('product_id'))
            ->setLabel(___('Choose products to include into test invoice'))
            ->loadOptions(Am_Di::getInstance()->billingPlanTable->selectAllSorted())
            ->addRule('required');
        $f->addSubmit('', ['value' => 'Test']);
        $f->addScript()->setScript(<<<CUT
$(function(){
    $("#user-0" ).autocomplete({
        minLength: 2,
        source: window.rootUrl + "/admin-users/autocomplete"
    });
});
CUT
        );
        foreach ($this->grid->getVariablesList() as $k)
        {
            $kk = $this->grid->getId() . '_' . $k;
            if ($v = @$_REQUEST[$kk])
                $f->addHidden($kk)->setValue($v);
        }
        return $f;
    }
}

class AdminBundleDiscountAdvController extends Am_Mvc_Controller_Grid
{
    public function checkAdminPermissions(Admin $admin)
    {
        return $admin->hasPermission(Am_Plugin_BundleDiscountAdv::ADMIN_PERM_ID);
    }

    public function createGrid()
    {
        $ds = new Am_Query($this->getDi()->bundleDiscountAdvTable);
        $grid = new Am_Grid_Editable('_bd', 'Bundle Discount', $ds, $this->_request, $this->view, $this->getDi());
        $grid->setPermissionId(Am_Plugin_BundleDiscountAdv::ADMIN_PERM_ID);
        $grid->addField('min', ___('Condition'), true, '', '', '5%');
        $grid->addField('product_ids', ___('Conditional Products'), true, '', [$this, 'renderProducts']);
        $grid->addField('apply_product_ids', ___('Apply Products'), true, '', [$this, 'renderProducts']);
        $grid->addField('discount', ___('Discount'), true, '', [$this, 'renderDiscount'], '5%');
        $grid->addField('is_recurring', ___('Recurring'), true, 'center', null, '1%');
        $grid->addField('comment', 'Comment');
        $grid->addField(new Am_Grid_Field_IsDisabled());
        $grid->setForm([$this, 'createForm']);
        $grid->actionAdd(new Am_Grid_Action_LiveEdit('comment'));
        $grid->actionAdd(new Am_Grid_Action_TestBundleDiscountAdv);
        $grid->actionAdd(new Am_Grid_Action_LiveCheckbox('is_recurring'));
        $grid->setFormValueCallback('product_ids', ['RECORD', 'unserializeList'], ['RECORD', 'serializeList']);
        $grid->setFormValueCallback('apply_product_ids', ['RECORD', 'unserializeList'], ['RECORD', 'serializeList']);

        $grid->addCallback(Am_Grid_Editable::CB_VALUES_FROM_FORM, function (& $vars, $r) {
            $vars['version'] = Am_Plugin_BundleDiscountAdv::VERSION;
        });
        return $grid;
    }

    function renderDiscount($record)
    {
        return sprintf("<td>%s&nbsp;%s</td>",
            $record->discount,
            ($record->discount_type == BundleDiscountAdv::DISCOUNT_PERCENT ? '%' : $this->getDi()->config->get('currency'))
        );
    }

    function renderProducts($record, $fn, $f, $fo)
    {
        /* @var $record BundleDiscountAdv */
        $product_ids = $record->getOnlyApplicableProductIds($fn);
        $category_ids = $record->getOnlyApplicableCategoryIds($fn);

        $res = [];

        if ($product_ids)
        {
            $titles = $this->getDi()->productTable->getProductTitles($product_ids);
            $titles = implode(', ', $titles);
            $res[] = sprintf("<strong>%s:</strong> %s", ___('Products'), $titles);
        }

        if ($category_ids)
        {
            $options = $this->getDi()->productCategoryTable->getAdminSelectOptions();
            $titles = [];
            foreach ($category_ids as $id)
                $titles[] = $options[$id];

            $titles = implode(', ', $titles);
            $res[] = sprintf("<strong>%s:</strong> %s", ___('Product Categories'), $titles);
        }

        if (!$product_ids && !$category_ids)
        {
            $res[] = sprintf('<strong>%s</strong>', ___('All'));
        }

        return sprintf('<td>%s</td>',
            implode('; ', $res)
        );
    }

    function createForm()
    {
        $form = new Am_Form_Admin('bundle-discount-adv');

        $form->addText('min', ['size' => 5])
            ->setLabel('Minimum Number of Items in invoice to apply discount');

        $product_categories = [];
        foreach ($this->getDi()->productCategoryTable->getAdminSelectOptions() as $id => $title)
        {
            $product_categories['CATEGORY-' . $id] = $title;
        }

        $form->addMagicSelect('product_ids',
                [
                    'multiple' => 1,
                    'class' => 'am-combobox'
                ])
            ->loadOptions([
                ___('Products') => Am_Di::getInstance()->productTable->getOptions(),
                ___('Product Categories') => $product_categories
            ])
            ->setLabel(___("Condition Products\n" .
                    "this rule will count only selected products to match.\n" .
                    "if nothing selected, rule will use all products"));

        $form->addMagicSelect('apply_product_ids',
                [
                    'multiple' => 1,
                    'class' => 'am-combobox'
                ])
            ->loadOptions([
                ___('Products') => Am_Di::getInstance()->productTable->getOptions(),
                ___('Product Categories') => $product_categories
            ])
            ->setLabel(___("Apply Discount to Products\n" .
                    "this rule will apply discount only to selected products.\n" .
                    "if nothing selected, apply discount to all products"));
        $form->addInteger('skip_first')
            ->setLabel("Do not apply discount to first ... suitable items\n" .
                "keep it empty to apply to all suitable items");

        $discountGroup = $form->addGroup()
                ->setLabel([
                    ___("Discount\n" .
                        "this discount will be applied to each item that present in condition\n" .
                        "In case of discount is more than total of item - discount will be completely ignored\n" .
                        "if user use coupon with purchase - max discount will be used,\n" .
                        "in case of several discount rules is applicable to same invoice - max discount will be used")
                ]);
        $discountGroup->setSeparator(' ');
        $discountGroup->addText('discount', ['size' => 5])
            ->addRule('gt', ___('must be greater than 0'), 0);
        $discountGroup->addSelect('discount_type')
            ->loadOptions([
                BundleDiscountAdv::DISCOUNT_PERCENT => '%',
                BundleDiscountAdv::DISCOUNT_NUMBER => Am_Currency::getDefault()
            ]);
        $form->addAdvCheckbox('is_recurring')
            ->setLabel(___("Apply to recurring?\napply coupon discount to recurring rebills"));
        $form->addTextarea('comment', ['rows' => 10, 'class'=> 'am-el-wide el-wide'])
            ->setLabel(___("Comment\nfor admin reference"));

        return $form;
    }
}

class BundleDiscountAdvTable extends Am_Table
{
    protected $_table = '?_bundle_discount_adv';
    protected $_recordClass = 'BundleDiscountAdv';
    protected $_key = 'bd_id';

    /**
     * Retrieve discount rules applicable to given invoice
     *
     * @param Invoice $invoice
     * @return array|null
     */
    function matchAll(Invoice $invoice)
    {
        $res = [];
        foreach ($this->findBy(['is_disabled' => 0]) as $record)
        {
            if ($record->match($invoice))
                $res[] = $record;
        }

        return $res;
    }
}

/**
 * @property int $bd_id
 * @property string $comment
 * @property int $min
 * @property string $product_ids
 * @property float $discount
 * @property enum $discount_type
 * @property bool $is_disabled
 */
class BundleDiscountAdv extends Am_Record
{
    const DISCOUNT_NUMBER = 'number';
    const DISCOUNT_PERCENT = 'percent';

    function match(Invoice $invoice)
    {
        $product_ids = $this->getApplicableProductIds('product_ids');
        $cnt = 0;

        /* @var $item InvoiceItem */
        foreach ($invoice->getItems() as $item)
        {
            if ($item->item_type == 'product' && in_array($item->item_id, $product_ids))
            {
                $cnt += $item->qty;
            }
        }

        return $cnt >= $this->min;
    }

    function calculate($amount, $qty)
    {
        $total = $amount * $qty;
        switch ($this->discount_type)
        {
            case self::DISCOUNT_PERCENT :
                $discount = moneyRound($total * $this->discount / 100);
                break;
            case self::DISCOUNT_NUMBER :
                $discount = moneyRound($qty * $this->discount);
                break;
            default:
                throw new Am_Exception_InternalError(sprintf('Unknown discount type [%s]', $this->discount_type));
        }

        if ($discount > $total)
        {
            $discount = 0;
        }

        return $discount;
    }

    function getApplicableProductIds($fn = 'product_ids')
    {
        //return all product ids in case this field is empty
        if (!$this->$fn)
            return array_keys($this->getDi()->productTable->getOptions());

        $catProducts = $this->getDi()->productCategoryTable->getCategoryProducts();
        $res = array_filter(explode(',', $this->$fn));
        foreach ($res as $k => $g)
        {
            if (preg_match('/CATEGORY-(\d*)/', $g, $match))
            {
                unset($res[$k]);
                $catId = $match[1];
                foreach ($catProducts[$catId] as $prId)
                {
                    $res[] = $prId;
                }
            }
        }
        return $res;
    }

    function getOnlyApplicableProductIds($fn = 'product_ids')
    {
        $res = array_filter(explode(',', $this->$fn));
        $result = [];
        foreach ($res as $k => $g)
        {
            if (!preg_match('/CATEGORY-(\d*)/', $g, $match))
            {
                $result[] = $g;
            }
        }
        return $result;
    }

    function getOnlyApplicableCategoryIds($fn = 'product_ids')
    {
        $res = array_filter(explode(',', $this->$fn));
        $result = [];
        foreach ($res as $k => $g)
        {
            if (preg_match('/CATEGORY-(\d*)/', $g, $match))
            {
                $result[] = $match[1];
            }
        }
        return $result;
    }
}

class Am_Invoice_Calc_BundleDiscountAdv extends Am_Invoice_Calc
{
    /** @var BundleDiscountAdv */
    protected $bundelDiscount = null;

    public function __construct(BundleDiscountAdv $bundelDiscount)
    {
        $this->bundelDiscount = $bundelDiscount;
    }

    public function calculate(Invoice $invoiceBill)
    {
        /* @var $bundelDiscount BundleDiscountAdv */
        $bundelDiscount = $this->bundelDiscount;
        $product_ids = $bundelDiscount->getApplicableProductIds('apply_product_ids');

        $item_num = 0;

        foreach ($invoiceBill->getItems() as $item)
        {
            if ($item->item_type == 'product' && in_array($item->item_id, $product_ids)) {
                $item_num++;
                if ($item_num <= $bundelDiscount->skip_first) continue;
            }

            if ($item->item_type == 'product' && in_array($item->item_id, $product_ids))
            {
                $discount = $bundelDiscount->calculate($item->first_price, $item->qty);
                if ($discount > $item->first_discount)
                    $item->first_discount = $discount;
            }

            if ($bundelDiscount->is_recurring &&
                $item->second_price > 0 &&
                $item->item_type == 'product' &&
                in_array($item->item_id, $product_ids)) {

                $discount = $bundelDiscount->calculate($item->second_price, $item->qty);
                if ($discount > $item->second_discount)
                    $item->second_discount = $discount;
            }
        }

        foreach ($invoiceBill->getItems() as $item)
        {
            $item->_calculateTotal();
        }
    }
}
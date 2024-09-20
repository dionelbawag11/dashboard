<?php

/**
 * @am_plugin_api 6.0
*/
class Am_Plugin_FieldRevision extends Am_Plugin
{
    const PLUGIN_STATUS = self::STATUS_BETA;
    const PLUGIN_REVISION = '6.3.20';
    const FIELD_DATA_PREFIX = '__data_';
    const ADMIN_PERM_ID = 'field-revision';

    protected $_configPrefix = 'misc.';

    static function getDbXml()
    {
        return <<<CUT
<schema version="4.0.0">
    <table name="field_revision">
        <field name="field_revision_id" type="int" notnull="1" extra="auto_increment"/>
        <field name="dattm" type="datetime"/>
        <field name="remote_addr" type="varchar" len="39"/>
        <field name="user_id" type="int"/>
        <field name="field" type="varchar" len="255"/>
        <field name="value_old" type="varchar" len="255"/>
        <field name="value_new" type="varchar" len="255"/>
        <index name="PRIMARY" unique="1">
            <field name="field_revision_id" />
        </index>
    </table>
</schema>
CUT;
    }

    function init()
    {
        parent::init();
        Am_View_Helper_Icon::$src['field-revision-all'] =
        'data:image/png;base64,' .
        'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAACjSURBVDjL7ZNBCsIwEEVz1mwTo1YjiHdIqyWgFBGPonWTC8T2BjlE4JsUwU0ILe7ExUtgPvNmNkMAkG8gPyAwxiAHYwxKKUgpk/kg8N5n4Zwn6865j4CVLXj1AA//rArsW4hAzCil4wTFsUdx6rBuLLaXJ+aH+zTBqukDFpuzxe5qsagnCIbV32vHybF5Wd/GC3JkBfHJEZu11hBCJHPyvwXyAt6tONifnq6xAAAAAElFTkSuQmCC';
    }

    function onGetPermissionsList(Am_Event $e)
    {
        $e->addReturn('Field Revision', self::ADMIN_PERM_ID);
    }

    function getTitle()
    {
        return 'Field Revision';
    }

    function _initSetupForm(Am_Form_Setup $form)
    {
        $form->addMagicSelect('fields')
            ->setLabel(___('Fields to track'))
            ->loadOptions($this->getFields());
    }

    public function getFields()
    {
        $fields = array(
            'email' => ___('E-Mail'),
            'login' => ___('Username'),
            'name_f' => ___('First Name'),
            'name_l' =>  ___('Last Name'),
            'street' =>  ___('Street'),
            'city' => ___('City'),
            'state' =>  ___('State'),
            'zip' => ___('ZIP Code'),
            'country' => ___('Country'),
            'phone' => ___('Phone'),
            'unsubscribed' => ___('Unsubscribed'),
            'lang' => ___('Language'),
            'is_locked' => ___('Is Locked'),
            'comment' => ___('Comment'),
            'aff_id' => ___('Affiliate Id#')
        );

        //Additional Fields
        foreach ($this->getDi()->userTable->customFields()->getAll() as $field) {
            !empty($field->sql) ?
                $fields[$field->name] = $field->title :
                $fields[self::FIELD_DATA_PREFIX . $field->name] = $field->title;
        }
        return $fields;
    }

    public function onUserBeforeUpdate($event)
    {
        $user = $event->getUser();
        $oldUser = $event->getOldUser();

        foreach($this->getConfig('fields') as $field) {
            if (strpos($field, self::FIELD_DATA_PREFIX) === 0) {
                $fieldname = str_replace(self::FIELD_DATA_PREFIX, '', $field);
                $oldVal = $oldUser->data()->get($fieldname);
                $newVal = $user->data()->get($fieldname);
            } else {
                $fieldname = $field;
                $oldVal = $oldUser->{$field};
                $newVal = $user->{$field};
            }
            if ($oldVal != $newVal) {
                $vars = $this->getDi()->userTable->customFields()->valuesToTable(array($fieldname => $oldVal));
                $oldVal = $vars[$fieldname];
                $vars = $this->getDi()->userTable->customFields()->valuesToTable(array($fieldname => $newVal));
                $newVal = $vars[$fieldname];

                $revision = $this->getDi()->fieldRevisionRecord;
                $revision->dattm = sqlTime('now');
                $revision->remote_addr = $_SERVER['REMOTE_ADDR'] ?: null;
                $revision->field = $field;
                $revision->user_id = $user->pk();

                $revision->value_old = $oldVal;
                $revision->value_new = $newVal;
                $revision->save();
            }
        }
    }

    function onUserTabs(Am_Event_UserTabs $e)
    {
        if ($e->getUserId() > 0) {
            $e->getTabs()->addPage(array(
                'id' => 'field-revision',
                'controller' => 'admin-field-revision',
                'action' => 'index',
                'params' => array(
                    'user_id' => $e->getUserId(),
                ),
                'resource' => self::ADMIN_PERM_ID,
                'label' => ___('Field Revisions'),
                'order' => 1000
            ));
        }
    }

    function onAdminMenu(Am_Event $e)
    {
        if ($p = $e->getMenu()->findOneById('utilites')) {
            $p->addPage(array(
                'id' => 'field-revision-all',
                'resource' => self::ADMIN_PERM_ID,
                'module' => 'default',
                'controller' => 'admin-field-revision-all',
                'action' => 'index',
                'label' => 'User Field Revions',
                'order' => 150
            ));
        }
    }

}

class FieldRevisionTable extends Am_Table
{
    protected $_table = '?_field_revision';
    protected $_recordClass = 'FieldRevision';
    protected $_key = 'field_revision_id';
}

class FieldRevision extends Am_Record {}

class AdminFieldRevisionController extends Am_Mvc_Controller_Grid
{
    protected $layout = 'admin/user-layout.phtml';

    public function checkAdminPermissions(Admin $admin)
    {
        return $admin->hasPermission(Am_Plugin_FieldRevision::ADMIN_PERM_ID);
    }

    function preDispatch()
    {
        $this->setActiveMenu('users-browse');

        $this->user_id = $this->getInt('user_id');
        if (!$this->user_id) {
            throw new Am_Exception_InputError("Wrong URL specified: no member# passed");
        }

        parent::preDispatch();
    }

    function createGrid()
    {
        $ds = new Am_Query($this->getDi()->fieldRevisionTable);
        $ds = $ds->addWhere('user_id=?', $this->user_id);
        $ds->setOrder('dattm', true);
        $grid = new Am_Grid_Editable('_fr', ___('Field Revisions'), $ds, $this->getRequest(), $this->getView(), $this->getDi());
        $grid->setPermissionId(Am_Plugin_FieldRevision::ADMIN_PERM_ID);
        $grid->setEventId('gridFieldRevision');
        $grid->actionsClear();
        $grid->addField(new Am_Grid_Field_Date('dattm', ___('Date')));
        $grid->addField('remote_addr', ___('IP'));
        $grid->addField(new Am_Grid_Field_Enum('field', ___('Field')))
            ->setTranslations($this->getDi()->plugins_misc->loadGet('field-revision')->getFields());
        $grid->addField('value_old', ___('Old Value'));
        $grid->addField('value_new', ___('New Value'));
        return $grid;
    }
}

class AdminFieldRevisionAllController extends Am_Mvc_Controller_Grid
{
    public function checkAdminPermissions(Admin $admin)
    {
        return $admin->hasPermission(Am_Plugin_FieldRevision::ADMIN_PERM_ID);
    }

    function createGrid()
    {
        $ds = new Am_Query($this->getDi()->fieldRevisionTable);
        $ds->leftJoin('?_user', 'u', 't.user_id=u.user_id')
            ->addField('u.login', 'u_login')
            ->addField('u.name_f', 'u_name_f')
            ->addField('u.name_l', 'u_name_l');
        $ds->setOrder('dattm', true);
        $grid = new Am_Grid_Editable('_afr', ___('Field Revisions'), $ds, $this->getRequest(), $this->getView(), $this->getDi());
        $grid->setPermissionId(Am_Plugin_FieldRevision::ADMIN_PERM_ID);
        $grid->setEventId('gridFieldRevisionAll');
        $grid->actionsClear();
        $grid->addField(new Am_Grid_Field_Date('dattm', ___('Date')));
        $grid->addField('remote_addr', ___('IP'));
        $grid->addField('user_id', ___('User'))
            ->setRenderFunction(array($this, 'renderUser'));
        $grid->addField(new Am_Grid_Field_Enum('field', ___('Field')))
            ->setTranslations($this->getDi()->plugins_misc->loadGet('field-revision')->getFields());
        $grid->addField('value_old', ___('Old Value'));
        $grid->addField('value_new', ___('New Value'));
        return $grid;
    }

    public function renderUser($r, $fieldName, $g, $fieldObj)
    {
        return sprintf('<td><a class="link" href="%s" target="_top">%s (%s %s)</a></td>',
            $this->getView()->userUrl($r->user_id),
                Am_Html::escape($r->u_login),
                Am_Html::escape($r->u_name_f),
                Am_Html::escape($r->u_name_l)
            );
    }
}
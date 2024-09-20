<?php

/**
 * Plugin add new form brick "File Upload".
 * User can upload some file here.
 *
 * Then admin can see list of uploaded files
 * in user edit screen in admin interface
 * in tab Uploaded Files
 * @am_plugin_api 6.0
 */
class Am_Plugin_FileUpload extends Am_Plugin
{
    const PLUGIN_STATUS = self::STATUS_PRODUCTION;
    const PLUGIN_COMM = self::COMM_COMMERCIAL;
    const PLUGIN_REVISION = '6.3.20';

    const UPLOAD_PREFIX = 'file-upload';
    const UPLOAD_PREFIX_PUBLIC = 'file-upload-public';
    const ADMIN_PERM_ID = 'file-upload';
    protected $_configPrefix = 'misc.';

    static function getEtXml()
    {
        return <<<CUT
<table_data name="email_template">
    <row type="email_template">
        <field name="name">misc.file-upload.notify_admin</field>
        <field name="email_template_layout_id">2</field>
        <field name="lang">en</field>
        <field name="format">text</field>
        <field name="subject">%site_title%: New File Uploaded (%user.name_f% %user.name_l%)</field>
        <field name="txt">
User %user.name_f% %user.name_l% (%user.login%)
uploaded new file(s):  %file_name%
        </field>
    </row>
</table_data>
CUT;
    }

    static function getDbXml()
    {
        return <<<CUT
<schema version="4.0.0">
    <table name="user">
        <field name="last_file_upload" type="datetime" notnull="0" />
    </table>
</schema>
CUT;
    }

    function onSetupEmailTemplateTypes(Am_Event $e)
    {
        $e->addReturn([
                'id' => 'misc.file-upload.notify_admin',
                'title' => 'Notify Admin about New File',
                'mailPeriodic' => Am_Mail::ADMIN_REQUESTED,
                'isAdmin' => true,
                'vars' => [
                    'user',
                        'file_name' => ___('Uploaded File Names'),
                        'file_category' => ___('Uploaded File Category')
                ],
        ], 'misc.file-upload.notify_admin');

        foreach ($this->getCategories() as $op) {
            $e->addReturn([
                    'id' => 'misc.file-upload.' . sha1($op),
                    'title' => sprintf('Notify Admin about New File (%s)', $op),
                    'mailPeriodic' => Am_Mail::ADMIN_REQUESTED,
                    'isAdmin' => true,
                    'vars' => [
                        'user',
                        'file_name' => ___('Uploaded File Names'),
                        'file_category' => ___('Uploaded File Category')
                    ],
            ], 'misc.file-upload.' . sha1($op));
        }
    }

    function onGetUploadPrefixList(Am_Event $e)
    {
        $e->addReturn([
                Am_Upload_Acl::IDENTITY_TYPE_ADMIN => Am_Upload_Acl::ACCESS_ALL,
                Am_Upload_Acl::IDENTITY_TYPE_USER => Am_Upload_Acl::ACCESS_WRITE | Am_Upload_Acl::ACCESS_READ_OWN,
                Am_Upload_Acl::IDENTITY_TYPE_ANONYMOUS => Am_Upload_Acl::ACCESS_WRITE | Am_Upload_Acl::ACCESS_READ_OWN
        ], self::UPLOAD_PREFIX);

        $e->addReturn([
                Am_Upload_Acl::IDENTITY_TYPE_ADMIN => Am_Upload_Acl::ACCESS_ALL,
                Am_Upload_Acl::IDENTITY_TYPE_USER => Am_Upload_Acl::ACCESS_ALL,
                Am_Upload_Acl::IDENTITY_TYPE_ANONYMOUS => Am_Upload_Acl::ACCESS_ALL
        ], self::UPLOAD_PREFIX_PUBLIC);
    }

    function _initSetupForm(Am_Form_Setup $form)
    {
        $form->setTitle(___('File Upload'));
        $form->addElement('email_checkbox', 'notify_admin')
            ->setLabel(___('Notify admin about new upload'));

        $form->addTextarea('category', ['class' => 'am-el-wide', 'rows' => 7, 'class'=>'one-per-line'])
                ->setlabel(___("Upload Categories"));

        if ($cats = $this->getCategories()) {
            $form->addMagicSelect('category_public')
                ->setLabel(___("Public Categories\n" .
                    "you need to save new categories before it appear in this list"))
                ->loadOptions($cats);
        }

        $form->addAdvCheckbox('widget')
            ->setLabel(___("Add Widget\nWith Uploaded Files To User Dashboard"));

        $g = $form->addGroup()
            ->setLabel(___("Filename\nmodify original file name"));
        $g->setSeparator('<br />');
        $g->addAdvCheckbox('filename_prepend_username', null, ['content' => 'Prepend Username']);
        $g->addAdvCheckbox('filename_prepend_category', null, ['content' => 'Prepend Category Name']);

        if ($_ = $this->getCategories()) {
            $fs = $form->addAdvFieldset('file-upload');
            $fs->setLabel(___('E-Mail Notification by Category'));

            foreach ($_ as $op) {
                $fs->addElement('email_checkbox', sha1($op))
                    ->setLabel($op);
            }
        }
    }

    function name($upload, $user = null, $category = null)
    {
        $name = $upload->name;
        if ($this->getConfig('filename_prepend_category') && $category) {
            $name = "{$category}-{$name}";
        }
        if ($this->getConfig('filename_prepend_username') && $user) {
            $name = "{$user->login}-{$name}";
        }
        return $name;
    }

    function directAction($request, $response, $invokeArgs)
    {
        switch ($request->getActionName()) {
            case 'get' :
                $upload = $this->getDi()->uploadTable->load($request->getParam('id'));

                if (!in_array($upload->prefix, [self::UPLOAD_PREFIX, self::UPLOAD_PREFIX_PUBLIC]) ||
                    !$this->getDi()->uploadAcl->checkPermission($upload,
                        Am_Upload_Acl::ACCESS_READ,
                        $this->getDi()->auth->getUser())) {

                    throw new Am_Exception_AccessDenied();
                }

                $user = null;
                $category = null;
                if ($uid = $this->getDi()->security->reveal($request->getParam('uid'))) {
                    $user = $this->getDi()->userTable->load($uid);

                    $userFilesCategory = $user->data()->getBlob('file-upload-category');
                    $userFilesCategory = $userFilesCategory  ?
                        unserialize($userFilesCategory) :
                        [];

                    $category = isset($userFilesCategory[$upload->pk()]) ? $userFilesCategory[$upload->pk()] : null;
                }

                $helper = new Am_Mvc_Controller_Action_Helper_SendFile();
                $helper->sendFile($upload->getFullPath(), $upload->getType(),
                    [
                        'disposition' => $request->getParam('d', 'attachment'),
                        'filename' => $this->name($upload, $user, $category),
                    ]);
                break;
            case 'delete':
                $s = $this->getDi()->session->ns($this->getId());
                $uplod_id = $s->{$request->getParam('token')};
                unset($s->{$request->getParam('token')});
                $upload = $this->getDi()->uploadTable->load($uplod_id);
                if (!in_array($upload->prefix, [self::UPLOAD_PREFIX, self::UPLOAD_PREFIX_PUBLIC]) ||
                    !$this->getDi()->uploadAcl->checkPermission($upload,
                        Am_Upload_Acl::ACCESS_WRITE,
                        $this->getDi()->auth->getUser())) {

                    throw new Am_Exception_AccessDenied();
                }
                $this->deleteFile($this->getDi()->auth->getUser(), $upload);
                break;
        }
    }

    function register($upload_id)
    {
        $token = $this->getDi()->security->randomString(8);
        $s = $this->getDi()->session->ns($this->getId());
        $s->$token = $upload_id;
        return $token;
    }

    function deleteFile(User $user, Upload $record)
    {
        $userFiles = unserialize($user->data()->getBlob('file-upload'));
        $userFilesCategory = unserialize($user->data()->getBlob('file-upload-category'));
        unset($userFilesCategory[$record->pk()]);
        foreach ($userFiles as $k => $v) {
            if ($v == $record->pk()) {
                unset($userFiles[$k]);
            }
        }
        $record->delete();
        $user->data()->setBlob('file-upload', $userFiles ? serialize(array_unique($userFiles)) : null);
        $user->data()->setBlob('file-upload-category', $userFilesCategory ? serialize($userFilesCategory) : null);
        $user->data()->update();
    }

    function getCategories()
    {
        $r = array_filter(array_map('trim', explode("\n", $this->getConfig('category'))));
        return $r ? array_combine($r, $r) : [];
    }

    function getUploadPrefix($category)
    {
        return ($category && $this->getConfig('category_public') && in_array($category, $this->getConfig('category_public'))) ?
            self::UPLOAD_PREFIX_PUBLIC :
            self::UPLOAD_PREFIX;
    }

    function onUserTabs(Am_Event_UserTabs $e)
    {
        if ($uid = $e->getUserId()) {
            $_ = $this->getDi()->userTable->load($uid)->data()->getBlob('file-upload');
            $cnt = $_ ? count($f_ = array_unique(unserialize($_))) : 0;
            if($cnt)
                $cnt = $this->getDi()->db->selectCell("SELECT count(*) FROM ?_upload where upload_id IN ( ?a )", $f_);
            $e->getTabs()->addPage([
                'id' => 'file-upload',
                'module' => 'default',
                'controller' => 'admin-file-upload',
                'params' => [
                    'user_id' => $e->getUserId(),
                ],
                'label' => ___('Uploaded Files') . ($cnt ? " (($cnt))" : ''),
                'order' => 1000
            ]);
        }
    }

    function saveUploads(Am_Event $e, $name = '_file-upload', $category = '')
    {
        /* @var $user User */
        $user = $e->getUser();
        $vars = $e->getVars();
        if (isset($vars[$name]) && $vars[$name]) {

            if ($this->getConfig('notify_admin') &&
                $et = Am_Mail_Template::load('misc.file-upload.notify_admin')) {

                $et->setUser($user);

                $file_name = [];
                foreach ($this->getDi()->uploadTable->loadIds((array)$vars[$name]) as $f) {
                    $file_name[] = $f->getName();
                }
                $et->setFile_name(implode(', ', $file_name));
                $et->setFile_category($category);
                $et->sendAdmin();
            }

            $userFiles = $user->data()->getBlob('file-upload');
            $userFiles = $userFiles ? unserialize($userFiles) : [];

            if ($category) {
                $userFilesCategory = $user->data()->getBlob('file-upload-category');
                $userFilesCategory = $userFilesCategory  ? unserialize($userFilesCategory) : [];

                $userFilesCategoryNew = [];
                foreach ((array)$vars[$name] as $id) {
                    $userFilesCategoryNew[$id] = $category;
                }
                $user->data()->setBlob('file-upload-category', serialize($userFilesCategory + $userFilesCategoryNew));
                if ($this->getConfig(sha1($category)) &&
                    $et = Am_Mail_Template::load('misc.file-upload.' . sha1($category))) {

                    $et->setUser($user);

                    $file_name = [];
                    foreach ($this->getDi()->uploadTable->loadIds((array)$vars[$name]) as $f) {
                        $file_name[] = $f->getName();
                    }
                    $et->setFile_name(implode(', ', $file_name));
                    $et->setFile_category($category);
                    $et->sendAdmin();
                }
            }

            $user->updateQuick('last_file_upload', sqlTime('now'));
            $user->data()->setBlob('file-upload', serialize(array_merge($userFiles, (array)$vars[$name])))->update();
        }
    }

    function onGetPermissionsList(Am_Event $e)
    {
        $e->addReturn(___('File-Upload: browse uploaded files'), self::ADMIN_PERM_ID);
    }

    function onGridUserInitGrid(Am_Event_Grid $e)
    {
        $e->getGrid()->actionAdd(new Am_Grid_Action_ExportFiles);
    }

    function onGridUserOnBeforeRun(Am_Event_Grid $e)
    {
        /* @var $grid Am_Grid_Editable */
        $grid = $e->getGrid();

        /* @var $customize Am_Grid_Action_Customize */
        $customize = $grid->actionGet('customize');
        $customize->addField(new Am_Grid_Field_Date('last_file_upload', ___('Last File Upload')));

        /* @var $export Am_Grid_Action_Export */
        $export = $grid->actionGet('export');
        $export->addField(new Am_Grid_Field('last_file_upload', ___('Last File Upload')));
    }

    function onInitFinished()
    {
        if ($this->getConfig('widget')
            && ($user = $this->getDi()->auth->getUser())
            && $user->data()->getBlob('file-upload')) {

            $this->getDi()->blocks->add('member/main/right', new Am_Block_Base(___('Your Personal Uploads'), 'personal-upload', $this, [$this, 'renderBlock']));
        }

        $router = $this->getDi()->front->getRouter();
        $router->addRoute("misc-{$this->getId()}-get", new Am_Mvc_Router_Route(
                "misc/{$this->getId()}/get/:id", [
                    'module' => 'default',
                    'controller' => 'direct',
                    'plugin_id' => $this->getId(),
                    'type' => 'misc',
                    'action' => 'get'
            ]
        ));
        $router->addRoute("misc-{$this->getId()}-delete", new Am_Mvc_Router_Route(
                "misc/{$this->getId()}/delete/:token", [
                    'module' => 'default',
                    'controller' => 'direct',
                    'plugin_id' => $this->getId(),
                    'type' => 'misc',
                    'action' => 'delete'
            ]
        ));
    }

    function renderBlock(Am_View $view)
    {
        $user = $this->getDi()->user;
        $userFiles = unserialize($user->data()->getBlob('file-upload'));
        $files = $this->getDi()->uploadTable->loadIds($userFiles);
        $out = [];
        foreach ($files as $f) {
            $out[] = sprintf('<li><a href="%s">%s</a></li>',
                $this->getDi()->url("misc/{$this->getId()}/get", [
                    'id' => $f->pk(),
                    'uid' => $this->getDi()->security->obfuscate($user->pk())
                ]),
                Am_Html::escape($f->name));
        }

        return sprintf('<ul class="am-widget-list">%s</ul>', implode("\n", $out));
    }

}

class Am_Grid_Action_UploadFileDelete extends Am_Grid_Action_Abstract
{
    protected $user;

    public function __construct($id = null, $title = null)
    {
        $this->title = ___("Delete %s");
        $this->attributes['data-confirm'] = ___("Do you really want to delete record?");
        parent::__construct($id, $title);
    }

    function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function run()
    {
        if ($this->grid->getRequest()->get('confirm')) {
            return $this->delete();
        } else {
            echo $this->renderConfirmation();
        }
    }

    public function delete()
    {
        $record = $this->grid->getRecord();
        $args = [$record, $this->grid];
        $this->grid->runCallback(Am_Grid_Editable::CB_BEFORE_DELETE, $args);
        $this->grid->getDi()->plugins_misc->loadGet('file-upload')->deleteFile($this->user, $record);
        $this->grid->runCallback(Am_Grid_Editable::CB_AFTER_DELETE, $args);
        $this->log();
        $this->grid->redirectBack();
    }
}

class Am_Grid_Action_Group_UploadFileGroupDelete extends Am_Grid_Action_Group_Abstract
{
    protected $id = 'group-delete';
    protected $batchCount = 10;
    protected $privilege = 'delete';

    protected $user;

    public function __construct($id = null, $title = null)
    {
        $this->title = ___('Delete');
        parent::__construct($id, $title);
    }

    public function handleRecord($id, $record)
    {
        $args = [$record, $this->grid];
        $this->grid->runCallback(Am_Grid_Editable::CB_BEFORE_DELETE, $args);
        $this->grid->getDi()->plugins_misc->loadGet('file-upload')->deleteFile($this->user, $record);
        $this->grid->runCallback(Am_Grid_Editable::CB_AFTER_DELETE, $args);
    }

    function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }
}

class Am_Grid_Action_Group_UploadFileGroupDownload extends Am_Grid_Action_Group_Abstract
{
    protected $id = 'group-download';

    protected $needConfirmation = false;

    public function __construct($id = null, $title = null)
    {
        $this->title = ___('Download');
        parent::__construct($id, $title);
    }

    public function run()
    {
        /** @var Am_Query $ds */
        $ds = $this->grid->getDataSource()->getDataSourceQuery();

        $ids = $this->getIds();
        if ($ids[0] != self::ALL) {
            $ds->addWhere('upload_id IN (?a)', $ids);
        }

        $fn = tempnam($this->grid->getDi()->upload_dir, 'zip_');

        $zip = new ZipArchive();
        $zip->open($fn, ZipArchive::OVERWRITE);

        foreach ($ds->selectAllRecords() as $upload) {
            $zip->addFile($upload->getFullPath(), $upload->getName());
        }

        $zip->close();

        register_shutdown_function([$this, 'cleanup'], $fn);

        $helper = new Am_Mvc_Controller_Action_Helper_SendFile();
        $helper->sendFile($fn, 'application/octet-stream', [
            'filename' => sprintf('file-upload-%s.zip', sqlDate('now')),
        ]);
    }

    function handleRecord($id, $record)
    {
        //nop
    }

    public function cleanup($fn)
    {
        unlink($fn);
    }
}

class Am_Grid_Action_UploadFile extends Am_Grid_Action_Abstract
{
    protected $user;
    protected $type = self::NORECORD; // this action does not operate on existing records

    public function __construct($id = null, $title = null)
    {
        $this->title = ___('Upload File');
        parent::__construct($id, $title);
    }

    function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    public function run()
    {
        $form = $this->grid->getForm();
        $form->setAttribute('target', '_top');
        $upload = new Am_Upload($this->grid->getDi());
        $upload->setPrefix(Am_Plugin_FileUpload::UPLOAD_PREFIX);

        if ($form->isSubmitted() && $upload->processSubmit('upload')) {

            [$file] = $upload->getUploads();

            $userFiles = $this->user->data()->getBlob('file-upload');
            $userFiles = $userFiles ? unserialize($userFiles) : [];
            $userFiles[] = $file->pk();
            $this->user->data()->setBlob('file-upload', serialize($userFiles));

            if ($_ = $this->grid->getCompleteRequest()->getParam('category')) {
                $userFilesCategory = $this->user->data()->getBlob('file-upload-category');
                $userFilesCategory = $userFilesCategory ? unserialize($userFilesCategory) : [];
                $userFilesCategory[$file->pk()] = $_;
                $this->user->data()->setBlob('file-upload-category', serialize($userFilesCategory));
            }
            $this->user->save();

            $file->updateQuick('user_id', $this->user->pk());

            return $this->grid->redirectBack();
        }

        echo $this->renderTitle();
        echo $form;
    }
}

class Am_Grid_Action_ExportFiles extends Am_Grid_Action_Abstract
{
    protected $privilege = 'export';
    protected $type = self::HIDDEN;

    public function run()
    {
        $ds = $this->grid->getDataSource();

        $fn = tempnam($this->grid->getDi()->upload_dir, 'zip_');

        $zip = new ZipArchive();
        $zip->open($fn, ZipArchive::OVERWRITE);

        $cnt = 0;
        $st = $ds->query();
        while ($u = $this->grid->getDi()->db->fetchRow($st)) {
            $user = $this->grid->getDi()->userTable->createRecord($u);
            $userFiles = $user->data()->getBlob('file-upload');
            $userFiles = $userFiles ?
                unserialize($userFiles) :
                [];
            if ($userFiles) {
                foreach ($userFiles as $upload_id) {
                    /* @var $upload Upload */
                    $upload = $this->grid->getDi()->uploadTable->load($upload_id, false);
                    if ($upload) {
                        $cnt++;
                        $zip->addFile($upload->getFullPath(), $user->login . '/' . $upload->getName());
                    }
                }
            }
        }

        $zip->close();

        if (!$cnt) {
            $this->cleanup($fn);
            throw new Am_Exception_InputError(___('Selected Users has not any associated files'));
        }

        register_shutdown_function([$this, 'cleanup'], $fn);

        $helper = new Am_Mvc_Controller_Action_Helper_SendFile();
        $helper->sendFile($fn, 'application/octet-stream', [
            'filename' => sprintf('file-upload-%s.zip', sqlDate('now')),
        ]);
    }

    public function cleanup($fn)
    {
        unlink($fn);
    }

    public function setGrid(Am_Grid_Editable $grid)
    {
        parent::setGrid($grid);
        if ($this->hasPermissions()) {
            $grid->addCallback(Am_Grid_ReadOnly::CB_RENDER_TABLE, [$this, 'renderLink']);
        }
    }

    public function renderLink(& $out)
    {
        $out .= sprintf('<div style="float:right">&nbsp;&nbsp;&nbsp;<a class="link" href="%s" target="_top">' . ___('Download Files (.zip)') . '</a></div>', $this->getUrl());
    }
}

class AdminFileUploadController extends Am_Mvc_Controller
{
    public function checkAdminPermissions(Admin $admin)
    {
        return true;
    }

    function indexAction()
    {
        /* @var $user User */
        $user = $this->getDi()->userTable->load($this->getParam('user_id'));
        $userFiles = $user->data()->getBlob('file-upload');
        $userFiles = $userFiles ?
            unserialize($userFiles) :
            [];
        $userFilesCategory = $user->data()->getBlob('file-upload-category');
        $userFilesCategory = $userFilesCategory  ?
            unserialize($userFilesCategory) :
            [];

        array_push($userFiles, -1); //to avoide SQL error

        $conditions = ['WHEN -1 THEN NULL']; //to avoide SQL error
        foreach ($userFilesCategory as $id => $title) {
            $conditions[] = sprintf('WHEN %d THEN %s', $id, $this->getDi()->db->escape($title));
        }
        $conditions = implode("\n", $conditions);

        $ds = new Am_Query($this->getDi()->uploadTable);
        $ds->addField(<<<CUT
(CASE upload_id
    $conditions
    ELSE NULL
END)
CUT
            , 'category');
        $ds = $ds->addWhere('upload_id IN (?a)', $userFiles)
            ->addWhere('prefix IN (?a)', [Am_Plugin_FileUpload::UPLOAD_PREFIX, Am_Plugin_FileUpload::UPLOAD_PREFIX_PUBLIC]);

        $grid = new Am_Grid_Editable('_file-upload', ___('Uploads'), $ds, $this->getRequest(), $this->getView(), $this->getDi());
        $grid->addField('name', ___('Filename'))
            ->setRenderFunction(function ($r, $fn, $g, $fo) use ($user) {

                return sprintf('<td><a href="%s" target="_blank">%s</a>%s</td>',
                    $this->getDi()->url('admin-file-upload/get', ['id' =>$r->pk(), 'user_id' => $user->pk()]),
                    $g->escape($r->name),
                    ($r->prefix == Am_Plugin_FileUpload::UPLOAD_PREFIX_PUBLIC ? ' <span style="color:#aaa">public</span>' : ''));
        });
        $grid->addField(new Am_Grid_Field_Date('uploaded', ___('Uploaded')));
        $grid->addField('category', ___('Category'))
            ->setGetFunction(function($r, $g, $fn, $fo) use ($userFilesCategory) {
                return isset($userFilesCategory[$r->pk()]) ? $userFilesCategory[$r->pk()] : '';
            });
        $grid->setPermissionId(Am_Plugin_FileUpload::ADMIN_PERM_ID);
        $grid->actionsClear();
        $grid->actionAdd(new Am_Grid_Action_UploadFileDelete('delete'))
            ->setUser($user)
            ->setTarget('_top');
        $grid->actionAdd(new Am_Grid_Action_UploadFile)
            ->setUser($user);
        $grid->actionAdd(new Am_Grid_Action_Group_UploadFileGroupDelete)
            ->setUser($user);
        $grid->actionAdd(new Am_Grid_Action_Group_UploadFileGroupDownload)
            ->setTarget('_top');

        //some category can be already removed from config
        $allCats = array_merge($this->getDi()->plugins_misc->loadGet('file-upload')->getCategories(), $userFilesCategory);

        $grid->actionAdd(new Am_Grid_Action_LiveSelect('category'))
            ->setOptions([''=> ''] + array_combine($allCats, $allCats))
            ->setUpdateCallback(function($ds, $record, $fieldname, $v) use ($user) {
                $userFilesCategory = $user->data()->getBlob('file-upload-category');
                $userFilesCategory = $userFilesCategory  ?
                    unserialize($userFilesCategory) :
                    [];
                $userFilesCategory[$record->pk()] = $v;
                $user->data()->setBlob('file-upload-category', serialize($userFilesCategory));
                $user->save();
            });

        $grid->setForm([$this, 'createForm']);

        $grid->runWithLayout('admin/user-layout.phtml');
    }

    public function getAction()
    {
        $file = $this->getDi()->uploadTable->load($this->getParam('id'));

        if (!$this->getDi()->uploadAcl->checkPermission($file,
                    Am_Upload_Acl::ACCESS_READ,
                    $this->getDi()->authAdmin->getUser())) {
            throw new Am_Exception_AccessDenied();
        }

        $user = $this->getDi()->userTable->load($this->getParam('user_id'));

        $userFilesCategory = $user->data()->getBlob('file-upload-category');
        $userFilesCategory = $userFilesCategory  ?
            unserialize($userFilesCategory) :
            [];

        $this->_helper->sendFile($file->getFullPath(), $file->getType(),
            ['filename' => $this->getPlugin()->name($file, $user, isset($userFilesCategory[$file->pk()]) ? $userFilesCategory[$file->pk()] : null)]);

        exit;
    }

    public function createForm()
    {
        $form = new Am_Form_Admin();
        $form->setAttribute('enctype', 'multipart/form-data');
        $file = $form->addElement('file', 'upload[]')
                ->setLabel(___('File'))
                ->setAttribute('class', 'styled');
        $file->addRule('required');
        if ($_ = $this->getDi()->plugins_misc->loadGet('file-upload')->getCategories()) {
            $form->addSelect('category')
                ->setLabel(___('Category'))
                ->loadOptions([''=> ''] + $_);
        }
        return $form;
    }

    function getPlugin()
    {
        return $this->getDi()->plugins_misc->loadGet('file-upload');
    }
}

Am_Di::getInstance()->hook->add(Am_Event::USER_SEARCH_CONDITIONS, function (Am_Event $e) {

if (!class_exists('Am_Query_User_Condition_IsFileUploaded', false)) :
    class Am_Query_User_Condition_IsFileUploaded extends Am_Query_Condition
        implements Am_Query_Renderable_Condition
    {
        protected $is_a = null;
        protected $options = [];

        function __construct()
        {
            $this->title = ___('Have uploaded at least 1 file');
            $this->options = [
                0 => ___('No'),
                1 => ___('Yes'),
            ];
        }

        function _getWhere(Am_Query $db)
        {
            if (!$this->is_a === null) return;
            $a = $db->getAlias();
            $stm= $this->is_a == 1 ? 'EXISTS' : 'NOT EXISTS';
            return "$stm (SELECT * FROM ?_data
                WHERE id=$a.user_id AND `key` = 'file-upload' AND `table`='user')";
        }

        public function setFromRequest(array $input)
        {
            if (@$input[$this->getId()]['val']!='') {
                $this->is_a = (int)$input[$this->getId()]['val'];
                return true;
            }
        }

        public function getId(){ return '-is-f-uploaded'; }

        public function renderElement(HTML_QuickForm2_Container $form)
        {
           $form->options['Misc'][$this->getId()] = $this->title;
           $group = $form->addGroup($this->getId())
               ->setLabel($this->title)
               ->setAttribute('id', $this->getId())
               ->setAttribute('class', 'searchField empty');
           $sel = $group->addSelect('val');
           $sel->loadOptions($this->options);
        }

        public function isEmpty()
        {
            return $this->is_a === null;
        }

        public function getDescription()
        {
            return $this->options[$this->is_a];
        }
    }

    class Am_Query_User_Condition_IsFileUploadedInCategory extends Am_Query_Condition
        implements Am_Query_Renderable_Condition
    {
        protected $is_a = null;
        protected $options = [];

        function __construct()
        {
            $this->title = ___('Have uploaded at least 1 file to category');
            $this->options = [
                0 => ___('No'),
                1 => ___('Yes'),
            ];
            $this->categories = Am_Di::getInstance()->plugins_misc->loadGet('file-upload')->getCategories();
        }

        function _getWhere(Am_Query $db)
        {
            if (!$this->is_a === null) return;
            $a = $db->getAlias();
            $stm= $this->is_a == 1 ? 'EXISTS' : 'NOT EXISTS';
            $c = "%{$this->c}%";
            return <<<CUT
                $stm (SELECT * FROM ?_data
                    WHERE id=$a.user_id
                        AND `key` = 'file-upload-category'
                        AND `table`='user'
                        AND `blob` LIKE '$c'
                    )

CUT;
        }

        public function setFromRequest(array $input)
        {
            if (@$input[$this->getId()]['val']!='' && !empty($input[$this->getId()]['c'])) {
                $this->is_a = (int)$input[$this->getId()]['val'];
                $this->c = $input[$this->getId()]['c'];
                return true;
            }
        }

        public function getId(){ return '-is-f-uploaded-c'; }

        public function renderElement(HTML_QuickForm2_Container $form)
        {
           $form->options['Misc'][$this->getId()] = $this->title;
           $group = $form->addGroup($this->getId())
               ->setLabel($this->title)
               ->setAttribute('id', $this->getId())
               ->setAttribute('class', 'searchField empty');
           $sel = $group->addSelect('val');
           $sel->loadOptions($this->options);
           $sel = $group->addSelect('c');
           $sel->loadOptions($this->categories);
        }

        public function isEmpty()
        {
            return $this->is_a === null;
        }

        public function getDescription()
        {
            return sprintf("%s in %s", $this->options[$this->is_a],
                $this->categories[$this->c]);
        }
    }
endif;

    $e->addReturn(new Am_Query_User_Condition_IsFileUploaded);
    if ($e->getDi()->plugins_misc->loadGet('file-upload')->getCategories()) {
        $e->addReturn(new Am_Query_User_Condition_IsFileUploadedInCategory);
    }
});

Am_Di::getInstance()->hook->add(Am_Event::LOAD_BRICKS, function (Am_Event $e) {

    class Am_Form_Brick_FileUpload extends Am_Form_Brick
    {
        protected $labels = [
            'Files' => 'Files',
            'you can upload your filese here' => 'you can upload your filese here',
            'This field is required' => 'This field is required'
        ];
        protected $hideIfLoggedInPossible = self::HIDE_DESIRED;

        public function __construct($id = null, $config = null)
        {
            $this->name = ___("File Upload");
            parent::__construct($id, $config);
        }

        public function init()
        {
            Am_Di::getInstance()->hook->add(Am_Event::SIGNUP_USER_ADDED, [$this, 'saveUploads']);
            Am_Di::getInstance()->hook->add(Am_Event::SIGNUP_USER_UPDATED, [$this, 'saveUploads']);
            Am_Di::getInstance()->hook->add(Am_Event::PROFILE_USER_UPDATED, [$this, 'saveUploads']);
        }

        public function saveUploads(Am_Event $e)
        {
            $this->getPlugin()
                ->saveUploads($e, '_file-upload_' . $this->getId(), $this->getConfig('category'));
        }

        public function insertBrick(HTML_QuickForm2_Container $form)
        {
            $prefix = $this->getPlugin()->getUploadPrefix($this->getConfig('category'));

            $maxSize = $this->getConfig('max_size') ? $this->getConfig('max_size') * 1024 * 1024 : 'false';

            $upload = $form->addUpload('_file-upload_' . $this->getId(), ['multiple' => !$this->getConfig('single')], ['prefix' => $prefix, 'secure' => true])
                ->setLabel($this->___('Files') . "\n" . $this->___('you can upload your files here'))
                ->setJsOptions(<<<CUT
{
   fileBrowser: false,
   urlUpload: '/upload/upload',
   urlGet: '/upload/get',
   fileMaxSize: {$maxSize}
}
CUT
                );

            if ($mime_types = $this->getConfig('mime_types')) {
                $upload->setAllowedMimeTypes($mime_types);
            }

            if ($this->getConfig('required')) {
                $upload->addRule('required', $this->___('This field is required'));
            }
        }

        public function initConfigForm(Am_Form $form)
        {
            $form->addAdvCheckbox('required')->setLabel(___('Required'));
            $form->addSelect('category')
                ->loadOptions(['' => ___('No Category')] +
                    Am_Di::getInstance()->plugins_misc->loadGet('file-upload')->getCategories())
                ->setLabel(___('Upload Category'));
            $form->addAdvCheckbox('single')
                ->setLabel(___("Allow to upload one file only\n"
                    . "By default brick allow to upload multiple files at once"));
            $form->addText('max_size')
                ->setLabel("Upload Max Size, Mb\nkeep empty if there is not limit");

            $mime_types = ['image/png', 'image/jpeg', 'image/tiff', 'image/bmp', 'image/gif'];

            $form->addMagicSelect('mime_types')
                ->setLabel("Allowed Mime Types\nkeep empty if there is not limit")
                ->loadOptions(array_combine($mime_types, $mime_types));
        }

        public function isMultiple()
        {
            return true;
        }

        protected function getPlugin()
        {
            return Am_Di::getInstance()->plugins_misc->loadGet('file-upload');
        }
    }

    class Am_Form_Brick_UploadedFiles extends Am_Form_Brick
    {
        static $scriptInit = false;

        protected $labels = [
            'Files' => 'Files'
        ];
        protected $hideIfLoggedInPossible = self::HIDE_DONT;

        public function __construct($id = null, $config = null)
        {
            $this->name = ___("Uploaded Files");
            parent::__construct($id, $config);
        }

        public function insertBrick(HTML_QuickForm2_Container $form)
        {
            $di = Am_Di::getInstance();
            if (($user = $di->auth->getUser()) && ($files = $user->data()->getBlob('file-upload'))) {
                $userFiles = unserialize($files);
                $_ = $user->data()->getBlob('file-upload-category');
                $userFilesCategory = $_ ? unserialize($user->data()->getBlob('file-upload-category')) : [];
                $files = $di->uploadTable->loadIds($userFiles);
                $out = [];
                $category = $this->getConfig('category');
                foreach ($files as $f) {
                    if ($category &&
                        (!isset($userFilesCategory[$f->pk()]) || $userFilesCategory[$f->pk()]!=$category)) {
                        continue;
                    }
                    if ($this->getConfig('can_delete')) {
                        $token = $di->plugins_misc->loadGet('file-upload')->register($f->pk());
                        $delete_url = $di->url("misc/file-upload/delete/$token");
                    }
                    $out[] = sprintf('<li><a href="%s">%s</a>%s</li>',
                        $di->url("misc/file-upload/get/{$f->pk()}", [
                            'uid' => $di->security->obfuscate($user->pk()),
                            'd' => $this->getConfig('disposition', 'attachment')]),
                        Am_Html::escape($f->name),
                            $this->getConfig('can_delete') ? ' <a class="am-file-upload-delete" href="' . $delete_url . '" style="color:#a94442; text-decoration:none">&#10005;</a>' : ''
                        );

                }
                if ($this->getConfig('can_delete') && !self::$scriptInit) {
                    self::$scriptInit = true;
                    $form->addScript()
                        ->setScript(<<<CUT
jQuery(function(){
    jQuery(document).on('click', '.am-file-upload-delete', function(){
        if (confirm('Are you sure?')) {
            jQuery.get(jQuery(this).attr('href'));
            jQuery(this).closest('li').remove();
        }
        return false;
    });
});
CUT
                            );
                }
                if ($out) {
                    $form->addHtml()
                        ->setHtml(sprintf('<ul class="am-widget-list am-file-upload-list">%s</ul>', implode("\n", $out)))
                        ->setLabel($this->___('Files'));
                }
            }
        }

        public function initConfigForm(Am_Form $form)
        {
            $form->addSelect('category')
                ->loadOptions(['' => ___('All Files')] +
                    Am_Di::getInstance()->plugins_misc->loadGet('file-upload')->getCategories())
                ->setLabel(___('Upload Category'));
            $form->addAdvRadio('disposition')
                ->setLabel(___('File Disposition'))
                ->loadOptions([
                    'attachment' => ___('Force Download (attachment)'),
                    'inline' => ___('Display in Browser (inline)')
                ]);
            $form->addAdvCheckbox('can_delete')
                ->setLabel(___('User can remove files'));
        }

        public function isMultiple()
        {
            return true;
        }
    }

});
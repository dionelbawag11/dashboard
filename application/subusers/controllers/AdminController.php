<?php

class Subusers_AdminController extends Am_Mvc_Controller
{
    public function checkAdminPermissions(Admin $admin)
    {
        return $admin->hasPermission(Bootstrap_Subusers::ADMIN_PERM_ID);
    }

    function preDispatch()
    {
        $this->user_id = $this->getInt('user_id');
        $this->view->user_id = $this->user_id;
    }

    function tabAction()
    {
        $this->setActiveMenu('users-browse');

        $user = $this->getDi()->userTable->load($this->user_id);

        $subusers_count = $user->data()->get('subusers_count');
        if (!$user->data()->get('subusers_count') &&
            !$this->getDi()->userTable->countBy(['subusers_parent_id' => $user->pk()])) {
            throw new Am_Exception_InputError(___('This user is not a reseller'));
        }

        $this->view->subusers_count = $subusers_count;

        $grid = Am_Grid_Editable_Subusers::factoryAdmin($user,
            $this->getRequest(), $this->view, $this->getDi());
        $grid->setPermissionId(Bootstrap_Subusers::ADMIN_PERM_ID);
        $this->view->title = ___('Subusers');
        $grid->runWithLayout('admin/subusers.phtml');
    }

    function detachAction()
    {
        $user = $this->getDi()->userTable->load($this->getParam('user_id'));
        $parent = $this->getDi()->userTable->load($user->subusers_parent_id);

        $this->getDi()->subusersSubscriptionTable->deleteByUserId($user->pk());
        $user->subusers_parent_id = 0;

        $user->save();
        $user->checkSubscriptions();
        $this->getModule()->checkAndUpdate($parent);
        Am_Mvc_Response::redirectLocation($this->view->userUrl($user->pk()));
    }
}
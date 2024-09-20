<?php

class Subusers_IndexController extends Am_Mvc_Controller
{
    protected $_session;

    function preDispatch()
    {
        $this->getDi()->auth->requireLogin($this->getRequest()->getRequestUri());
    }

    function indexAction()
    {
        $subusers_count = $this->getDi()->user->data()->get('subusers_count');
        if (empty($subusers_count))
            throw new Am_Exception_Security(___('Resellers-only page'));

        $this->view->subusers_count = $subusers_count;

        $grid = Am_Grid_Editable_Subusers::factory($this->getDi()->user, $this->getRequest(), $this->view, $this->getDi());

        $pending = array_reduce($subusers_count, function ($_, $item) {return $_ + $item['pending_count'];}, 0);

        if ($pending) {
            $this->view->message = ___('You have too many subusers assigned to this account.  You may choose to remove %d users from your account', $pending);
        } else {
            if ($this->getDi()->config->get('subusers_cannot_delete')==2) // no pending accounts, user cannot delete
                $grid->actionDelete('delete');
        }

        $invite = null;

        if ($this->getDi()->subusersInviteTable->countBy([
            'parent_id' => $this->getDi()->user->pk(),
            'dattm_join' => null
        ])) {

            $ds = new Am_Query($this->getDi()->subusersInviteTable);
            $ds->leftJoin('?_user', 'u', 't.user_id=u.user_id');
            $ds->addWhere('dattm_join IS NULL');
            $ds->addWhere('parent_id=?', $this->getDi()->user->pk());
            $ds->addField('u.email');

            $invite = new Am_Grid_ReadOnly('_i', ___('Pending Invites'), $ds, $this->getRequest(), $this->view);
            $invite->addField(new Am_Grid_Field_Date('dattm', ___('Date/Time')));
            $invite->addField('email', ___('E-Mail'));
        }
        $this->view->invite = $invite;

        $grid->runWithLayout('member/subusers.phtml');
    }

    function getSession()
    {
       return  $this->getModule()->getSession();
    }

    function _saveSession()
    {
        $sess = serialize($_SESSION);

        $this->getSession()->parent_user = [
            'reseller_login' => $this->getDi()->auth->getUser()->login,
            'reseller_id' => $this->getDi()->auth->getUserId(),
            'session' => $sess
        ];

        $this->getSession()->lock();
    }

    function _restoreSession()
    {
        if(is_array($this->getSession()->parent_user)){
            $session = unserialize($this->getSession()->parent_user['session']);
            session_unset();
            foreach($session as $k=>$v){
                $_SESSION[$k] = $v;
            }
        }
    }

    function restoreSessionAction()
    {
        if(!is_array($parent_user = $this->getSession()->parent_user))
            throw new Am_Exception_InternalError("Parent session is empty");

        $this->getDi()->auth->logout();
        $this->_restoreSession();
        $adapter = new Am_Auth_Adapter_User($this->getDi()->userTable->load($parent_user['reseller_id']));
        $this->getDi()->auth->login($adapter, $this->getRequest()->getClientIp(), false);
        $this->_response->redirectLocation($this->getDi()->url('member', false));
    }

    function loginAsAction()
    {
        if(!$this->getDi()->config->get('subusers_can_login'))
            throw new Am_Exception_InputError('Ability to login as user is not enabled');

        $user = $this->getDi()->auth->getUser();

        $child_id = $this->getRequest()->getFiltered('id');

        if(!$child_id)
            throw new Am_Exception_InputError("Empty user ID. Can't login");

        $child = $this->getDi()->getInstance()->userTable->load($child_id);

        if($child->subusers_parent_id != $user->pk())
            throw new Am_Exception_InputError("Permission denied. User assigned to another reseller!");

        $this->_saveSession();
        $adapter = new Am_Auth_Adapter_User($child);
        $this->getDi()->auth->login($adapter, $this->getRequest()->getClientIp(), false);
        $this->_response->redirectLocation($this->getDi()->url('member', false));
    }
}
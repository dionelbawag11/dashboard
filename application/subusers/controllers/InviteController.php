<?php

class Subusers_InviteController extends Am_Mvc_Controller
{
    protected $_session;

    function preDispatch()
    {
        $this->getDi()->auth->requireLogin($this->getRequest()->getRequestUri());
    }

    function indexAction()
    {
        $subusers_count = $this->getDi()->user->data()->get('subusers_count');
        if (empty($subusers_count)) {
            throw new Am_Exception_Security(___('Resellers-only page'));
        }
        $form = new Am_Form;
        $form->addCsrf();

        $di = $this->getDi();

        $form->addText('email')
            ->setLabel(___('Email'))
            ->addRule('callback2', null, function($email) use ($di) {
                if (!$user = $di->userTable->findFirstByEmail($email)) {
                    return ___('There is not account with such email address');
                }
                if ($user->subusers_parent_id) {
                    return ___('This user is already member of other group');
                }

                if($user->pk() == $di->user->pk()){
                    return ___("Unable to send invitation to self email address");
                }
            });

        $options = $this->getDi()->subusersSubscriptionTable->getProductOptions($this->getDi()->user, true);
        reset($options);
        if (count($options) == 1) {
            $sel = $form->addSelect('_groups[0]', ['id' => 'subuser-groups'])
                ->setLabel(___('Groups'))
                ->loadOptions($options)
                ->setValue(key($options));

            $sel->persistentFreeze(true);
            $sel->toggleFrozen(true);
        } else {
            $sel = $form->addMagicSelect('_groups', ['id' => 'subuser-groups'])
                ->setLabel(___('Groups'))
                ->loadOptions($options);
        }

        $form->addSaveButton(___('Send Invite'));

        $form->addDataSource($this->getRequest());

        $this->view->title = ___("Invite User");

        if ($form->isSubmitted() && $form->validate()) {
            $v = $form->getValue();
            $user = $this->getDi()->userTable->findFirstByEmail($v['email']);

            $this->getDi()->subusersInviteTable->invite($this->getDi()->user, $user, $v['_groups']);

            if($et = Am_Mail_Template::load('subusers.invite_mail', $user->lang))
            {
                $et->setUser($user);
                $et->setReseller($this->getDi()->user);
                $et->send($user);
            }
            $msg = AM_Html::escape(___('Invite request is sent to user.'));
            $this->view->content = <<<CUT
<div class="am-info">{$msg}</div>
CUT;
        } else {
            $this->view->content = (string) $form;
        }

        $this->view->display('member/layout.phtml');
    }

    function rejectAction()
    {
        if (!$invite = $this->getDi()->subusersInviteTable->load($this->getDi()->security->reveal($this->getParam('id')))) {
           throw new Am_Exception_InputError;
        }

        if ($invite->user_id != $this->getDi()->user->pk()) {
            throw new Am_Exception_InputError;
        }

        $invite->delete();
        Am_Mvc_Response::redirectLocation($this->getDi()->url('member'));
    }

    function acceptAction()
    {
        if (!$invite = $this->getDi()->subusersInviteTable->load($this->getDi()->security->reveal($this->getParam('id')))) {
           throw new Am_Exception_InputError;
        }

        if ($invite->user_id != $this->getDi()->user->pk()) {
            throw new Am_Exception_InputError;
        }

        $product_ids = array_filter(explode(',', $invite->product_ids));

        $this->getDi()->user->subusers_parent_id = $invite->parent_id;
        $this->getDi()->user->save();

        $invite->updateQuick('dattm_join', sqlTime('now'));

        if ($product_ids) {
            $this->getDi()->subusersSubscriptionTable->setForUser($this->getDi()->user->pk(), $product_ids);
            $this->getModule()->checkAndUpdate($this->getDi()->userTable->load($this->getDi()->user->subusers_parent_id));
        }

        Am_Mvc_Response::redirectLocation($this->getDi()->url('member'));
    }
}
<?php

namespace Eva\EvaUser\Controllers;


use Eva\EvaUser\Models;
use Eva\EvaUser\Forms;
use Phalcon\Paginator\Adapter\Model as Paginator;

class SessionController extends ControllerBase
{
    public function verifyAction()
    {
        $code = $this->dispatcher->getParam('code');
        $username = $this->dispatcher->getParam('username');
        $user = new Models\Login();

        try {
            $user->verifyNewUser($username, $code);
        } catch(\Exception $e) {
            $this->errorHandler($e, $user->getMessages());
            return $this->response->redirect($this->getDI()->get('config')->user->activeFailedRedirectUri);
        }
        $this->flashSession->success('SUCCESS_USER_ACTIVED');
        return $this->response->redirect($this->getDI()->get('config')->user->activeSuccessRedirectUri);
    }

    public function forgotAction()
    {
        if ($this->request->isPost()) {
            return;
        }
        $user = new Models\ResetPassword();
        $user->assign(array(
            'email' => $this->request->getPost('email'),
        ));
        if($user->resetPassword()) {
            $this->flashSession->success('Password reset mail sent success');
            return $this->response->redirect('/admin');
        } else {
            $this->flashSession->error($user->getMessages());
            return $this->response->redirect('/admin');
        }
    }

    public function resetAction()
    {
        $code = $this->dispatcher->getParam('code');
        $username = $this->dispatcher->getParam('username');


    }

    public function dashboardAction()
    {

    }

    public function testAction()
    {
        $user = new Models\Login();
        $authIdentity = $user->getAuthIdentity();
        if(!$authIdentity && ($tokenString = $this->cookies->get('realm')->getValue())) {
            if($user->loginWithCookie($tokenString)) {
            } else {
                $this->cookies->delete('realm');
            }
        }
    }

}

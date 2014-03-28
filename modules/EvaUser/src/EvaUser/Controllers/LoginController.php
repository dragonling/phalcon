<?php

namespace Eva\EvaUser\Controllers;


use Eva\EvaUser\Models;
use Eva\EvaUser\Forms;
use Phalcon\Paginator\Adapter\Model as Paginator;

class LoginController extends ControllerBase
{
    public function indexAction()
    {
        if (!$this->request->isPost()) {
            return;
        }

        $form = new Forms\LoginForm();
        if ($form->isValid($this->request->getPost()) === false) {
            $this->validHandler($form);
            return $this->response->redirect($this->getDI()->get('config')->user->loginFailedRedirectUri);
        }

        $user = new Models\Login();
        $identify = $this->request->getPost('identify');
        if(false === strpos($identify, '@')) {
            $user->assign(array(
                'username' => $identify,
                'password' => $this->request->getPost('password'),
            ));
        } else {
            $user->assign(array(
                'email' => $identify,
                'password' => $this->request->getPost('password'),
            ));
        }
        try {
            $user->login();
            if($this->request->getPost('remember')) {
                $token = $user->getRememberMeToken();
                if($token) {
                    $this->cookies->set('realm', $token, time() + $user->getTokenExpired());
                } else {
                    $this->flashSession->error($user->getMessages());
                }
            }
            $this->flashSession->success('SUCCESS_USER_LOGGED_IN');
            return $this->response->redirect($this->getDI()->get('config')->user->loginSuccessRedirectUri);
        } catch(\Exception $e) {
            $this->errorHandler($e, $user->getMessages());
            return $this->response->redirect($this->getDI()->get('config')->user->loginFailedRedirectUri);
        }
    }

    public function reactiveAction()
    {
        $username = $this->request->get('username');
        if(!$username) {
            return $this->response->redirect($this->getDI()->get('config')->user->resetFailedRedirectUri);
        }

        $user = new Models\Login();
        try {
            $user->sendVerificationEmail($username);
            $this->flashSession->success('SUCCESS_USER_ACTIVE_MAIL_SENT');
            return $this->response->redirect($this->getDI()->get('config')->user->resetSuccessRedirectUri);
        } catch(\Exception $e) {
            $this->errorHandler($e, $user->getMessages());
            return $this->response->redirect($this->getDI()->get('config')->user->resetFailedRedirectUri);
        }
    }

}

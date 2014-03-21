<?php

namespace Eva\EvaUser\Controllers;

use Eva\EvaUser\Models;
use Eva\EvaUser\Forms;

class RegisterController extends ControllerBase
{
    public function indexAction()
    {
        if (!$this->request->isPost()) {
            return;
        }

        $form = new Forms\RegisterForm();
        if ($form->isValid($this->request->getPost()) === false) {
            $this->validHandler($form);
            return $this->response->redirect($this->getDI()->get('config')->user->registerFailedRedirectUri);
        }
        $user = new Models\Login();
        $user->assign(array(
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
        ));
        try {
            $user->register();
        } catch(\Exception $e) {
            $this->errorHandler($e, $user->getMessages());
            return $this->response->redirect($this->getDI()->get('config')->user->registerFailedRedirectUri);
        }
        $this->flashSession->success('Register Success');
        return $this->response->redirect($this->getDI()->get('config')->user->registerFailedRedirectUri);
    }

}

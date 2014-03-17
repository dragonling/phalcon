<?php

namespace Eva\EvaUser\Controllers;


use Eva\EvaUser\Models;
use Eva\EvaUser\Forms;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;

class UserController extends ControllerBase
{

    public function registerAction()
    {
        $form = new Forms\RegisterForm();

        if ($this->request->isPost()) {

            if ($form->isValid($this->request->getPost()) != false) {
                $user = new Models\Users();
                $user->assign(array(
                    'username' => $this->request->getPost('username'),
                    'email' => $this->request->getPost('email'),
                    'password' => $this->request->getPost('password'),
                ));
                if ($user->register()) {
                    $this->flash->success('Register Success');
                    return $this->response->redirect('/user/login');
                } else {
                    $this->flash->error($user->getMessages());
                    return $this->response->redirect('/user/login');
                }
            }
        }

        p($form->getMessages());
        $this->view->form = $form;
    }

    public function loginAction()
    {
    
    }

    public function verifyAction()
    {
        $code = $this->dispatcher->getParam('code');
        $userId = $this->dispatcher->getParam('userId');
        $user = new Models\Users();
        if($user->verifyNewUser($userId, $code)) {
            $this->flash->success('Verify Success');
        } else {
            $this->flash->error('Verify Failed');
        }
        return $this->response->redirect('/user/login');
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        p('user');
    }

}

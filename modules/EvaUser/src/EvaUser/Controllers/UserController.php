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
                    return $this->dispatcher->forward(array(
                        'controller' => 'index',
                        'action' => 'index'
                    ));
                } else {
                    p($user->getMessages());
                }

                //$this->flash->error($user->getMessages());

                /*
                if ($user->save()) {
                    return $this->dispatcher->forward(array(
                        'controller' => 'index',
                        'action' => 'index'
                    ));
                }

                $this->flash->error($user->getMessages());
                */
            }
        }

        p($form->getMessages());
        $this->view->form = $form;

    }

    /**
     * Index action
     */
    public function indexAction()
    {
        p('user');
    }

}

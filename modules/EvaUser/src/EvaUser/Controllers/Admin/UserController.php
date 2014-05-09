<?php

namespace Eva\EvaUser\Controllers\Admin;

use Eva\EvaUser\Models;

class UserController extends AdminControllerBase
{

    /**
     * Index action
     */
     public function indexAction()
     {
        $currentPage = $this->request->getQuery('page', 'int'); // GET
        $limit = $this->request->getQuery('limit', 'int');
        $limit = $limit > 50 ? 50 : $limit;
        $limit = $limit < 10 ? 10 : $limit;

        $posts = Models\UserManager::find(array(
            'order' => 'id DESC',
        ));
        $paginator = new \Eva\EvaEngine\Paginator(array(
            "data" => $posts,
            "limit"=> $limit,
            "page" => $currentPage
        ));
        $paginator->setQuery(array(
            'limit' => $limit,
        ));
        $pager = $paginator->getPaginate();
        $this->view->setVar('pager', $pager);
    }

    public function createAction()
    {
        $user = new Models\User();
        $form = new \Eva\EvaUser\Forms\UserForm();
        $form->setModel($user);
        $form->addForm('Profile', 'Eva\EvaUser\Forms\ProfileForm');
        $this->view->setVar('item', $user);
        $this->view->setVar('form', $form);


        if(!$this->request->isPost()){
            return false;
        }

        $data = $this->request->getPost();
        if(!$form->isFullValid($data)) {
            return $this->displayInvalidMessages($form);
        }

        try {
            $form->save();
        } catch(\Exception $e) {
            return $this->displayException($e, $form->getModel()->getMessages());
        }
        $this->flashSession->success('SUCCESS_USER_CREATED');
        return $this->redirectHandler('/admin/user/edit/' . $form->getModel()->id);
    }

    public function editAction()
    {
        $this->view->changeRender('admin/user/create');
        $user = Models\User::findFirst($this->dispatcher->getParam('id'));
        if(!$user) {
            
        }

        $form = new \Eva\EvaUser\Forms\UserForm();
        $form->setModel($user);
        $form->addForm('Profile', 'Eva\EvaUser\Forms\ProfileForm');
        $this->view->setVar('item', $user);
        $this->view->setVar('form', $form);

        if(!$this->request->isPost()){
            return false;
        }

        $data = $this->request->getPost();
        if(!$form->isFullValid($data)) {
            return $this->displayInvalidMessages($form);
        }

        try {
            $form->save();
        } catch(\Exception $e) {
            return $this->displayException($e, $form->getModel()->getMessages());
        }
        $this->flashSession->success('SUCCESS_USER_UPDATED');
        return $this->redirectHandler('/admin/user/edit/' . $user->id);
    
    }

}

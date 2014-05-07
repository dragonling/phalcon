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

    public function suggestionsAction()
    {
        $query = $this->request->get('query');
        if($query) {
            $users = Models\User::find(array(
                "columns" => array('id', 'username', 'status'),
                "conditions" => "username like '%$query%'",
                "limit" => 10,
            ));
            $users = $users ? $users->toArray() : array();
        } else {
            $users = array();
        }

        return $this->response->setJsonContent($users);

    }

    public function createAction()
    {
        $user = new Models\User();
        $userForm = new \Eva\EvaUser\Forms\UserForm();
        $userForm->setModel($user);
        $userForm->addForm('Profile', 'Eva\EvaUser\Forms\ProfileForm');
        $this->view->setVar('item', $user);
        $this->view->setVar('form', $userForm);

        if(!$this->request->isPost()){
            return false;
        }
        $data = $this->request->getPost();
        if($userForm->isFullValid($data)) {
            $model = $userForm->getModel();
            p($model->Profile);
            //p($userForm->getModel('Profile'));
            exit;
        } else {
            p($userForm->getFullMessages());
            exit;
        }
    }

    public function editAction()
    {
        $this->view->changeRender('admin/user/create');
        $user = Models\User::findFirst($this->dispatcher->getParam('id'));
        $userForm = new \Eva\EvaUser\Forms\UserForm();
        $userForm->setModel($user);
        $userForm->addForm('Profile', 'Eva\EvaUser\Forms\ProfileForm');
        $this->view->setVar('item', $user);
        $this->view->setVar('form', $userForm);

        if(!$this->request->isPost()){
            return false;
        }
        $data = $this->request->getPost();
        if($userForm->isFullValid($data)) {
            p($data);
            $model = $userForm->getModel();
            p($model->id);
            p($model->Profile);
            //p($userForm->getModel('Profile'));
            exit;
        } else {
            p($userForm->getFullMessages());
            exit;
        }
    
    }

}

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
        $this->view->setVar('userForm', $userForm);
        $this->view->setVar('item', $user);


        $profileForm = new \Eva\EvaUser\Forms\ProfileForm();
        $profileForm->setModel(new Models\Profile());
        $profileForm->setPrefix('Profile');
        $this->view->setVar('profileForm', $profileForm);

        if(!$this->request->isPost()){
            return false;
        }
        $data = $this->request->getPost();
    }

    public function editAction()
    {
        $this->view->changeRender('admin/user/create');
        $user = Models\User::findFirst($this->dispatcher->getParam('id'));
        $userForm = new \Eva\EvaUser\Forms\UserForm();
        $userForm->setModel($user);
        $this->view->setVar('userForm', $userForm);
        $this->view->setVar('item', $user);


        $profileForm = new \Eva\EvaUser\Forms\ProfileForm();
        $profileForm->setModel(new Models\Profile());
        $profileForm->setPrefix('Profile');
        $this->view->setVar('profileForm', $profileForm);

        if(!$this->request->isPost()){
            return false;
        }
        $data = $this->request->getPost();
    
    }

}

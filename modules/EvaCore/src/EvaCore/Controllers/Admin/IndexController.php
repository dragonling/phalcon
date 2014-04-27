<?php

namespace Eva\EvaCore\Controllers\Admin;

class IndexController extends \Eva\EvaEngine\Mvc\Controller\AdminControllerBase
{

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->view->disable();
        return $this->response->redirect('/admin/login');
    }


    public function dashboardAction()
    {
        $this->view->setModuleLayout('EvaCore', '/views/admin/layouts/layout');
        $this->view->setModuleViewsDir('EvaCore', '/views');
        $this->view->setModulePartialsDir('EvaCore', '/views');

        $user = new \Eva\EvaUser\Models\Login();
        if($user->isUserLoggedIn()) {
            $view->authIdentity = $user->getAuthIdentity();
        }
    }
}

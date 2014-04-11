<?php

namespace Eva\EvaUser\Controllers\Admin;

use Phalcon\Mvc\Controller;

class ControllerBase extends \Eva\EvaEngine\Controller\AdminControllerBase
{
    public function initialize()
    {
        $this->view->setModuleLayout('EvaCore', '/views/admin/layouts/login');
        $this->view->setModuleViewsDir('EvaUser', '/views');
        $this->view->setModulePartialsDir('EvaCore', '/views');
    }

}

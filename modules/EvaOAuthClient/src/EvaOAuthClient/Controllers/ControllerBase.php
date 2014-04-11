<?php

namespace Eva\EvaOAuthClient\Controllers;

class ControllerBase extends \Eva\EvaEngine\Controller\ControllerBase
{
    public function initialize()
    {
        $this->view->setModuleLayout('EvaCore', '/views/admin/layouts/login');
        $this->view->setModuleViewsDir('EvaOAuthClient', '/views');
        $this->view->setModulePartialsDir('EvaCore', '/views');
    }
}

<?php

namespace Eva\EvaFileSystem\Controllers\Admin;

class ControllerBase extends \Eva\EvaEngine\Controller\AdminControllerBase
{
    public function initialize()
    {
        $this->view->setModuleLayout('EvaCore', '/views/admin/layouts/layout');
        $this->view->setModuleViewsDir('EvaFileSystem', '/views');
        $this->view->setModulePartialsDir('EvaCore', '/views');
    }
}

<?php

namespace Eva\EvaUser\Controllers\Admin;

class AdminControllerBase extends ControllerBase
{
    public function initialize()
    {
        $this->view->setModuleLayout('EvaCore', '/views/admin/layouts/layout');
        $this->view->setModuleViewsDir('EvaUser', '/views');
        $this->view->setModulePartialsDir('EvaCore', '/views');
    }

}

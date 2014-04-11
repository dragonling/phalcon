<?php

namespace Eva\EvaBlog\Controllers\Admin;

class ControllerBase extends \Eva\EvaEngine\Controller\ControllerBase
{
    public function initialize()
    {
        $this->view->setModuleLayout('EvaCore', '/views/backend/layouts/layout');
        $this->view->setModuleViewsDir('EvaBlog', '/views');
    }
}

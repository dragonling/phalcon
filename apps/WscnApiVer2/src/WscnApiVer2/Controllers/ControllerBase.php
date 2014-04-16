<?php

namespace WscnApiVer2\Controllers;

class ControllerBase extends \Eva\EvaEngine\Controller\ControllerBase
{
    public function initialize()
    {
        $this->view->setModuleLayout('WscnApiVer2', '/views/layouts/default');
        $this->view->setModuleViewsDir('WscnApiVer2', '/views');
        $this->view->setModulePartialsDir('WscnApiVer2', '/views');
    }

}

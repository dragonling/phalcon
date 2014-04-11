<?php

namespace WscnGold\Controllers;

class ControllerBase extends \Eva\EvaEngine\Controller\ControllerBase
{
    public function initialize()
    {
        $this->view->setModuleLayout('WscnGold', '/views/layouts/default');
        $this->view->setModuleViewsDir('WscnGold', '/views');
        $this->view->setModulePartialsDir('WscnGold', '/views');
    }

}

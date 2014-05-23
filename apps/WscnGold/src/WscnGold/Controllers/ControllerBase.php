<?php

namespace WscnGold\Controllers;

class ControllerBase extends \Eva\EvaEngine\Mvc\Controller\ControllerBase
{
    public function initialize()
    {
        $this->view->cache(array(
            'lifetime' => 60
        ));
        $this->view->setModuleLayout('WscnGold', '/views/layouts/default');
        $this->view->setModuleViewsDir('WscnGold', '/views');
        $this->view->setModulePartialsDir('WscnGold', '/views');
    }

}

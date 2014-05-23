<?php

namespace WscnGold\Controllers;

class ControllerBase extends \Eva\EvaEngine\Mvc\Controller\ControllerBase
{
    public function initialize()
    {
        $cacheKey = md5($this->request->getURI());
        $this->view->cache(array(
            'lifetime' => 60,
            'key' => $cacheKey,
        ));
        $this->view->setModuleLayout('WscnGold', '/views/layouts/default');
        $this->view->setModuleViewsDir('WscnGold', '/views');
        $this->view->setModulePartialsDir('WscnGold', '/views');
    }

}

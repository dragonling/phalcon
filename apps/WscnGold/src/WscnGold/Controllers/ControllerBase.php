<?php

namespace WscnGold\Controllers;

class ControllerBase extends \Eva\EvaEngine\Controller\ControllerBase
{
    public function initialize()
    {
        $view = $this->view;
        $view->setViewsDir($this->getDI()->get('modules')->getModulePath('WscnGold') . '/views/');
        $view->setLayoutsDir('layouts/');
        $view->setLayout('default');
    }

}

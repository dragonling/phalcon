<?php

namespace WscnApiVer2\Controllers;

use Swagger\Annotations as SWG;

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        echo 1;
    }

    public function resourcesAction()
    {
        $swagger = new \Swagger\Swagger(__DIR__);
        $content = json_encode($swagger->getResourceList());
        $this->response->setContentType('application/json', 'utf-8');
        return $this->response->setContent($content);
    }

    public function resourceAction()
    {
        $swagger = new \Swagger\Swagger(__DIR__);
        $resource = $swagger->getResource('/' . $this->dispatcher->getParam('id'));
        p($resource);
        exit;
        //return $this->response->setContent($content);
    }
}

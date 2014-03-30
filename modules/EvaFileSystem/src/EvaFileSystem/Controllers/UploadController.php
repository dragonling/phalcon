<?php

namespace Eva\EvaFileSystem\Controllers;


use Eva\EvaFileSystem\Models;
use Eva\EvaUser\Models as UserModels;
use EvaOAuth\Service as OAuthService;

class UploadController extends ControllerBase
{

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->view->disable();
        if(!$this->request->isPost() || !$this->request->hasFiles()){
            return;
        }


        foreach ($this->request->getUploadedFiles() as $file) {
            $file->moveTo($this->getDI()->get('config')->upload->path . $file->getName());
        }
    }

    public function testAction()
    {
    }
}

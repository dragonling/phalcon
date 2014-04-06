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
            $this->response->setStatusCode('400', 'No Upload Files');
            return;
        }

        foreach ($this->request->getUploadedFiles() as $file) {
            $file->moveTo($this->getDI()->get('config')->upload->path . $file->getName());
        }
        $this->view->disable();
        echo json_encode(array(
            'url' => $this->getDI()->get('config')->upload->url . '/' . $file->getName(),
        ));
    }

    public function testAction()
    {
    }
}

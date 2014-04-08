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

        $upload = new Models\Upload();
        $files = array();
        foreach ($this->request->getUploadedFiles() as $file) {
            $file = $upload->upload($file);
            if($file) {
                $fileinfo = $file->toArray();
                $fileinfo['fullUrl'] = $file->getFullUrl();
                $files[] = $fileinfo;
            }

        }
        $this->response->setContentType('application/json', 'utf-8');
        return $this->response->setJsonContent(array(
            'count' => count($files),
            'results' => $files,
        ));
    }

    public function testAction()
    {
    }
}

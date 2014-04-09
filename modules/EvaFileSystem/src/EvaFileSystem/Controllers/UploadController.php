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
         if(!$this->request->isPost() || !$this->request->hasFiles()){
             $this->response->setStatusCode('400', 'No Upload Files');
             $this->response->setContentType('application/json', 'utf-8');
             return $this->response->setJsonContent(array(
                 'errors' => array(
                     array(
                         'code' => 400,
                         'message' => 'ERR_FILE_NO_UPLOAD'
                     )
                 ),
             ));
         }

         $upload = new Models\Upload();
         try {
             $files = $this->request->getUploadedFiles();
             //Only allow upload the first file by force
             $file = $files[0];
             $file = $upload->upload($file);
             if($file) {
                 $fileinfo = $file->toArray();
                 $fileinfo['fullUrl'] = $file->getFullUrl();
             }
         } catch(\Exception $e) {
             return $this->jsonErrorHandler($e, $upload->getMessage());
         }

         $this->response->setContentType('application/json', 'utf-8');
         return $this->response->setJsonContent($fileinfo);
    }

    public function testAction()
    {
    }
}

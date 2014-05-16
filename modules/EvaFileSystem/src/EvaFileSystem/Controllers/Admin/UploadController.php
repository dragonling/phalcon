<?php

namespace Eva\EvaFileSystem\Controllers\Admin;

use Eva\EvaFileSystem\Models;

class UploadController extends ControllerBase
{

    /**
     * Index action
     */
     public function indexAction()
     {
         if (!$this->request->isPost() || !$this->request->hasFiles()) {
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
             if ($file) {
                 $fileinfo = $file->toArray();
                 $fileinfo['localUrl'] = $file->getLocalUrl();
             }
         } catch (\Exception $e) {
             return $this->displayExceptionForJson($e, $upload->getMessages());
         }

         $this->response->setContentType('application/json', 'utf-8');

         return $this->response->setJsonContent($fileinfo);
    }

    public function testAction()
    {
    }

    public function encodeAction()
    {
         if (!$this->request->isPost()) {
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
             $file = $upload->uploadByEncodedData(
                $this->request->getPost('file'),
                $this->request->getPost('name'),
                $this->request->getPost('type')
             );
             if ($file) {
                 $fileinfo = $file->toArray();
                 $fileinfo['localUrl'] = $file->getLocalUrl();
             }
         } catch (\Exception $e) {
             return $this->displayExceptionForJson($e, $upload->getMessages());
         }

         $this->response->setContentType('application/json', 'utf-8');

         return $this->response->setJsonContent($fileinfo);
    }
}

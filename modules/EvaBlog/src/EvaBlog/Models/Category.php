<?php

namespace Eva\EvaBlog\Models;


use Eva\EvaBlog\Entities;
use Eva\EvaFileSystem\Models\Upload as UploadModel;
use \Phalcon\Mvc\Model\Message as Message;
use Eva\EvaEngine\Exception;

class Category extends Entities\Categories
{
    public function beforeValidationOnCreate()
    {
        $this->createdAt = time();
        if(!$this->slug) {
            $factory = new \RandomLib\Factory();
            $this->slug = $factory->getMediumStrengthGenerator()->generateString(8, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
        }
    }

    public function createCategory()
    {
        if($this->getDI()->getRequest()->hasFiles()) {
            $upload = new UploadModel();
            $files = $this->getDI()->getRequest()->getUploadedFiles();
            if($files)
            $file = $files[0];
            $file = $upload->upload($file);
            if($file) {
                $this->image_id = $file->id;
                $this->image = $file->getFullUrl();
            }
        }
        $this->save();
    }


    public function updateCategory()
    {
        if($this->getDI()->getRequest()->hasFiles()) {
            $upload = new UploadModel();
            $files = $this->getDI()->getRequest()->getUploadedFiles();
            if($files)
            $file = $files[0];
            $file = $upload->upload($file);
            if($file) {
                $this->image_id = $file->id;
                $this->image = $file->getFullUrl();
            }
        }
        $this->save();
    }
}

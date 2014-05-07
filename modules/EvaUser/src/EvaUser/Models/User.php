<?php

namespace Eva\EvaUser\Models;


use Eva\EvaUser\Entities;
use \Phalcon\Mvc\Model\Message as Message;
use Eva\EvaFileSystem\Models\Upload as UploadModel;
use Eva\EvaEngine\Exception;

class User extends Entities\Users
{
    public function beforeCreate()
    {
        $this->createdAt = $this->createdAt ? $this->createdAt : time();
        $this->providerType = $this->providerType != 'DEFAULT' ? $this->providerType : 'ADMIN';
    }

    public function beforeUpdate()
    {
        if(!$this->password) {
            $this->skipAttributesOnUpdate(array('password'));
        }
    }

    public function beforeSave()
    {
        if($this->password) {
            $this->password = password_hash($this->password, PASSWORD_DEFAULT, array('cost' => 10));
        }

        if($this->getDI()->getRequest()->hasFiles()) {
            $upload = new UploadModel();
            $files = $this->getDI()->getRequest()->getUploadedFiles();
            if(!$files) {
                return;
            }
            $file = $files[0];
            $file = $upload->upload($file);
            if($file) {
                $this->avatar_id = $file->id;
                $this->avatar = $file->getFullUrl();
            }
        }
    }

    public function isExist()
    {
        $userinfo = array();
        if($this->id) {
            $userinfo = self::findFirst("id = '$this->id'");
        } elseif($this->username) {
            $userinfo = self::findFirst("username = '$this->username'");
        } elseif($this->email) {
            $userinfo = self::findFirst("email = '$this->email'");
        }
        return $userinfo ? $userinfo->id : false;
    }

    public function createUser($data)
    {
    
    }

}

<?php

namespace Eva\EvaUser\Models;


use Eva\EvaUser\Entities;
use \Phalcon\Mvc\Model\Message as Message;
use Eva\EvaEngine\Exception;

class User extends Entities\Users
{
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

    public function createUser()
    {
    
    }

}

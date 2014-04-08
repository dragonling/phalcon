<?php

namespace Eva\EvaBlog\Models;


use Eva\EvaBlog\Entities;
use Eva\EvaUser\Models\Login as LoginModel;
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

    public function beforeCreate()
    {
    }
}

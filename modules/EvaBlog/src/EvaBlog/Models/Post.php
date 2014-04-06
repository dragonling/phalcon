<?php

namespace Eva\EvaBlog\Models;


use Eva\EvaBlog\Entities;
use \Phalcon\Mvc\Model\Message as Message;
use Eva\EvaEngine\Exception;

class Post extends Entities\Posts
{
    protected $useMasterSlave = false;
    public function beforeValidationOnCreate()
    {
        $this->createdAt = time();
        $factory = new \RandomLib\Factory();
        $this->slug = $factory->getMediumStrengthGenerator()->generateString(8, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
    }
}

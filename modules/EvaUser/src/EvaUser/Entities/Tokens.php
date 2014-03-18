<?php

namespace Eva\EvaUser\Entities;

class Tokens extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var string
     */
    public $sessionId;
     
    /**
     *
     * @var string
     */
    public $token;
     
    /**
     *
     * @var string
     */
    public $userHash;
     
    /**
     *
     * @var integer
     */
    public $user_id;
     
    /**
     *
     * @var string
     */
    public $refreshTime;
     
    /**
     *
     * @var string
     */
    public $expiredTime;
     
    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'sessionId' => 'sessionId', 
            'token' => 'token', 
            'userHash' => 'userHash', 
            'user_id' => 'user_id', 
            'refreshTime' => 'refreshTime', 
            'expiredTime' => 'expiredTime'
        );
    }

    public function getSource() {
        return 'eva_user_tokens';
    }

}

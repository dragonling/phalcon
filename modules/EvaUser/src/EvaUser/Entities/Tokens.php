<?php

namespace Eva\EvaUser\Entities;

class Tokens extends \Eva\EvaEngine\Model
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
     * @var integer
     */
    public $refreshAt;
     
    /**
     *
     * @var integer
     */
    public $expiredAt;
     
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
            'refreshAt' => 'refreshAt', 
            'expiredAt' => 'expiredAt'
        );
    }



    protected $tableName = 'user_tokens';
}

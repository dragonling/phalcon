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
    public $refreshTimestamp;
     
    /**
     *
     * @var integer
     */
    public $expiredTimestamp;
     
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
            'refreshTimestamp' => 'refreshTimestamp', 
            'expiredTimestamp' => 'expiredTimestamp'
        );
    }

    protected $tableName = 'user_tokens';
}

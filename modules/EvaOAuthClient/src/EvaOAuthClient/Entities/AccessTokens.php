<?php

namespace Eva\EvaOAuthClient\Entities;

class AccessTokens extends \Eva\EvaEngine\Model
{
    /**
     *
     * @var string
     */
    public $adapterKey;
     
    /**
     *
     * @var string
     */
    public $token;
     
    /**
     *
     * @var string
     */
    public $version;
     
    /**
     *
     * @var string
     */
    public $tokenStatus;
     
    /**
     *
     * @var string
     */
    public $scope;
     
    /**
     *
     * @var string
     */
    public $refreshToken;
     
    /**
     *
     * @var string
     */
    public $refreshTime;
     
    /**
     *
     * @var string
     */
    public $expireTime;
     
    /**
     *
     * @var string
     */
    public $remoteUserId;
     
    /**
     *
     * @var string
     */
    public $remoteUserName;
     
    /**
     *
     * @var string
     */
    public $remoteNickName;
     
    /**
     *
     * @var string
     */
    public $remoteEmail;
     
    /**
     *
     * @var string
     */
    public $remoteImageUrl;
     
    /**
     *
     * @var string
     */
    public $remoteExtra;
     
    /**
     *
     * @var integer
     */
    public $user_id;
     
    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'adapterKey' => 'adapterKey', 
            'token' => 'token', 
            'version' => 'version', 
            'tokenStatus' => 'tokenStatus', 
            'scope' => 'scope', 
            'refreshToken' => 'refreshToken', 
            'refreshTime' => 'refreshTime', 
            'expireTime' => 'expireTime', 
            'remoteUserId' => 'remoteUserId', 
            'remoteUserName' => 'remoteUserName', 
            'remoteNickName' => 'remoteNickName', 
            'remoteEmail' => 'remoteEmail', 
            'remoteImageUrl' => 'remoteImageUrl', 
            'remoteExtra' => 'remoteExtra', 
            'user_id' => 'user_id'
        );
    }

    protected $tableName = 'oauth_accesstokens';
}

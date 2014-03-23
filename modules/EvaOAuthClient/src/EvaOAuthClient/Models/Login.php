<?php

namespace Eva\EvaOAuthClient\Models;


use Eva\EvaUser\Entities\Users as UserEntity;
use Eva\EvaOAuthClient\Entities\AccessTokens;
use \Phalcon\Mvc\Model\Message as Message;
use Eva\EvaEngine\Exception;

class Login extends UserEntity
{
    protected $useMasterSlave;

    public function register()
    {
        $userinfo = self::findFirst("username = '$this->username'");
        if($userinfo) {
            throw new Exception\ResourceConflictException('ERR_USER_USERNAME_ALREADY_TAKEN');
        }

        $userinfo = self::findFirst("email = '$this->email'");
        if($userinfo) {
            throw new Exception\ResourceConflictException('ERR_USER_EMAIL_ALREADY_TAKEN');
        }

        $session = $this->getDI()->getSession('session');
        $accessToken = $session->get('access-token');

        if(!$accessToken) {
            throw new Exception\ResourceConflictException('ERR_OAUTH_NO_ACCESS_TOKEN');
        }

        //OAuth register user already active
        $this->status = 'active';
        $this->accountType = 'basic';

        //No password
        $this->password = null;

        // generate random hash for email verification (40 char string)
        $this->activationHash = sha1(uniqid(mt_rand(), true));
        // generate integer-timestamp for saving of account-creating date
        $this->creationTimestamp = time();
        $this->providerType = $accessToken['adapterKey'] . '_' . $accessToken['version'];

        if (!$this->save()) {
            throw new Exception\RuntimeException('ERR_USER_CREATE_FAILED');
        }

        $userinfo = self::findFirst("username = '$this->username'");
        if(!$userinfo) {
            throw new Exception\RuntimeException('ERR_USER_CREATE_FAILED');
        }

        $accessTokenEntity = new AccessTokens();
        $accessTokenEntity->assign($accessToken);
        $accessTokenEntity->tokenStatus = 'active';
        $accessTokenEntity->user_id = $userinfo->id;
        //$this->sendVerificationEmail($userinfo->username);
        if(!$accessTokenEntity->save()) {
            throw new Exception\RuntimeException('ERR_OAUTH_TOKEN_CREATE_FAILED');
        }
        return $userinfo;
    }


    public function sendVerificationEmail($username)
    {
        $userinfo = self::findFirst("username = '$username'");
        if(!$userinfo) {
            throw new Exception\ResourceNotFoundException('ERR_USER_NOT_EXIST');
        }

        if($userinfo->status == 'active') {
            throw new Exception\OperationNotPermitedException('ERR_USER_ALREADY_ACTIVED');
        }

        $mailer = $this->getDI()->get('mailer');
        $message = $this->getDI()->get('mailMessage');
        $message->setTo(array(
            $userinfo->email => $userinfo->username
        ));
        $message->setTemplate($this->getDI()->get('config')->user->activeMailTemplate);
        $message->assign(array(
            'user' => $userinfo->toArray(),
            'url' => $message->toSystemUrl('/session/verify/' . urlencode($userinfo->username) . '/' . $userinfo->activationHash)
        ));

        return $mailer->send($message->getMessage());
    }

}

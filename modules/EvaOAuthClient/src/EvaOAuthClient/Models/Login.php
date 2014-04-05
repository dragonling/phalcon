<?php

namespace Eva\EvaOAuthClient\Models;

use Eva\EvaUser\Models\Login as UserLogin;
use Eva\EvaUser\Entities\Users as UserEntity;
use Eva\EvaOAuthClient\Entities\AccessTokens;
use \Phalcon\Mvc\Model\Message as Message;
use Eva\EvaEngine\Exception;

class Login extends UserEntity
{
    public function loginWithAccessToken(array $accessToken)
    {
        $accessTokenEntity = new AccessTokens();
        $accessTokenEntity->assign($accessToken);
        $token = $accessTokenEntity->findFirst(array(
            "adapterKey = :adapterKey: AND remoteUserId = :remoteUserId: AND version = :version:",
            'bind' => array(
                'adapterKey' => $accessToken['adapterKey'],
                'version' => $accessToken['version'],
                'remoteUserId' => $accessToken['remoteUserId'],
            )
        ));
        if(!$token || !$token->user_id) {
            return false;
        }
        
        $userModel = new UserLogin();
        $userModel->assign(array(
            'id' => $token->user_id
        ));
        return $userModel->loginWithId();
    }

    public function connectWithExistEmail(array $accessToken)
    {
        if(!$accessToken) {
            throw new Exception\ResourceConflictException('ERR_OAUTH_NO_ACCESS_TOKEN');
        }

        $userinfo = self::findFirst("email = '$this->email'");
        if(!$userinfo) {
            throw new Exception\ResourceNotFoundException('ERR_USER_NOT_EXIST');
        }

        if($userinfo->status == 'deleted') {
            throw new Exception\OperationNotPermitedException('ERR_USER_BE_BANNED');
        }

        if($userinfo->status == 'inactive') {
            $userinfo->status = 'active';
            if (!$userinfo->save()) {
                throw new Exception\RuntimeException('ERR_USER_SAVE_FAILED');
            }
        }

        $accessTokenEntity = new AccessTokens();
        $accessTokenEntity->assign($accessToken);
        $accessTokenEntity->tokenStatus = 'active';
        $accessTokenEntity->user_id = $userinfo->id;
        //$this->sendVerificationEmail($userinfo->username);
        if(!$accessTokenEntity->save()) {
            throw new Exception\RuntimeException('ERR_OAUTH_TOKEN_CREATE_FAILED');
        }

        $userModel = new UserLogin(); 
        $authIdentity = $userModel->saveUserToSession($userinfo);
        return $authIdentity;
    }

    public function connectWithPassword(array $accessToken)
    {
        $userModel = new UserLogin();
        $userModel->assign(array(
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
        ));
        $authIdentity = $userModel->login();

        $accessTokenEntity = new AccessTokens();
        $accessTokenEntity->assign($accessToken);
        $accessTokenEntity->tokenStatus = 'active';
        $accessTokenEntity->user_id = $authIdentity['id'];
        if(!$accessTokenEntity->save()) {
            throw new Exception\RuntimeException('ERR_OAUTH_TOKEN_CREATE_FAILED');
        }
        return $authIdentity;
    }

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
        $this->emailStatus = 'inactive';

        //No password
        $this->password = null;

        // generate random hash for email verification (40 char string)
        $this->activationHash = sha1(uniqid(mt_rand(), true));
        // generate integer-timestamp for saving of account-creating date
        $this->createdAt = time();
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

        $userModel = new UserLogin(); 
        $authIdentity = $userModel->saveUserToSession($userinfo);
        return $authIdentity;
    }


    public function sendConfirmEmail($username)
    {
        $userinfo = self::findFirst("username = '$username'");
        if(!$userinfo) {
            throw new Exception\ResourceNotFoundException('ERR_USER_NOT_EXIST');
        }

        if($userinfo->status == 'deleted') {
            throw new Exception\OperationNotPermitedException('ERR_USER_BE_BANNED');
        }

        $mailer = $this->getDI()->get('mailer');
        $message = $this->getDI()->get('mailMessage');
        $message->setTo(array(
            $userinfo->email => $userinfo->username
        ));
        $message->setTemplate($this->getDI()->get('config')->user->confirmMailTemplate);
        $message->assign(array(
            'user' => $userinfo->toArray(),
            'url' => $message->toSystemUrl('/auth/verify/' . urlencode($userinfo->username) . '/' . $userinfo->activationHash)
        ));

        return $mailer->send($message->getMessage());
    }

}

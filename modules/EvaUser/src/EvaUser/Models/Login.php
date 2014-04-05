<?php

namespace Eva\EvaUser\Models;


use Eva\EvaUser\Entities;
use \Phalcon\Mvc\Model\Message as Message;
use Eva\EvaEngine\Exception;

class Login extends Entities\Users
{
    protected $maxLoginRetry = 3;

    private $tokenSalt = 'EvaUser_Login_TokenSalt';

    protected $tokenExpired = 5184000; //60 days

    public function getTokenExpired()
    {
        return $this->tokenExpired;
    }

    public function getRememberMeToken()
    {
        if(!$this->username) {
            $this->appendMessage(new Message('ERR_USER_REMEMBER_TOKEN_NO_USER_INPUT'));
            return false;
        }
        $sessionId = $this->getDI()->get('session')->getId();
        if(!$sessionId) {
            $this->appendMessage(new Message('ERR_USER_REMEMBER_TOKEN_NO_SESSION'));
            return false;
        }
        $userinfo = self::findFirst("username = '$this->username'");
        if(!$userinfo) {
            $this->appendMessage(new Message('ERR_USER_REMEMBER_TOKEN_USER_NOT_FOUND'));
            return false;
        }
        $token = new Entities\Tokens();
        $token->sessionId = $sessionId;
        $token->token = md5(uniqid(rand(), true));
        $token->userHash = $this->getUserHash($userinfo);
        $token->user_id = $userinfo->id;
        $token->refreshAt = time();
        $token->expiredAt = time() + $this->tokenExpired;
        $token->save();
        $tokenString = $sessionId . '|' . $token->token . '|' . $token->userHash;
        return $tokenString;
    }

    public function getUserHash(Entities\Users $userinfo)
    {
        //If user password or status changed, all user token will be unavailable 
        return md5($this->tokenSalt . $userinfo->status .  $userinfo->password); 
    }

    public function saveUserToSession(Entities\Users $userinfo)
    {
        $authIdentity = $this->userToAuthIdentity($userinfo);
        $this->getDI()->get('session')->set('auth-identity', $authIdentity);
        return $authIdentity;
    }

    public function userToAuthIdentity(Entities\Users $userinfo)
    {
        return array(
            'id' => $userinfo->id,
            'username' => $userinfo->username,
            'email' => $userinfo->email,
        );
    }


    public function login()
    {
        $userinfo = array();
        if($this->username) {
            $userinfo = self::findFirst("username = '$this->username'");
        } elseif($this->email) {
            $userinfo = self::findFirst("email = '$this->email'");
        } else {
            throw new Exception\InvalidArgumentException('ERR_USER_NO_USERNAME_OR_EMAIL_INPUT');
        }

        if(!$userinfo) {
            throw new Exception\ResourceNotFoundException('ERR_USER_NOT_EXIST');
        }

        if($userinfo->failedLogins >= $this->maxLoginRetry && $userinfo->loginFailedAt > (time() - 30)) {
            throw new Exception\RuntimeException('ERR_USER_PASSWORD_WRONG_MAX_TIMES');
        }

        // check if hash of provided password matches the hash in the database
        if(!password_verify($this->password, $userinfo->password)) {
            //MUST be string type here
            $userinfo->failedLogins = (string) ($userinfo->failedLogins + 1);
            $userinfo->loginFailedAt = time();
            $userinfo->save();
            throw new Exception\VerifyFailedException('ERR_USER_PASSWORD_WRONG');
        }

        if($userinfo->status != 'active') {
            throw new Exception\UnauthorizedException('ERR_USER_NOT_ACTIVATED');
        }

        $userinfo->failedLogins = 0;
        $userinfo->loginFailedAt = time();
        $userinfo->save();

        $authIdentity = $this->saveUserToSession($userinfo);
        return $authIdentity;
    }

    public function loginWithId()
    {
        $userinfo = array();
        if($this->id) {
            $userinfo = self::findFirst("id = '$this->id'");
        }
        if(!$userinfo) {
            throw new Exception\ResourceNotFoundException('ERR_USER_NOT_EXIST');
        }

        if($userinfo->status != 'active') {
            throw new Exception\UnauthorizedException('ERR_USER_NOT_ACTIVATED');
        }

        $userinfo->failedLogins = 0;
        $userinfo->loginFailedAt = time();
        $userinfo->save();
        $authIdentity = $this->saveUserToSession($userinfo);
        return $authIdentity;
    }


    public function loginWithCookie($tokenString)
    {
        $tokenArray = explode('|', $tokenString);
        if(!$tokenArray || count($tokenArray) < 3) {
            $this->appendMessage(new Message('ERR_USER_REMEMBER_TOKEN_FORMAT_INCORRECT'));
            return false;
        }
        $token = new Entities\Tokens();
        $token->assign(array(
            'sessionId' => $tokenArray[0],
            'token' => $tokenArray[1],
            'userHash' => $tokenArray[2],
        ));
        $tokenInfo = $token::findFirst();
        if(!$tokenInfo) {
            $this->appendMessage(new Message('ERR_USER_REMEMBER_TOKEN_NOT_FOUND'));
            return false;
        }

        if($tokenInfo->expiredAt < time()) {
            $this->appendMessage(new Message('ERR_USER_REMEMBER_TOKEN_EXPIRED'));
            return false;
        }

        $this->id = $tokenInfo->user_id;
        $userinfo = self::findFirst("id = '$this->id'");

        if(!$userinfo) {
            $this->appendMessage(new Message('ERR_USER_REMEMBER_TOKEN_USER_NOT_FOUND'));
            return false;
        }

        if($userinfo->status != 'active') {
            $this->appendMessage(new Message('ERR_USER_REMEMBER_TOKEN_USER_NOT_ACTIVATED'));
            return false;
        }

        $userinfo->failedLogins = 0;
        $userinfo->loginAt = time();
        $userinfo->save();

        $this->saveUserToSession($userinfo);
        return true;
    }

    public function getAuthIdentity()
    {
        $authIdentity = $this->getDI()->get('session')->get('auth-identity');
        if($authIdentity) {
            return $authIdentity;
        }
        return false;
    }

    /**
     * Returns the current state of the user's login
     * @return bool user's login status
     */
    public function isUserLoggedIn()
    {
        return $this->getAuthIdentity();
    }
}

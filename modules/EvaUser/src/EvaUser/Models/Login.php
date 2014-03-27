<?php

namespace Eva\EvaUser\Models;


use Eva\EvaUser\Entities;
use \Phalcon\Mvc\Model\Message as Message;
use Eva\EvaEngine\Exception;

class Login extends Entities\Users
{
    /**
    *  Token Create Feedbacks
    */
    const FEEDBACK_TOKEN_NO_USER_INPUT = 'FEEDBACK_TOKEN_NO_USER_INPUT';
    const FEEDBACK_TOKEN_NO_USER_FOUND = 'FEEDBACK_TOKEN_NO_USER_FOUND';
    const FEEDBACK_TOKEN_NO_SESSION = 'FEEDBACK_TOKEN_NO_SESSION';
    const FEEDBACK_TOKEN_SAVE_FAILED = 'FEEDBACK_TOKEN_SAVE_FAILED';

    /**
    *  Token Login FeedBacks
    */
    const FEEDBACK_TOKEN_FORMAT_INCORRECT = 'FEEDBACK_TOKEN_FORMAT_INCORRECT';
    const FEEDBACK_TOKEN_NOT_FOUND = 'FEEDBACK_TOKEN_NOT_FOUND';
    const FEEDBACK_TOKEN_EXPIRED = 'FEEDBACK_TOKEN_EXPIRED';

    protected $maxLoginRetry = 3;

    private $tokenSalt = 'EvaUser_Login_TokenSalt';

    protected $tokenExpired = 5184000; //60 days

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

        $this->status = 'inactive';
        $this->accountType = 'basic';
        // crypt the user's password with the PHP 5.5's password_hash() function, results in a 60 character
        // how those PHP 5.5 functions want the parameter: as an array with, currently only used with 'cost' => XX
        $this->password = password_hash($this->password, PASSWORD_DEFAULT, array('cost' => 10));

        // generate random hash for email verification (40 char string)
        $this->activationHash = sha1(uniqid(mt_rand(), true));
        // generate integer-timestamp for saving of account-creating date
        $this->creationTimestamp = time();
        $this->providerType = 'DEFAULT';
        if ($this->save() == false) {
            throw new Exception\RuntimeException('ERR_USER_CREATE_FAILED');
        }

        $userinfo = self::findFirst("username = '$this->username'");
        if(!$userinfo) {
            throw new Exception\RuntimeException('ERR_USER_CREATE_FAILED');
        }
        $this->sendVerificationEmail($userinfo->username);
        return $userinfo;
    }


    public function sendVerificationEmail($username, $forceSend = false)
    {
        if(false === $forceSend && $this->getDI()->get('config')->mailer->async) {
            $queue = $this->getDI()->get('queue');
            $result = $queue->doBackground('sendmailAsync', json_encode(array(
                'class' => __CLASS__,
                'method' => __FUNCTION__,
                'parameters' => array($username, true)
            )));
            return true;
        }

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

        $mailer->send($message->getMessage());
        return true;
    }


    /**
    * checks the email/verification code combination and set the user's activation status to active in the database
    * @param int $user_id user id
    * @param string $user_activation_verification_code verification token
    * @return bool success status
    */
    public function verifyNewUser($username, $activationCode)
    {
        $userinfo = self::findFirst("username = '$username'");
        if(!$userinfo) {
            throw new Exception\ResourceNotFoundException('ERR_USER_NOT_EXIST');
        }

        if($userinfo->status == 'active') {
            throw new Exception\OperationNotPermitedException('ERR_USER_ALREADY_ACTIVED');
        }

        //status tranfer only allow inactive => active
        if($userinfo->status != 'inactive') {
            throw new Exception\OperationNotPermitedException('ERR_USER_BE_BANNED');
        }

        if($userinfo->activationHash != $activationCode) {
            throw new Exception\VerifyFailedException('ERR_USER_ACTIVATE_CODE_NOT_MATCH');
        }

        $userinfo->status = 'active';
        if (!$userinfo->save()) {
            throw new Exception\RuntimeException('ERR_USER_ACTIVE_FAILED');
        }
        return true;
    }

    public function getTokenExpired()
    {
        return $this->tokenExpired;
    }

    public function getRememberMeToken()
    {
        if(!$this->username) {
            $this->appendMessage(new Message(self::FEEDBACK_TOKEN_NO_USER_INPUT));
            return false;
        }
        $sessionId = $this->getDI()->get('session')->getId();
        if(!$sessionId) {
            $this->appendMessage(new Message(self::FEEDBACK_TOKEN_NO_SESSION));
            return false;
        }
        $userinfo = self::findFirst("username = '$this->username'");
        if(!$userinfo) {
            $this->appendMessage(new Message(self::FEEDBACK_TOKEN_NO_USER_FOUND));
            return false;
        }
        $token = new Entities\Tokens();
        $token->sessionId = $sessionId;
        $token->token = md5(uniqid(rand(), true));
        $token->userHash = $this->getUserHash($userinfo);
        $token->user_id = $userinfo->id;
        $token->refreshTimestamp = time();
        $token->expiredTimestamp = time() + $this->tokenExpired;
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

        if($userinfo->failedLogins >= $this->maxLoginRetry && $userinfo->lastFailedLoginTimestamp > (time() - 30)) {
            throw new Exception\RuntimeException('ERR_USER_PASSWORD_WRONG_MAX_TIMES');
        }

        // check if hash of provided password matches the hash in the database
        if(!password_verify($this->password, $userinfo->password)) {
            //MUST be string type here
            $userinfo->failedLogins = (string) ($userinfo->failedLogins + 1);
            $userinfo->lastFailedLoginTimestamp = time();
            $userinfo->save();
            throw new Exception\VerifyFailedException('ERR_USER_PASSWORD_WRONG');
        }

        if($userinfo->status != 'active') {
            throw new Exception\UnauthorizedException('ERR_USER_NOT_ACTIVATED');
        }

        $userinfo->failedLogins = 0;
        $userinfo->lastLoginTimestamp = time();
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
        $userinfo->lastLoginTimestamp = time();
        $userinfo->save();
        $authIdentity = $this->saveUserToSession($userinfo);
        return $authIdentity;
    }


    public function loginWithCookie($tokenString)
    {
        $tokenArray = explode('|', $tokenString);
        if(!$tokenArray || count($tokenArray) < 3) {
            $this->appendMessage(new Message(self::FEEDBACK_TOKEN_FORMAT_INCORRECT));
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
            $this->appendMessage(new Message(self::FEEDBACK_TOKEN_NOT_FOUND));
            return false;
        }

        if($tokenInfo->expiredTimestamp < time()) {
            $this->appendMessage(new Message(self::FEEDBACK_TOKEN_EXPIRED));
            return false;
        }

        $this->id = $tokenInfo->user_id;
        $userinfo = self::findFirst("id = '$this->id'");

        if(!$userinfo) {
            $this->appendMessage(new Message(self::FEEDBACK_ACCOUNT_NOT_FOUND));
            return false;
        }

        if($userinfo->status != 'active') {
            $this->appendMessage(new Message(self::FEEDBACK_ACCOUNT_NOT_ACTIVATED_YET));
            return false;
        }

        $userinfo->failedLogins = 0;
        $userinfo->lastLoginTimestamp = time();
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

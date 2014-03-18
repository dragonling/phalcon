<?php

namespace Eva\EvaUser\Models;


use Eva\EvaUser\Entities;
use \Phalcon\Mvc\Model\Message as Message;

class Login extends Entities\Users
{
    /**
    *  Register Feedbacks
    */
    const FEEDBACK_USERNAME_ALREADY_TAKEN   = 'FEEDBACK_USERNAME_ALREADY_TAKEN';
    const FEEDBACK_USER_EMAIL_ALREADY_TAKEN = 'FEEDBACK_USER_EMAIL_ALREADY_TAKEN';
    const FEEDBACK_ACCOUNT_CREATION_FAILED  = 'FEEDBACK_ACCOUNT_CREATION_FAILED';
    const FEEDBACK_VERIFICATION_MAIL_SENDING_FAILED = 'FEEDBACK_VERIFICATION_MAIL_SENDING_FAILED';
    const FEEDBACK_ACCOUNT_ACTIVATION_FAILED = 'FEEDBACK_ACCOUNT_ACTIVATION_FAILED';

    /**
    *  Login Feedbacks
    */
    const FEEDBACK_LOGIN_FAILED = 'FEEDBACK_LOGIN_FAILED';
    const FEEDBACK_ACCOUNT_NOT_FOUND = 'FEEDBACK_ACCOUNT_NOT_FOUND';
    const FEEDBACK_PASSWORD_WRONG = 'FEEDBACK_PASSWORD_WRONG';
    const FEEDBACK_PASSWORD_WRONG_MAX_TIMES = 'FEEDBACK_PASSWORD_WRONG_MAX_TIMES';
    const FEEDBACK_ACCOUNT_NOT_ACTIVATED_YET = 'FEEDBACK_ACCOUNT_NOT_ACTIVATED_YET';

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
            $this->appendMessage(new Message(self::FEEDBACK_USERNAME_ALREADY_TAKEN));
            return false;
        }

        $userinfo = self::findFirst("email = '$this->email'");
        if($userinfo) {
            $this->appendMessage(new Message(self::FEEDBACK_USER_EMAIL_ALREADY_TAKEN));
            return false;
        }

        $this->status = 'inactive';
        $this->accountType = 'basic';
        // crypt the user's password with the PHP 5.5's password_hash() function, results in a 60 character
        // how those PHP 5.5 functions want the parameter: as an array with, currently only used with 'cost' => XX
        $this->password = password_hash($this->password, PASSWORD_DEFAULT, array('cost' => 10));

        // generate random hash for email verification (40 char string)
        $this->activationHash = sha1(uniqid(mt_rand(), true));
        // generate integer-timestamp for saving of account-creating date
        //$this->creationTimestamp = gmdate('Y-m-d H:i:s');
        $this->creationTimestamp = time();
        $this->providerType = 'DEFAULT';
        if ($this->save() == false) {
            $this->appendMessage(new Message(self::FEEDBACK_ACCOUNT_CREATION_FAILED));
            return false;
        }

        $userinfo = self::findFirst("username = '$this->username'");
        if(!$userinfo) {
            $this->appendMessage(new Message(self::FEEDBACK_ACCOUNT_CREATION_FAILED));
            return false;
        }

        $this->sendVerificationEmail($userinfo->id);

        return true;
    }


    public function sendVerificationEmail($userId)
    {
        $userinfo = self::findFirst("id = $userId");
        if(!$userinfo) {
            $this->appendMessage(new Message(self::FEEDBACK_VERIFICATION_MAIL_SENDING_FAILED));
            return false;
        }

        $mailer = $this->getDi()->get('mailer');
        $message = \Swift_Message::newInstance()
        ->setSubject('Active Your Account')
        ->setFrom(array('noreply@wallstreetcn.com' => 'WallsteetCN'))
        ->setTo(array($userinfo->email => $userinfo->username))
        ->setBody('http://www.goldtoutiao.com/user/verify/' . $userinfo->id . '/' . $userinfo->activationHash)
        ;

        return $mailer->send($message);
    }


    /**
    * checks the email/verification code combination and set the user's activation status to active in the database
    * @param int $user_id user id
    * @param string $user_activation_verification_code verification token
    * @return bool success status
    */
    public function verifyNewUser($userId, $activationCode)
    {
        $userinfo = self::findFirst("id = $userId");
        if(!$userinfo) {
            $this->appendMessage(new Message(self::FEEDBACK_ACCOUNT_ACTIVATION_FAILED));
            return false;
        }

        if($userinfo->activationHash != $activationCode) {
            $this->appendMessage(new Message(self::FEEDBACK_ACCOUNT_ACTIVATION_FAILED));
            return false;
        }

        $userinfo->status = 'active';
        if ($userinfo->save() == false) {
            $this->appendMessage(new Message(self::FEEDBACK_ACCOUNT_CREATION_FAILED));
            return false;
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
        $sessionId = $this->getDi()->get('session')->getId();
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

    public function userToAuthIdentity(Entities\Users $userinfo)
    {
        return array(
            'id' => $userinfo->id,
            'username' => $userinfo->username,
        );
    }


    public function login()
    {
        $userinfo = array();
        if($this->id) {
            $userinfo = self::findFirst("id = '$this->id'");
        } elseif($this->username) {
            $userinfo = self::findFirst("username = '$this->username'");
        } elseif($this->email) {
            $userinfo = self::findFirst("email = '$this->email'");
        }

        if(!$userinfo) {
            $this->appendMessage(new Message(self::FEEDBACK_ACCOUNT_NOT_FOUND));
            return false;
        }

        if($userinfo->failedLogins >= $this->maxLoginRetry && $userinfo->lastLoginTimestamp > (time() - 30)) {
            $this->appendMessage(new Message(self::FEEDBACK_PASSWORD_WRONG_MAX_TIMES));
            return false;
        }

        // check if hash of provided password matches the hash in the database
        if(!password_verify($this->password, $userinfo->password)) {
            $this->appendMessage(new Message(self::FEEDBACK_PASSWORD_WRONG));
            $userinfo->failedLogins++;
            $userinfo->lastFailedLoginTimestamp = time();
            $userinfo->save();
            return false;
        }

        if($userinfo->status != 'active') {
            $this->appendMessage(new Message(self::FEEDBACK_ACCOUNT_NOT_ACTIVATED_YET));
            return false;
        }

        $userinfo->failedLogins = 0;
        $userinfo->lastLoginTimestamp = time();
        $userinfo->save();

        $authIdentity = $this->userToAuthIdentity($userinfo);

        $this->getDi()->get('session')->set('auth-identity', $authIdentity);
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

        $authIdentity = $this->userToAuthIdentity($userinfo);

        $this->getDi()->get('session')->set('auth-identity', $authIdentity);

        return true;
    }

    public function getAuthIdentity()
    {
        $authIdentity = $this->getDi()->get('session')->get('auth-identity');
        if($authIdentity) {
            return $authIdentity;
        }
        return false;
    }
}

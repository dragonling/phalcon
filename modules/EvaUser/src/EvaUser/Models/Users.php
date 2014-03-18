<?php

namespace Eva\EvaUser\Models;


use Eva\EvaUser\Entities;
use \Phalcon\Mvc\Model\Message as Message;

class Users extends Entities\Users
{
    const FEEDBACK_USERNAME_ALREADY_TAKEN   = 'FEEDBACK_USERNAME_ALREADY_TAKEN';
    const FEEDBACK_USER_EMAIL_ALREADY_TAKEN = 'FEEDBACK_USER_EMAIL_ALREADY_TAKEN';
    const FEEDBACK_ACCOUNT_CREATION_FAILED  = 'FEEDBACK_ACCOUNT_CREATION_FAILED';
    const FEEDBACK_VERIFICATION_MAIL_SENDING_FAILED = 'FEEDBACK_VERIFICATION_MAIL_SENDING_FAILED';
    const FEEDBACK_ACCOUNT_ACTIVATION_FAILED = 'FEEDBACK_ACCOUNT_ACTIVATION_FAILED';

    const FEEDBACK_LOGIN_FAILED = 'FEEDBACK_LOGIN_FAILED';
    const FEEDBACK_ACCOUNT_NOT_FOUND = 'FEEDBACK_ACCOUNT_NOT_FOUND';
    const FEEDBACK_PASSWORD_WRONG = 'FEEDBACK_PASSWORD_WRONG';
    const FEEDBACK_PASSWORD_WRONG_MAX_TIMES = 'FEEDBACK_PASSWORD_WRONG_MAX_TIMES';
    const FEEDBACK_ACCOUNT_NOT_ACTIVATED_YET = 'FEEDBACK_ACCOUNT_NOT_ACTIVATED_YET';

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
            return false;
        }
        $sessionId = $this->getDi()->get('session')->getId();
        if(!$sessionId) {
            return false;
        }
        $userinfo = self::findFirst("username = '$this->username'");
        if(!$userinfo) {
            return false;
        }
        $token = new Entities\Tokens();
        $token->sessionId = $sessionId;
        $token->token = md5(uniqid(rand(), true));
        $token->hash = md5($this->tokenSalt . $this->password);
        $token->user_id = $this->id;
        $token->refreshTimestamp = time();
        $token->expiredTimestamp = time() + $this->tokenExpired;
        $token->save();
        $tokenString = $sessionId . '|' . $token->token . '|' . $token->hash;
        //$cookies = $this->getDi()->get('cookies');
        //$cookies->set('realm', $tokenString, $token->expiredTimestamp);
        return $tokenString;
    }



    public function login()
    {
        $userinfo = self::findFirst("username = '$this->username'");
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
        return true;
    }

}

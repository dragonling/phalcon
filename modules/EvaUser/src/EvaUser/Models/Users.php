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
        $this->creationTime = gmdate('Y-m-d H:i:s');
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

}

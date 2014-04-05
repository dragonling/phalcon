<?php

namespace Eva\EvaUser\Models;


use Eva\EvaUser\Entities;
use \Phalcon\Mvc\Model\Message as Message;
use Eva\EvaEngine\Exception;

class Register extends Entities\Users
{
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
        $this->emailStatus = 'inactive';
        $this->accountType = 'basic';
        // crypt the user's password with the PHP 5.5's password_hash() function, results in a 60 character
        // how those PHP 5.5 functions want the parameter: as an array with, currently only used with 'cost' => XX
        $this->password = password_hash($this->password, PASSWORD_DEFAULT, array('cost' => 10));

        // generate random hash for email verification (40 char string)
        $this->activationHash = sha1(uniqid(mt_rand(), true));
        // generate integer-timestamp for saving of account-creating date
        $this->createdAt = time();
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
        $userinfo->activedAt = time();
        $userinfo->emailStatus = 'active';
        $userinfo->emailConfirmedAt = time();
        if (!$userinfo->save()) {
            throw new Exception\RuntimeException('ERR_USER_ACTIVE_FAILED');
        }
        return true;
    }
}

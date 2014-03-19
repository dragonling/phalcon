<?php

namespace Eva\EvaUser\Models;


use Eva\EvaUser\Entities;
use \Phalcon\Mvc\Model\Message as Message;

class ResetPassword extends Entities\Users
{
    const FEEDBACK_USER_NOT_EXIST = 'FEEDBACK_USER_NOT_EXIST';
    const FEEDBACK_RESET_PASSWORD_MAIL_SENDING_FAILED = 'FEEDBACK_RESET_PASSWORD_MAIL_SENDING_FAILED';

    public function resetPassword()
    {
        $userinfo = array();
        if($this->username) {
            $userinfo = self::findFirst("username = '$this->username'");
        } elseif($this->email) {
            $userinfo = self::findFirst("email = '$this->email'");
        }

        if(!$userinfo) {
            $this->appendMessage(new Message(self::FEEDBACK_USER_NOT_EXIST));
            return false;
        }

        // generate random hash for email password reset verification (40 char string)
        $userinfo->passwordResetHash = sha1(uniqid(mt_rand(), true));
        $userinfo->passwordResetTimestamp = time();
        $userinfo->save();

        $this->sendPasswordResetMail($userinfo->id);
        return true;
    }

    public function sendPasswordResetMail($userId)
    {
        $userinfo = self::findFirst("id = $userId");
        if(!$userinfo) {
            $this->appendMessage(new Message(self::FEEDBACK_RESET_PASSWORD_MAIL_SENDING_FAILED));
            return false;
        }

        $mailer = $this->getDi()->get('mailer');
        $message = \Swift_Message::newInstance()
        ->setSubject('Reset Password')
        ->setFrom(array('noreply@wallstreetcn.com' => 'WallsteetCN'))
        ->setTo(array($userinfo->email => $userinfo->username))
        ->setBody('http://www.goldtoutiao.com/user/reset/' . urlencode($userinfo->username) . '/' . $userinfo->passwordResetHash)
        ;

        return $mailer->send($message);
    }
}

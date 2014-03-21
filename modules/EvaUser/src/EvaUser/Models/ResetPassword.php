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
            throw new Exception\ResourceNotFoundException('ERR_USER_NOT_EXIST');
        }

        //status tranfer only allow inactive => active
        if($userinfo->status != 'active') {
            throw new Exception\OperationNotPermitedException('ERR_USER_NOT_ACTIVED');
        }

        // generate random hash for email password reset verification (40 char string)
        $userinfo->passwordResetHash = sha1(uniqid(mt_rand(), true));
        $userinfo->passwordResetTimestamp = time();
        $userinfo->save();

        $this->sendPasswordResetMail($userinfo->email);
        return true;
    }

    public function sendPasswordResetMail($email)
    {
        $userinfo = self::findFirst("email= '$email'");
        if(!$userinfo) {
            throw new Exception\ResourceNotFoundException('ERR_USER_NOT_EXIST');
        }

        $mailer = $this->getDi()->get('mailer');
        $message = \Swift_Message::newInstance()
        ->setSubject('Reset Password')
        ->setFrom(array('noreply@wallstreetcn.com' => 'WallsteetCN'))
        ->setTo(array($userinfo->email => $userinfo->username))
        ->setBody('http://www.goldtoutiao.com/session/reset/' . urlencode($userinfo->username) . '/' . $userinfo->passwordResetHash)
        ;

        return $mailer->send($message);
    }

    /**
    * Verifies the password reset request via the verification hash token (that's only valid for one hour)
    * @param string $userName Username
    * @param string $verificationCode Hash token
    * @return bool Success status
    */
    public function verifyPasswordReset($userName, $verificationCode)
    {
        $userinfo = self::findFirst("user= $userId");
        if(!$userinfo) {
            $this->appendMessage(new Message(self::FEEDBACK_USER_NOT_EXIST));
            return false;
        }

        // check if user-provided username + verification code combination exists
        $query = $this->db->prepare("SELECT user_id, user_password_reset_timestamp
        FROM users
        WHERE user_name = :user_name
        AND user_password_reset_hash = :user_password_reset_hash
        AND user_provider_type = :user_provider_type");
        $query->execute(array(':user_password_reset_hash' => $verification_code,
        ':user_name' => $user_name,
        ':user_provider_type' => 'DEFAULT'));

        // if this user with exactly this verification hash code exists
        if ($query->rowCount() != 1) {
            $_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_RESET_COMBINATION_DOES_NOT_EXIST;
            return false;
        }

        // get result row (as an object)
        $result_user_row = $query->fetch();
        // 3600 seconds are 1 hour
        $timestamp_one_hour_ago = time() - 3600;
        // if password reset request was sent within the last hour (this timeout is for security reasons)
        if ($result_user_row->user_password_reset_timestamp > $timestamp_one_hour_ago) {
            // verification was successful
            $_SESSION["feedback_positive"][] = FEEDBACK_PASSWORD_RESET_LINK_VALID;
            return true;
        } else {
            $_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_RESET_LINK_EXPIRED;
            return false;
        }
    }
}

<?php

namespace Eva\EvaUser\Models;


use Eva\EvaUser\Entities;
use \Phalcon\Mvc\Model\Message as Message;

class Users extends Entities\Users
{
    const FEEDBACK_USERNAME_ALREADY_TAKEN   = 'FEEDBACK_USERNAME_ALREADY_TAKEN';
    const FEEDBACK_USER_EMAIL_ALREADY_TAKEN = 'FEEDBACK_USER_EMAIL_ALREADY_TAKEN';
    const FEEDBACK_ACCOUNT_CREATION_FAILED  = 'FEEDBACK_ACCOUNT_CREATION_FAILED';

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

        return true;
        exit;

        // get user_id of the user that has been created, to keep things clean we DON'T use lastInsertId() here
        $query = $this->db->prepare("SELECT user_id FROM users WHERE user_name = :user_name");
        $query->execute(array(':user_name' => $user_name));
        if ($query->rowCount() != 1) {
            $_SESSION["feedback_negative"][] = FEEDBACK_UNKNOWN_ERROR;
            return false;
        }
        $result_user_row = $query->fetch();
        $user_id = $result_user_row->user_id;

        // send verification email, if verification email sending failed: instantly delete the user
        if ($this->sendVerificationEmail($user_id, $user_email, $user_activation_hash)) {
            $_SESSION["feedback_positive"][] = FEEDBACK_ACCOUNT_SUCCESSFULLY_CREATED;
            return true;
        } else {
            $query = $this->db->prepare("DELETE FROM users WHERE user_id = :last_inserted_id");
            $query->execute(array(':last_inserted_id' => $user_id));
            $_SESSION["feedback_negative"][] = FEEDBACK_VERIFICATION_MAIL_SENDING_FAILED;
            return false;
        }

    }

}

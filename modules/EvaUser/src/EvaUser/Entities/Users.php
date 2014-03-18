<?php

namespace Eva\EvaUser\Entities;

use Phalcon\Mvc\Model\Validator\Email as Email;

class Users extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;
     
    /**
     *
     * @var string
     */
    public $username;
     
    /**
     *
     * @var string
     */
    public $email;
     
    /**
     *
     * @var string
     */
    public $mobile;
     
    /**
     *
     * @var string
     */
    public $status;
     
    /**
     *
     * @var string
     */
    public $accountType;
     
    /**
     *
     * @var string
     */
    public $screenName;
     
    /**
     *
     * @var string
     */
    public $firstName;
     
    /**
     *
     * @var string
     */
    public $lastName;
     
    /**
     *
     * @var string
     */
    public $password;
     
    /**
     *
     * @var string
     */
    public $oldPassword;
     
    /**
     *
     * @var string
     */
    public $gender;
     
    /**
     *
     * @var integer
     */
    public $avatar_id;
     
    /**
     *
     * @var string
     */
    public $avatar;
     
    /**
     *
     * @var string
     */
    public $timezone;
     
    /**
     *
     * @var string
     */
    public $language;
     
    /**
     *
     * @var string
     */
    public $remembermeToken;
     
    /**
     *
     * @var integer
     */
    public $creationTimestamp;
     
    /**
     *
     * @var integer
     */
    public $lastLoginTimestamp;
     
    /**
     *
     * @var string
     */
    public $failedLogins;
     
    /**
     *
     * @var integer
     */
    public $lastFailedLoginTimestamp;
     
    /**
     *
     * @var string
     */
    public $activationHash;
     
    /**
     *
     * @var string
     */
    public $passwordResetHash;
     
    /**
     *
     * @var integer
     */
    public $passwordResetTimestamp;
     
    /**
     *
     * @var string
     */
    public $providerType;

    public function validation()
    {

        $this->validate(
            new Email(
                array(
                    "field"    => "email",
                    "required" => true,
                )
            )
        );
        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'username' => 'username', 
            'email' => 'email', 
            'mobile' => 'mobile', 
            'status' => 'status', 
            'accountType' => 'accountType', 
            'screenName' => 'screenName', 
            'firstName' => 'firstName', 
            'lastName' => 'lastName', 
            'password' => 'password', 
            'oldPassword' => 'oldPassword', 
            'gender' => 'gender', 
            'avatar_id' => 'avatar_id', 
            'avatar' => 'avatar', 
            'timezone' => 'timezone', 
            'language' => 'language', 
            'remembermeToken' => 'remembermeToken', 
            'creationTimestamp' => 'creationTimestamp', 
            'lastLoginTimestamp' => 'lastLoginTimestamp', 
            'failedLogins' => 'failedLogins', 
            'lastFailedLoginTimestamp' => 'lastFailedLoginTimestamp', 
            'activationHash' => 'activationHash', 
            'passwordResetHash' => 'passwordResetHash', 
            'passwordResetTimestamp' => 'passwordResetTimestamp', 
            'providerType' => 'providerType'
        );
    }

     
    public function getSource() {
        return 'eva_user_users';
    }

}

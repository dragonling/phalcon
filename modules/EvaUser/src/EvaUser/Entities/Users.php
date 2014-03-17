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
     * @var string
     */
    public $creationTime;
     
    /**
     *
     * @var string
     */
    public $lastLoginTime;
     
    /**
     *
     * @var string
     */
    public $failedLogins;
     
    /**
     *
     * @var string
     */
    public $lastFailedLogin;
     
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
     * @var string
     */
    public $passwordResetTime;

    /**
     *
     * @var string
     */
    public $providerType;
     
    /**
     * Validations and business logic
     */
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

    public function getSource() {
        return 'eva_user_users';
    }

}

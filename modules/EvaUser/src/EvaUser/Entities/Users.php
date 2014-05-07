<?php

namespace Eva\EvaUser\Entities;

use Phalcon\Mvc\Model\Validator\Email as Email;

class Users extends \Eva\EvaEngine\Mvc\Model
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
    public $status = 'inactive';
     
    /**
     *
     * @var string
     */
    public $accountType = 'basic';
     
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
    public $avatarId;
     
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
    public $emailStatus = 'inactive';
     
    /**
     *
     * @var integer
     */
    public $emailConfirmedAt;
     
    /**
     *
     * @var integer
     */
    public $createdAt;
     
    /**
     *
     * @var integer
     */
    public $loginAt;
     
    /**
     *
     * @var string
     */
    public $failedLogins;
     
    /**
     *
     * @var integer
     */
    public $loginFailedAt;
     
    /**
     *
     * @var string
     */
    public $activationHash;
     
    /**
     *
     * @var integer
     */
    public $activedAt;
     
    /**
     *
     * @var string
     */
    public $passwordResetHash;
     
    /**
     *
     * @var integer
     */
    public $passwordResetAt;
     
    /**
     *
     * @var string
     */
    public $providerType = 'DEFAULT';
     
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
            'avatarId' => 'avatarId', 
            'avatar' => 'avatar', 
            'timezone' => 'timezone', 
            'language' => 'language', 
            'emailStatus' => 'emailStatus', 
            'emailConfirmedAt' => 'emailConfirmedAt', 
            'createdAt' => 'createdAt', 
            'loginAt' => 'loginAt', 
            'failedLogins' => 'failedLogins', 
            'loginFailedAt' => 'loginFailedAt', 
            'activationHash' => 'activationHash', 
            'activedAt' => 'activedAt', 
            'passwordResetHash' => 'passwordResetHash', 
            'passwordResetAt' => 'passwordResetAt', 
            'providerType' => 'providerType'
        );
    }

    protected $tableName = 'user_users';

    public function initialize()
    {
        $this->hasOne('id', 'Eva\EvaUser\Entities\Profiles', 'userId', array(
            'alias' => 'Profile'
        ));

        parent::initialize();
    }
}

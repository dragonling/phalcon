<?php


namespace Wscn\Frontend\Models;
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
    public $flag;
     
    /**
     *
     * @var string
     */
    public $screenName;
     
    /**
     *
     * @var string
     */
    public $salt;
     
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
    public $lastUpdateTime;
     
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
    public $registerTime;
     
    /**
     *
     * @var string
     */
    public $lastLoginTime;
     
    /**
     *
     * @var string
     */
    public $language;
     
    /**
     *
     * @var integer
     */
    public $setting;
     
    /**
     *
     * @var integer
     */
    public $inviteUserId;
     
    /**
     *
     * @var string
     */
    public $onlineStatus;
     
    /**
     *
     * @var string
     */
    public $lastFreshTime;
     
    /**
     *
     * @var integer
     */
    public $viewCount;
     
    /**
     *
     * @var string
     */
    public $registerIp;
     
    /**
     *
     * @var string
     */
    public $lastLoginIp;
     
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

}

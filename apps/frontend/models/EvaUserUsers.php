<?php


use Phalcon\Mvc\Model\Validator\Email as Email;

class EvaUserUsers extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    protected $id;
     
    /**
     *
     * @var string
     */
    protected $username;
     
    /**
     *
     * @var string
     */
    protected $email;
     
    /**
     *
     * @var string
     */
    protected $mobile;
     
    /**
     *
     * @var string
     */
    protected $status;
     
    /**
     *
     * @var string
     */
    protected $flag;
     
    /**
     *
     * @var string
     */
    protected $screenName;
     
    /**
     *
     * @var string
     */
    protected $salt;
     
    /**
     *
     * @var string
     */
    protected $firstName;
     
    /**
     *
     * @var string
     */
    protected $lastName;
     
    /**
     *
     * @var string
     */
    protected $password;
     
    /**
     *
     * @var string
     */
    protected $oldPassword;
     
    /**
     *
     * @var string
     */
    protected $lastUpdateTime;
     
    /**
     *
     * @var string
     */
    protected $gender;
     
    /**
     *
     * @var integer
     */
    protected $avatar_id;
     
    /**
     *
     * @var string
     */
    protected $avatar;
     
    /**
     *
     * @var string
     */
    protected $timezone;
     
    /**
     *
     * @var string
     */
    protected $registerTime;
     
    /**
     *
     * @var string
     */
    protected $lastLoginTime;
     
    /**
     *
     * @var string
     */
    protected $language;
     
    /**
     *
     * @var integer
     */
    protected $setting;
     
    /**
     *
     * @var integer
     */
    protected $inviteUserId;
     
    /**
     *
     * @var string
     */
    protected $onlineStatus;
     
    /**
     *
     * @var string
     */
    protected $lastFreshTime;
     
    /**
     *
     * @var integer
     */
    protected $viewCount;
     
    /**
     *
     * @var string
     */
    protected $registerIp;
     
    /**
     *
     * @var string
     */
    protected $lastLoginIp;
     
    /**
     * Method to set the value of field id
     *
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Method to set the value of field username
     *
     * @param string $username
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Method to set the value of field email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Method to set the value of field mobile
     *
     * @param string $mobile
     * @return $this
     */
    public function setMobile($mobile)
    {
        $this->mobile = $mobile;

        return $this;
    }

    /**
     * Method to set the value of field status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Method to set the value of field flag
     *
     * @param string $flag
     * @return $this
     */
    public function setFlag($flag)
    {
        $this->flag = $flag;

        return $this;
    }

    /**
     * Method to set the value of field screenName
     *
     * @param string $screenName
     * @return $this
     */
    public function setScreenname($screenName)
    {
        $this->screenName = $screenName;

        return $this;
    }

    /**
     * Method to set the value of field salt
     *
     * @param string $salt
     * @return $this
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Method to set the value of field firstName
     *
     * @param string $firstName
     * @return $this
     */
    public function setFirstname($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Method to set the value of field lastName
     *
     * @param string $lastName
     * @return $this
     */
    public function setLastname($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Method to set the value of field password
     *
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Method to set the value of field oldPassword
     *
     * @param string $oldPassword
     * @return $this
     */
    public function setOldpassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    /**
     * Method to set the value of field lastUpdateTime
     *
     * @param string $lastUpdateTime
     * @return $this
     */
    public function setLastupdatetime($lastUpdateTime)
    {
        $this->lastUpdateTime = $lastUpdateTime;

        return $this;
    }

    /**
     * Method to set the value of field gender
     *
     * @param string $gender
     * @return $this
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Method to set the value of field avatar_id
     *
     * @param integer $avatar_id
     * @return $this
     */
    public function setAvatarId($avatar_id)
    {
        $this->avatar_id = $avatar_id;

        return $this;
    }

    /**
     * Method to set the value of field avatar
     *
     * @param string $avatar
     * @return $this
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Method to set the value of field timezone
     *
     * @param string $timezone
     * @return $this
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Method to set the value of field registerTime
     *
     * @param string $registerTime
     * @return $this
     */
    public function setRegistertime($registerTime)
    {
        $this->registerTime = $registerTime;

        return $this;
    }

    /**
     * Method to set the value of field lastLoginTime
     *
     * @param string $lastLoginTime
     * @return $this
     */
    public function setLastlogintime($lastLoginTime)
    {
        $this->lastLoginTime = $lastLoginTime;

        return $this;
    }

    /**
     * Method to set the value of field language
     *
     * @param string $language
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Method to set the value of field setting
     *
     * @param integer $setting
     * @return $this
     */
    public function setSetting($setting)
    {
        $this->setting = $setting;

        return $this;
    }

    /**
     * Method to set the value of field inviteUserId
     *
     * @param integer $inviteUserId
     * @return $this
     */
    public function setInviteuserid($inviteUserId)
    {
        $this->inviteUserId = $inviteUserId;

        return $this;
    }

    /**
     * Method to set the value of field onlineStatus
     *
     * @param string $onlineStatus
     * @return $this
     */
    public function setOnlinestatus($onlineStatus)
    {
        $this->onlineStatus = $onlineStatus;

        return $this;
    }

    /**
     * Method to set the value of field lastFreshTime
     *
     * @param string $lastFreshTime
     * @return $this
     */
    public function setLastfreshtime($lastFreshTime)
    {
        $this->lastFreshTime = $lastFreshTime;

        return $this;
    }

    /**
     * Method to set the value of field viewCount
     *
     * @param integer $viewCount
     * @return $this
     */
    public function setViewcount($viewCount)
    {
        $this->viewCount = $viewCount;

        return $this;
    }

    /**
     * Method to set the value of field registerIp
     *
     * @param string $registerIp
     * @return $this
     */
    public function setRegisterip($registerIp)
    {
        $this->registerIp = $registerIp;

        return $this;
    }

    /**
     * Method to set the value of field lastLoginIp
     *
     * @param string $lastLoginIp
     * @return $this
     */
    public function setLastloginip($lastLoginIp)
    {
        $this->lastLoginIp = $lastLoginIp;

        return $this;
    }

    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of field username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Returns the value of field email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Returns the value of field mobile
     *
     * @return string
     */
    public function getMobile()
    {
        return $this->mobile;
    }

    /**
     * Returns the value of field status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns the value of field flag
     *
     * @return string
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * Returns the value of field screenName
     *
     * @return string
     */
    public function getScreenname()
    {
        return $this->screenName;
    }

    /**
     * Returns the value of field salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Returns the value of field firstName
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstName;
    }

    /**
     * Returns the value of field lastName
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastName;
    }

    /**
     * Returns the value of field password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the value of field oldPassword
     *
     * @return string
     */
    public function getOldpassword()
    {
        return $this->oldPassword;
    }

    /**
     * Returns the value of field lastUpdateTime
     *
     * @return string
     */
    public function getLastupdatetime()
    {
        return $this->lastUpdateTime;
    }

    /**
     * Returns the value of field gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Returns the value of field avatar_id
     *
     * @return integer
     */
    public function getAvatarId()
    {
        return $this->avatar_id;
    }

    /**
     * Returns the value of field avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Returns the value of field timezone
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Returns the value of field registerTime
     *
     * @return string
     */
    public function getRegistertime()
    {
        return $this->registerTime;
    }

    /**
     * Returns the value of field lastLoginTime
     *
     * @return string
     */
    public function getLastlogintime()
    {
        return $this->lastLoginTime;
    }

    /**
     * Returns the value of field language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Returns the value of field setting
     *
     * @return integer
     */
    public function getSetting()
    {
        return $this->setting;
    }

    /**
     * Returns the value of field inviteUserId
     *
     * @return integer
     */
    public function getInviteuserid()
    {
        return $this->inviteUserId;
    }

    /**
     * Returns the value of field onlineStatus
     *
     * @return string
     */
    public function getOnlinestatus()
    {
        return $this->onlineStatus;
    }

    /**
     * Returns the value of field lastFreshTime
     *
     * @return string
     */
    public function getLastfreshtime()
    {
        return $this->lastFreshTime;
    }

    /**
     * Returns the value of field viewCount
     *
     * @return integer
     */
    public function getViewcount()
    {
        return $this->viewCount;
    }

    /**
     * Returns the value of field registerIp
     *
     * @return string
     */
    public function getRegisterip()
    {
        return $this->registerIp;
    }

    /**
     * Returns the value of field lastLoginIp
     *
     * @return string
     */
    public function getLastloginip()
    {
        return $this->lastLoginIp;
    }

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
            'flag' => 'flag', 
            'screenName' => 'screenName', 
            'salt' => 'salt', 
            'firstName' => 'firstName', 
            'lastName' => 'lastName', 
            'password' => 'password', 
            'oldPassword' => 'oldPassword', 
            'lastUpdateTime' => 'lastUpdateTime', 
            'gender' => 'gender', 
            'avatar_id' => 'avatar_id', 
            'avatar' => 'avatar', 
            'timezone' => 'timezone', 
            'registerTime' => 'registerTime', 
            'lastLoginTime' => 'lastLoginTime', 
            'language' => 'language', 
            'setting' => 'setting', 
            'inviteUserId' => 'inviteUserId', 
            'onlineStatus' => 'onlineStatus', 
            'lastFreshTime' => 'lastFreshTime', 
            'viewCount' => 'viewCount', 
            'registerIp' => 'registerIp', 
            'lastLoginIp' => 'lastLoginIp'
        );
    }

}

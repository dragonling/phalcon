<?php
namespace Eva\EvaBlog\Forms;

use Eva\EvaEngine\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Check;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\Identical;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Confirmation;

class PostForm extends Form
{
    /**
     * @Type(Hidden)
     * @var integer
     */
    public $id;
     
    /**
     *
     * @Validator("PresenceOf", message = "Please input title")
     * @var string
     */
    public $title;
     
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
    public $visibility;
     
    /**
     *
     * @var string
     */
    public $sourceCode;
     
    /**
     *
     * @var string
     */
    public $language;
     
    /**
     *
     * @var integer
     */
    public $parentId;
     
    /**
     *
     * @var string
     */
    public $slug;
     
    /**
     *
     * @var integer
     */
    public $sortOrder;
     
    /**
     *
     * @var integer
     */
    public $createdAt;
     
    /**
     *
     * @var integer
     */
    public $user_id;
     
    /**
     *
     * @var string
     */
    public $username;
     
    /**
     *
     * @var integer
     */
    public $updatedAt;
     
    /**
     *
     * @var integer
     */
    public $editor_id;
     
    /**
     *
     * @var string
     */
    public $editor_name;
     
    /**
     *
     * @var string
     */
    public $commentStatus;
     
    /**
     *
     * @var string
     */
    public $commentType;
     
    /**
     *
     * @var integer
     */
    public $commentCount;
     
    /**
     *
     * @var integer
     */
    public $count;
     
    /**
     *
     * @var integer
     */
    public $image_id;
     
    /**
     *
     * @var string
     */
    public $image;
     
    /**
     *
     * @var string
     */
    public $summary;

}

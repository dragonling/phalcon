<?php
namespace Eva\EvaBlog\Forms;

use Eva\EvaEngine\Form;

class CategoryForm extends Form
{
    /**
     * @Type(Hidden)
     * @var integer
     */
    public $id;
     
    /**
     * @Validator("PresenceOf", message = "Please input category name")
     * @var string
     */
    public $categoryName;
     
    /**
     *
     * @var string
     */
    public $slug;
     
    /**
     * @Type(TextArea)
     * @var string
     */
    public $description;
     
    /**
     *
     * @var integer
     */
    public $parentId;
     
    /**
     *
     * @var integer
     */
    public $rootId;
     
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
    public $count;
     
    /**
     *
     * @var integer
     */
    public $leftId;
     
    /**
     *
     * @var integer
     */
    public $rightId;

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

}

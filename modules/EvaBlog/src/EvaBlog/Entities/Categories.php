<?php


namespace Eva\EvaBlog\Entities;


class Categories extends \Eva\EvaEngine\Model
{
    protected $tableName = 'blog_categories';

    /**
     *
     * @var integer
     */
    public $id;
     
    /**
     *
     * @var string
     */
    public $categoryName;
     
    /**
     *
     * @var string
     */
    public $slug;
     
    /**
     *
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
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'categoryName' => 'categoryName', 
            'slug' => 'slug', 
            'description' => 'description', 
            'parentId' => 'parentId', 
            'rootId' => 'rootId', 
            'sortOrder' => 'sortOrder', 
            'createdAt' => 'createdAt', 
            'count' => 'count', 
            'leftId' => 'leftId', 
            'rightId' => 'rightId'
        );
    }
}

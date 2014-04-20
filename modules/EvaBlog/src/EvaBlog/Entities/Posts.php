<?php

namespace Eva\EvaBlog\Entities;

use Eva\EvaBlog\Entities\Texts;

class Posts extends \Eva\EvaEngine\Mvc\Model
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
    public $title;
     
    /**
     *
     * @var string
     */
    public $status = 'pending';
     
    /**
     *
     * @var string
     */
    public $flag;
     
    /**
     *
     * @var string
     */
    public $visibility = 'public';
     
    /**
     *
     * @var string
     */
    public $sourceCode = 'html';
     
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
     
    /**
     *
     * @var string
     */
    public $source;
     
    /**
     *
     * @var string
     */
    public $sourceUrl;
     
    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'title' => 'title', 
            'status' => 'status', 
            'flag' => 'flag', 
            'visibility' => 'visibility', 
            'sourceCode' => 'sourceCode', 
            'language' => 'language', 
            'parentId' => 'parentId', 
            'slug' => 'slug', 
            'sortOrder' => 'sortOrder', 
            'createdAt' => 'createdAt', 
            'user_id' => 'user_id', 
            'username' => 'username', 
            'updatedAt' => 'updatedAt', 
            'editor_id' => 'editor_id', 
            'editor_name' => 'editor_name', 
            'commentStatus' => 'commentStatus', 
            'commentType' => 'commentType', 
            'commentCount' => 'commentCount', 
            'count' => 'count', 
            'image_id' => 'image_id', 
            'image' => 'image', 
            'summary' => 'summary',
            'source' => 'source',
            'sourceUrl' => 'sourceUrl',
        );
    }

    protected $tableName = 'blog_posts';

    public function initialize()
    {
        $this->hasOne('id', 'Eva\EvaBlog\Entities\Texts', 'post_id', array(
            'alias' => 'Text'
        ));

        $this->belongsTo('user_id', 'Eva\EvaUser\Entities\Users', 'id', array(
            'alias' => 'User'
        ));

        $this->hasMany(
            'id',
            'Eva\EvaBlog\Entities\CategoriesPosts',
            'post_id',
            array('alias' => 'CategoriesPosts')
        );

        $this->hasManyToMany(
            'id',
            'Eva\EvaBlog\Entities\CategoriesPosts',
            'post_id',
            'category_id',
            'Eva\EvaBlog\Entities\Categories',
            'id',
            array('alias' => 'Categories')
        );

        $this->hasMany(
            'id',
            'Eva\EvaBlog\Entities\TagsPosts',
            'post_id',
            array('alias' => 'TagsPosts')
        );

        $this->hasManyToMany(
            'id',
            'Eva\EvaBlog\Entities\TagsPosts',
            'post_id',
            'tag_id',
            'Eva\EvaBlog\Entities\Tags',
            'id',
            array('alias' => 'Tags')
        );

        parent::initialize();
    }
}

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
    public $codeType = 'markdown';

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
    public $userId;

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
    public $editorId;

    /**
     *
     * @var string
     */
    public $editorName;

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
    public $imageId;

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
    public $sourceName;

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
            'codeType' => 'codeType',
            'language' => 'language',
            'parentId' => 'parentId',
            'slug' => 'slug',
            'sortOrder' => 'sortOrder',
            'createdAt' => 'createdAt',
            'userId' => 'userId',
            'username' => 'username',
            'updatedAt' => 'updatedAt',
            'editorId' => 'editorId',
            'editorName' => 'editorName',
            'commentStatus' => 'commentStatus',
            'commentType' => 'commentType',
            'commentCount' => 'commentCount',
            'count' => 'count',
            'imageId' => 'imageId',
            'image' => 'image',
            'summary' => 'summary',
            'sourceName' => 'sourceName',
            'sourceUrl' => 'sourceUrl',
        );
    }

    protected $tableName = 'blog_posts';

    public function initialize()
    {
        $this->hasOne('id', 'Eva\EvaBlog\Entities\Texts', 'postId', array(
            'alias' => 'Text'
        ));

        $this->belongsTo('userId', 'Eva\EvaUser\Entities\Users', 'id', array(
            'alias' => 'User'
        ));

        $this->hasMany(
            'id',
            'Eva\EvaBlog\Entities\CategoriesPosts',
            'postId',
            array('alias' => 'CategoriesPosts')
        );

        $this->hasManyToMany(
            'id',
            'Eva\EvaBlog\Entities\CategoriesPosts',
            'postId',
            'categoryId',
            'Eva\EvaBlog\Entities\Categories',
            'id',
            array('alias' => 'Categories')
        );

        $this->hasMany(
            'id',
            'Eva\EvaBlog\Entities\TagsPosts',
            'postId',
            array('alias' => 'TagsPosts')
        );

        $this->hasManyToMany(
            'id',
            'Eva\EvaBlog\Entities\TagsPosts',
            'postId',
            'tagId',
            'Eva\EvaBlog\Entities\Tags',
            'id',
            array('alias' => 'Tags')
        );

        parent::initialize();
    }
}

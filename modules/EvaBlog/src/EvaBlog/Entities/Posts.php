<?php

namespace Eva\EvaBlog\Entities;

use Eva\EvaBlog\Entities\Texts;

class Posts extends \Eva\EvaEngine\Model
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
    public $codeType;
     
    /**
     *
     * @var string
     */
    public $language;
     
    /**
     *
     * @var string
     */
    public $parentId;
     
    /**
     *
     * @var integer
     */
    public $connect_id;
     
    /**
     *
     * @var string
     */
    public $trackback;
     
    /**
     *
     * @var string
     */
    public $urlName;
     
    /**
     *
     * @var string
     */
    public $preview;
     
    /**
     *
     * @var integer
     */
    public $orderNumber;
     
    /**
     *
     * @var integer
     */
    public $setting;
     
    /**
     *
     * @var string
     */
    public $createTime;
     
    /**
     *
     * @var integer
     */
    public $user_id;
     
    /**
     *
     * @var string
     */
    public $user_name;
     
    /**
     *
     * @var string
     */
    public $updateTime;
     
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
    public $postPassword;
     
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
    public $viewCount;
     
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
            'connect_id' => 'connect_id', 
            'trackback' => 'trackback', 
            'urlName' => 'urlName', 
            'preview' => 'preview', 
            'orderNumber' => 'orderNumber', 
            'setting' => 'setting', 
            'createTime' => 'createTime', 
            'user_id' => 'user_id', 
            'user_name' => 'user_name', 
            'updateTime' => 'updateTime', 
            'editor_id' => 'editor_id', 
            'editor_name' => 'editor_name', 
            'postPassword' => 'postPassword', 
            'commentStatus' => 'commentStatus', 
            'commentType' => 'commentType', 
            'commentCount' => 'commentCount', 
            'viewCount' => 'viewCount'
        );
    }

    protected $tableName = 'blog_posts';

    public function initialize()
    {
        $this->hasOne("id", 'Eva\EvaBlog\Entities\Texts', "post_id", array(
            'alias' => 'Text'
        ));
        $this->belongsTo("user_id", 'Eva\EvaUser\Entities\Users', "id", array(
            'alias' => 'User'
        ));
        parent::initialize();
    }
}

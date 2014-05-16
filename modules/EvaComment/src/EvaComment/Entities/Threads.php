<?php

namespace Eva\EvaComment\Entities;

use Eva\EvaEngine\Mvc\Model as BaseModel;

class Threads extends BaseModel
{
    protected $tableName = 'comment_threads';

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $uniqueKey;

    /**
     *
     * @var string
     */
    public $permalink;

    /**
     *
     * @var string
     */
    public $title;

    /**
     *
     * @var string
     */
    public $isCommentAble;

    /**
     *
     * @var string
     */
    public $numComments;

    /**
     *
     * @var integer
     */
    public $lastCommentAt;

    /**
     *
     * @var string
     */
    public $channel;

    public function onConstruct()
    {
        $this->title = '';
        $this->isCommentAble = '';
        $this->numComments = 0;
        $this->lastCommentAt = time();
        $this->channel = 0;
    }

    public function isCommentable()
    {
        return $this->isCommentAble;
    }

}

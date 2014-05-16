<?php
namespace Eva\EvaComment\Entities;

use Eva\EvaEngine\Mvc\Model as BaseModel;

class Comments extends BaseModel
{
    protected $tableName = 'comment_comments';

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $threadId;

    /**
     *
     * @var string
     */
    public $status;

    /**
     *
     * @var string
     */
    public $sourceCode;

    /**
     *
     * @var string
     */
    public $content;

    /**
     *
     * @var integer
     */
    public $ancestorId;

    /**
     *
     * @var integer
     */
    public $parentId;

    /**
     *
     * @var string
     */
    public $parentPath;

    /**
     *
     * @var integer
     */
    public $depth;

    /**
     *
     * @var integer
     */
    public $numReply;

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
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $userSite;

    /**
     *
     * @var string
     */
    public $userType;

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
     *
     * @var integer
     */
    public $createdAt;

    public function initialize()
    {
        $this->belongsTo('threadId', '\Eva\EvaComment\Entities\Threads', 'id', array(
                'alias' => 'Thread',
                'foreignKey' => true
            ));

    }

    public function onConstruct()
    {
        $this->username = 'anonymous';
        $this->numReply = 0;
        $this->parentId = 0;
        $this->ancestorId = 0;
        $this->status = 0;
        $this->createdAt = time();
    }
}

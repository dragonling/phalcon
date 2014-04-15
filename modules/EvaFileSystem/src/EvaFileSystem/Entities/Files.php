<?php

namespace Eva\EvaFileSystem\Entities;

class Files extends \Eva\EvaEngine\Mvc\Model
{
    protected $tableName = 'file_files';

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
    public $storageAdapter;
     
    /**
     *
     * @var string
     */
    public $isImage;
     
    /**
     *
     * @var string
     */
    public $fileName;
     
    /**
     *
     * @var string
     */
    public $fileExtension;
     
    /**
     *
     * @var string
     */
    public $originalName;
     
    /**
     *
     * @var string
     */
    public $filePath;
     
    /**
     *
     * @var string
     */
    public $fileHash;
     
    /**
     *
     * @var integer
     */
    public $fileSize;
     
    /**
     *
     * @var string
     */
    public $mimeType;
     
    /**
     *
     * @var integer
     */
    public $imageWidth;
     
    /**
     *
     * @var integer
     */
    public $imageHeight;
     
    /**
     *
     * @var string
     */
    public $description;
     
    /**
     *
     * @var integer
     */
    public $sortOrder;
     
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
    public $createdAt;
     
    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'title' => 'title', 
            'status' => 'status', 
            'storageAdapter' => 'storageAdapter', 
            'isImage' => 'isImage', 
            'fileName' => 'fileName', 
            'fileExtension' => 'fileExtension', 
            'originalName' => 'originalName', 
            'filePath' => 'filePath', 
            'fileHash' => 'fileHash', 
            'fileSize' => 'fileSize', 
            'mimeType' => 'mimeType', 
            'imageWidth' => 'imageWidth', 
            'imageHeight' => 'imageHeight', 
            'description' => 'description', 
            'sortOrder' => 'sortOrder', 
            'user_id' => 'user_id', 
            'username' => 'username', 
            'createdAt' => 'createdAt'
        );
    }


    public function initialize()
    {
        $this->belongsTo('user_id', 'Eva\EvaUser\Entities\Users', 'id', array(
            'alias' => 'User'
        ));
    }
}

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
            'userId' => 'userId',
            'username' => 'username',
            'createdAt' => 'createdAt'
        );
    }

    public function getFullUrl()
    {
        if (!$this->id) {
            return '';
        }
        if ($url = $this->getDI()->get('config')->filesystem->urlBaseForCDN) {
            return $url . '/' . $this->filePath . '/' . $this->fileName;
        }

        return $this->getLocalUrl();
    }

    public function getLocalUrl()
    {
        if (!$this->id) {
            return '';
        }

        return $this->getDI()->get('config')->filesystem->urlBaseForLocal . '/' . $this->filePath . '/' . $this->fileName;
    }

    public function getLocalPath()
    {
        if (!$this->id) {
            return '';
        }

        return $this->getDI()->get('config')->filesystem->uploadPath . '/'. $this->filePath . '/' . $this->fileName;
    }

    public function getReadableFileSize()
    {
        $size = $this->fileSize;
        $units = array(' B', ' KB', ' MB', ' GB', ' TB');
        for ($i = 0; $size >= 1024 && $i < 4; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . $units[$i];
    }

    public function initialize()
    {
        $this->belongsTo('userId', 'Eva\EvaUser\Entities\Users', 'id', array(
            'alias' => 'User'
        ));
    }
}

<?php

namespace Eva\EvaFileSystem\Models;

use Eva\EvaFileSystem\Entities\Files;
use Eva\EvaUser\Models\Login as LoginModel;
use Eva\EvaEngine\Exception;

class Upload extends Files
{
    public function beforeCreate()
    {
        $user = new LoginModel();
        if($userinfo = $user->isUserLoggedIn()) {
            $this->user_id = $userinfo['id'];
            $this->username = $userinfo['username'];
        }
    }

    public function getFullUrl()
    {
        if(!$this->id) {
            return '';
        }

        return $this->getDI()->get('config')->filesystem->uploadUrlBase . '/' . $this->filePath . '/' . $this->fileName;
    }

    public function getLocalPath()
    {
        if(!$this->id) {
            return '';
        }
        return $this->getDI()->get('config')->filesystem->uploadPath . '/'. $this->filePath . '/' . $this->fileName;
    }

    public function upload(\Phalcon\Http\Request\File $file)
    {
        if($file->getError()){
            throw new Exception\IOException('ERR_FILE_UPLOAD_FAILED');
        }

        $originalName = $file->getName();
        $tmp = $file->getTempName();
        $fileSize = $file->getSize();
        $type = $file->getType();
        $filenameArray = explode(".", $originalName);
        $fileExtension = strtolower(array_pop($filenameArray));
        $originalFileName = implode('.', $filenameArray);
        $fileName = \Phalcon\Tag::friendlyTitle($originalFileName);
        if($fileName == '-') {
            $factory = new \RandomLib\Factory();
            $fileName = $factory->getMediumStrengthGenerator()->generateString(6, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
        }
        
        //hash file less then 10M
        if($fileSize < 1048576 * 10){
            $fileHash = hash_file('CRC32', $tmp, false);
        }
        if(false === strpos($type, 'image')) {
            $isImage = 0;
        } else {
            $isImage = 1;
        }

        $fileinfo = array(
            'title' => $originalFileName,
            'status' => 'published',
            'storageAdapter' => 'local',
            'originalName' => $originalName,
            'fileSize' => $fileSize,
            'mimeType' => $type,
            'fileExtension' => $fileExtension,
            'fileHash' => $fileHash,
            'isImage' => $isImage,
            'fileName' => $fileName . '.' . $fileExtension,
            'createdAt' => time(),
        );

        if($isImage) {
            $image = getimagesize($tmp);
            $fileinfo['imageWidth'] = $image[0];
            $fileinfo['imageHeight'] = $image[1];
        }

        $config = $this->getDI()->get('config')->filesystem;
        $adapter = new \Gaufrette\Adapter\Local($config->uploadPath);
        $filesystem = new \Gaufrette\Filesystem($adapter);

        $path = md5(time());
        $path = str_split($path, 2);
        $pathlevel = $config->uploadPathlevel;
        $pathlevel > 6 ? 6 : $pathlevel;
        $path = array_slice($path, 0, $pathlevel);
        $filePath = implode('/', $path);
        $path = $filePath . '/' . $fileName . '.' . $fileExtension;

        $fileinfo['filePath'] = $filePath;

        $upload = new Upload();
        $upload->assign($fileinfo);
        if($upload->save()) {
            if (!$filesystem->has($path)) {
                if($filesystem->write($path, file_get_contents($tmp))) {
                    unlink($tmp);
                } else {
                    throw new Exception\IOException('ERR_FILE_MOVE_TO_STORAGE_FAILED');
                }
            } else {
                throw new Exception\ResourceConflictException('ERR_FILE_UPLOAD_BY_CONFLICT_NAME');
            }
        } else {
            throw new Exception\RuntimeException('ERR_FILE_SAVE_TO_DB_FAILED');
        }
        return $upload;
    }
}

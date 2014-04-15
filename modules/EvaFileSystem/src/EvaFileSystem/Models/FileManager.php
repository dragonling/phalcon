<?php

namespace Eva\EvaFileSystem\Models;

use Eva\EvaFileSystem\Entities\Files;
use Eva\EvaUser\Models\Login as LoginModel;
use Eva\EvaEngine\Exception;

class FileManager extends Files
{
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

    public function getReadableFileSize()
    {
        $size = $this->fileSize;
        $units = array(' B', ' KB', ' MB', ' GB', ' TB');
        for ($i = 0; $size >= 1024 && $i < 4; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . $units[$i];
    }
}

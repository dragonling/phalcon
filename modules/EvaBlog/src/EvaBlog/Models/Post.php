<?php

namespace Eva\EvaBlog\Models;


use Eva\EvaBlog\Entities;
use Eva\EvaUser\Models\Login as LoginModel;
use Eva\EvaFileSystem\Models\Upload as UploadModel;
use \Phalcon\Mvc\Model\Message as Message;
use Eva\EvaEngine\Exception;

class Post extends Entities\Posts
{
    public function beforeValidationOnCreate()
    {
        $this->createdAt = time();
        if(!$this->slug) {
            $factory = new \RandomLib\Factory();
            $this->slug = $factory->getMediumStrengthGenerator()->generateString(8, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
        }
    }

    public function beforeCreate()
    {
        $user = new LoginModel();
        if($userinfo = $user->isUserLoggedIn()) {
            $this->user_id = $userinfo['id'];
            $this->username = $userinfo['username'];
        }

        if($this->getDI()->getRequest()->hasFiles()) {
            $upload = new UploadModel();
            $files = $this->getDI()->getRequest()->getUploadedFiles();
            if(!$files) {
                return;
            }
            $file = $files[0];
            $file = $upload->upload($file);
            if($file) {
                $this->image_id = $file->id;
                $this->image = $file->getFullUrl();
            }
        }
    }

    public function beforeUpdate()
    {
        $user = new LoginModel();
        if($userinfo = $user->isUserLoggedIn()) {
            $this->editor_id = $userinfo['id'];
            $this->editor_name = $userinfo['username'];
        }

        $this->updatedAt = time();

        if($this->getDI()->getRequest()->hasFiles()) {
            $upload = new UploadModel();
            $files = $this->getDI()->getRequest()->getUploadedFiles();
            if(!$files) {
                return;
            }
            $file = $files[0];
            $file = $upload->upload($file);
            if($file) {
                $this->image_id = $file->id;
                $this->image = $file->getFullUrl();
            }
        }
    }


    public function createPost(array $data)
    {
        $data['Categories'] = isset($data['Categories']) ? $data['Categories'] : array(); 
        $textData = $data['Text'];
        $tagData = $data['Tags'];
        $categoryData = $data['Categories'];
        unset($data['Text']);
        unset($data['Tags']);
        unset($data['Categories']);

        $text = new Text();
        $text->assign($textData);
        $this->Text = $text;

        $tags = array();
        if($tagData) {
            $tagArray = explode(',', $tagData);
            foreach($tagArray as $tagName) {
                $tag = new Tag();
                $tag->tagName = $tagName;
                $tags[] = $tag;
            }
            if($tags) {
                $this->Tags = $tags;
            }
        }

        $categories = array();
        if($categoryData) {
            foreach($categoryData as $categoryId) {
                $category = Category::findFirst($categoryId);
                if($category) {
                    $categories[] = $category;
                }
            }
            $this->Categories = $categories;
        }



        $this->assign($data);
        $this->save();
    }

    public function updatePost($data)
    {
        $data['Categories'] = isset($data['Categories']) ? $data['Categories'] : array(); 
        $textData = $data['Text'];
        $tagData = $data['Tags'];
        $categoryData = $data['Categories'];
        unset($data['Text']);
        unset($data['Tags']);
        unset($data['Categories']);

        $text = new Text();
        $text->assign($textData);
        $this->Text = $text;

        $tags = array();
        //remove old relations
        if($this->TagsPosts) {
            $this->TagsPosts->delete();
        }
        if($tagData) {
            $tagArray = explode(',', $tagData);
            foreach($tagArray as $tagName) {
                $tag = new Tag();
                $tag->tagName = $tagName;
                $tags[] = $tag;
            }
            if($tags) {
                $this->Tags = $tags;
            }
        }

        //remove old relations
        if($this->CategoriesPosts) {
            $this->CategoriesPosts->delete();
        }
        $categories = array();
        if($categoryData) {
            foreach($categoryData as $categoryId) {
                $category = Category::findFirst($categoryId);
                if($category) {
                    $categories[] = $category;
                }
            }
            $this->Categories = $categories;
        }

        $this->assign($data);
        $this->save();
    
    }

    public function remotePost($id)
    {
        $this->id = $id;
        //remove old relations
        if($this->TagsPosts) {
            $this->TagsPosts->delete();
        }
        //remove old relations
        if($this->CategoriesPosts) {
            $this->CategoriesPosts->delete();
        }
        $this->Text->delete();
        $this->delete();
    }

    public function getTagString()
    {
        if(!$this->Tags) {
            return '';
        }

        $tags = $this->Tags;
        $tagArray = array();
        foreach($tags as $tag) {
            $tagArray[] = $tag->tagName;
        }
        return implode(',', $tagArray);
    }
}

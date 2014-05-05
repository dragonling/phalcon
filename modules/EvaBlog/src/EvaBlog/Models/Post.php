<?php

namespace Eva\EvaBlog\Models;


use Eva\EvaBlog\Entities;
use Eva\EvaUser\Models\Login as LoginModel;
use Eva\EvaFileSystem\Models\Upload as UploadModel;
use \Phalcon\Mvc\Model\Message as Message;
use Eva\EvaEngine\Exception;

class Post extends Entities\Posts
{
    public static $defaultDump = array(
        'id',
        'title',
        'sourceCode',
        'createdAt',
        'summary',
        'summaryHtml' => 'getSummaryHtml',
        'commentStatus',
        'sourceName',
        'sourceUrl',
        'url' => 'getUrl',
        'imageUrl' => 'getImageUrl',
        'content' => 'getContentHtml',
        'Text' => array(
            'content',
        ),
        'Tags' => array(
            'id',
            'tagName',
        ),
        'Categories' => array(
            'id',
            'categoryName',
        ),
        'User' => array(
            'id',
            'username',
        ),
    );


    public function beforeValidationOnCreate()
    {
        $this->createdAt = $this->createdAt ? $this->createdAt : time();
        if(!$this->slug) {
            $this->slug = \Phalcon\Text::random(\Phalcon\Text::RANDOM_ALNUM, 8);
        }
    }

    public function beforeCreate()
    {
        $user = new LoginModel();
        if($userinfo = $user->isUserLoggedIn()) {
            $this->user_id = $this->user_id ? $this->user_id : $userinfo['id'];
            $this->username = $this->username ? $this->username : $userinfo['username'];
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
                $this->image = $file->getLocalUrl();
            }
        }
    }

    public function findPosts(array $query = array())
    {
        $itemQuery = $this->query();

        $orderMapping = array(
            'id' => 'id ASC',
            '-id' => 'id DESC',
            'created_at' => 'createdAt ASC',
            '-created_at' => 'createdAt DESC',
        );

        if(!empty($query['columns'])) {
            $itemQuery->columns($query['columns']);
        }

        if(!empty($query['q'])) {
            $itemQuery->andWhere('title LIKE :q:', array('q' => "%{$query['q']}%"));
        }

        if(!empty($query['status'])) {
            $itemQuery->andWhere('status = :status:', array('status' => $query['status']));
        }

        if(!empty($query['uid'])) {
            $itemQuery->andWhere('user_id = :uid:', array('uid' => $query['uid']));
        }

        if(!empty($query['cid'])) {
            $itemQuery->join('Eva\EvaBlog\Entities\CategoriesPosts', 'id = r.post_id', 'r')
            ->andWhere('r.category_id = :cid:', array('cid' => $query['cid']));
        }

        $order = 'createdAt DESC';
        if(!empty($query['order'])) {
            $order = empty($orderMapping[$query['order']]) ? 'createdAt DESC' : $orderMapping[$query['order']];
        }
        $itemQuery->orderBy($order);

        $posts = $itemQuery->execute();
        return $posts;
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
        if(!$this->save()) {
            throw new Exception\RuntimeException('Create post failed');
        }
        return $this;
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
        if(!$this->save()) {
            throw new Exception\RuntimeException('Update post failed');
        }
        return $this;
    }

    public function removePost($id)
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

    public function getSummaryHtml()
    {
        if(!$this->summary) {
            return '';
        }

        if ($this->sourceCode == 'markdown') {
            $parsedown = new \Parsedown();
            return $parsedown->text($this->summary);
        } else {
            return $this->summary;
        }
    }

    public function getContentHtml()
    {
        if(!$this->Text) {
            return '';
        }
        if($this->sourceCode == 'markdown') {
            $parsedown = new \Parsedown();
            return $parsedown->text($this->Text->content);
        }
        return $this->Text->content;
    }

    public function getUrl()
    {
        $postUrl = $this->getDI()->get('config')->baseUri;
        $postPath = $this->getDI()->get('config')->blog->postPath;
        return $postUrl . sprintf($postPath, $this->slug);
    }

    public function getImageUrl()
    {
        if(!$this->image) {
            return '';
        }

        if($this->image) {
            if(
                \Phalcon\Text::startsWith($this->image, 'http://', false) ||
                \Phalcon\Text::startsWith($this->image, 'https://', false)
            ) {
                return $this->image;
            }
        }

        $staticUri = $this->getDI()->get('config')->filesystem->staticUri;
        $staticPath = $this->getDI()->get('config')->filesystem->staticPath;
        return $staticUri . $staticPath . $this->image;
    }
}

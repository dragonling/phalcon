<?php

namespace Eva\EvaBlog\Models;

use Eva\EvaBlog\Entities;
use Eva\EvaUser\Models\Login as LoginModel;
use Eva\EvaFileSystem\Models\Upload as UploadModel;
use Eva\EvaEngine\Exception;
use Eva\EvaEngine\Mvc\Model\Validator\Uniqueness;


class Post extends Entities\Posts
{
    public static $defaultDump = array(
        'id',
        'title',
        'codeType',
        'createdAt',
        'summary',
        'summaryHtml' => 'getSummaryHtml',
        'commentStatus',
        'sourceName',
        'sourceUrl',
        'url' => 'getUrl',
        'imageUrl' => 'getImageUrl',
        'content' => 'getContentHtml',
        'text' => array(
            'content',
        ),
        'tags' => array(
            'id',
            'tagName',
        ),
        'categories' => array(
            'id',
            'categoryName',
        ),
        'user' => array(
            'id',
            'username',
        ),
    );

    public function beforeValidationOnCreate()
    {
        $this->createdAt = $this->createdAt ? $this->createdAt : time();
        if (!$this->slug) {
            $this->slug = \Phalcon\Text::random(\Phalcon\Text::RANDOM_ALNUM, 8);
        }

        $this->validate(new Uniqueness(array(
            'field' => 'slug'
        )));
    }

    public function beforeValidationOnUpdate()
    {
        $this->validate(new Uniqueness(array(
            'field' => 'slug',
            'conditions' => 'id != :id:',
            'bind' => array(
                'id' => $this->id
            ),
        )));
    }


    public function beforeCreate()
    {
        $user = new LoginModel();
        if ($userinfo = $user->isUserLoggedIn()) {
            $this->userId = $this->userId ? $this->userId : $userinfo['id'];
            $this->username = $this->username ? $this->username : $userinfo['username'];
        }
    }

    public function beforeUpdate()
    {
        $user = new LoginModel();
        if ($userinfo = $user->isUserLoggedIn()) {
            $this->editorId = $userinfo['id'];
            $this->editorName = $userinfo['username'];
        }

        $this->updatedAt = time();
    }

    public function beforeSave()
    {
        if ($this->getDI()->getRequest()->hasFiles()) {
            $upload = new UploadModel();
            $files = $this->getDI()->getRequest()->getUploadedFiles();
            if (!$files) {
                return;
            }
            $file = $files[0];
            $file = $upload->upload($file);
            if ($file) {
                $this->imageId = $file->id;
                $this->image = $file->getLocalUrl();
            }
        }
    }

    public function validation()
    {
        if ($this->validationHasFailed() == true) {
            return false;
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

        if (!empty($query['columns'])) {
            $itemQuery->columns($query['columns']);
        }

        if (!empty($query['q'])) {
            $itemQuery->andWhere('title LIKE :q:', array('q' => "%{$query['q']}%"));
        }

        if (!empty($query['status'])) {
            $itemQuery->andWhere('status = :status:', array('status' => $query['status']));
        }

        if (!empty($query['uid'])) {
            $itemQuery->andWhere('userId = :uid:', array('uid' => $query['uid']));
        }

        if (!empty($query['cid'])) {
            $itemQuery->join('Eva\EvaBlog\Entities\CategoriesPosts', 'id = r.postId', 'r')
            ->andWhere('r.categoryId = :cid:', array('cid' => $query['cid']));
        }

        $order = 'createdAt DESC';
        if (!empty($query['order'])) {
            $order = empty($orderMapping[$query['order']]) ? 'createdAt DESC' : $orderMapping[$query['order']];
        }
        $itemQuery->orderBy($order);

        $posts = $itemQuery->execute();

        return $posts;
    }

    public function createPost(array $data)
    {
        $data['categories'] = isset($data['categories']) ? $data['categories'] : array();
        $textData = isset($data['text']) ? $data['text'] : array();
        $tagData = isset($data['tags']) ? $data['tags'] : array();
        $categoryData = $data['categories'];
        unset($data['tags']);
        unset($data['text']);
        unset($data['categories']);

        $text = new Text();
        $text->assign($textData);
        $this->text = $text;

        $tags = array();
        if ($tagData) {
            $tagArray = explode(',', $tagData);
            foreach ($tagArray as $tagName) {
                $tag = new Tag();
                $tag->tagName = $tagName;
                $tags[] = $tag;
            }
            if ($tags) {
                $this->tags = $tags;
            }
        }

        $categories = array();
        if ($categoryData) {
            foreach ($categoryData as $categoryId) {
                $category = Category::findFirst($categoryId);
                if ($category) {
                    $categories[] = $category;
                }
            }
            $this->categories = $categories;
        }

        $this->assign($data);
        if (!$this->save()) {
            throw new Exception\RuntimeException('Create post failed');
        }

        return $this;
    }

    public function updatePost($data)
    {
        $data['categories'] = isset($data['categories']) ? $data['categories'] : array();
        $textData = $data['text'];
        $tagData = $data['tags'];
        $categoryData = $data['categories'];
        unset($data['text']);
        unset($data['tags']);
        unset($data['categories']);

        $text = new Text();
        $text->assign($textData);
        $this->text = $text;

        $tags = array();
        //remove old relations
        if ($this->tagsPosts) {
            $this->tagsPosts->delete();
        }
        if ($tagData) {
            $tagArray = explode(',', $tagData);
            foreach ($tagArray as $tagName) {
                $tag = new Tag();
                $tag->tagName = $tagName;
                $tags[] = $tag;
            }
            if ($tags) {
                $this->tags = $tags;
            }
        }

        //remove old relations
        if ($this->categoriesPosts) {
            $this->categoriesPosts->delete();
        }
        $categories = array();
        if ($categoryData) {
            foreach ($categoryData as $categoryId) {
                $category = Category::findFirst($categoryId);
                if ($category) {
                    $categories[] = $category;
                }
            }
            $this->categories = $categories;
        }

        $this->assign($data);
        if (!$this->save()) {
            throw new Exception\RuntimeException('Update post failed');
        }

        return $this;
    }

    public function removePost($id)
    {
        $this->id = $id;
        //remove old relations
        if ($this->tagsPosts) {
            $this->tagsPosts->delete();
        }
        //remove old relations
        if ($this->categoriesPosts) {
            $this->categoriesPosts->delete();
        }
        $this->text->delete();
        $this->delete();
    }

    public function getTagString()
    {
        if (!$this->tags) {
            return '';
        }

        $tags = $this->tags;
        $tagArray = array();
        foreach ($tags as $tag) {
            $tagArray[] = $tag->tagName;
        }

        return implode(',', $tagArray);
    }

    public function getSummaryHtml()
    {
        if (!$this->summary) {
            return '';
        }

        if ($this->codeType == 'markdown') {
            $parsedown = new \Parsedown();

            return $parsedown->text($this->summary);
        } else {
            return $this->summary;
        }
    }

    public function getContentHtml()
    {
        if (!$this->text) {
            return '';
        }
        if ($this->codeType == 'markdown') {
            $parsedown = new \Parsedown();

            return $parsedown->text($this->text->content);
        }

        return $this->text->content;
    }

    public function getUrl()
    {
        $postUrl = $this->getDI()->get('config')->baseUri;
        $postPath = $this->getDI()->get('config')->blog->postPath;

        return $postUrl . sprintf($postPath, $this->slug);
    }

    public function getImageUrl()
    {
        if (!$this->image) {
            return '';
        }

        if ($this->image) {
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

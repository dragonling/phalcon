<?php

namespace Eva\EvaBlog\Controllers;

use Eva\EvaBlog\Models;

class PostController extends ControllerBase
{

    /**
     * Index action
     */
    public function indexAction()
    {
        if(!$this->request->isPost()) {
            return;
        }

        $blog = new Models\Post();
        $blog->assign(array(
            'title' => $this->request->getPost('title'),
            'status' => 'published',
            'visibility' => 'public',
            'codeType' => 'markdown',
            'language' => 'zh_CN',
            'urlName' => 'test',
            'createTime' => time(),
        ));
        if(!$blog->save()) {
            p($blog->getMessages());
            exit;
        }
    }

    public function listAction()
    {
        $currentPage = $this->request->getQuery('page', 'int'); // GET
        $limit = $this->request->getQuery('limit', 'int');
        $order = $this->request->getQuery('order', 'int');
        $limit = $limit > 50 ? 50 : $limit;
        $limit = $limit < 10 ? 10 : $limit;

        $posts = Models\Post::find(array(
            'order' => 'id DESC',
            //'columns' => 'id, title, status, createTime, User'
        ));
        $paginator = new \Eva\EvaEngine\Paginator(array(
            "data" => $posts,
            "limit"=> $limit,
            "page" => $currentPage
        ));
        $paginator->setQuery(array(
            'order' => '',
            'limit' => $limit,
            'q' => '',
        ));
        $pager = $paginator->getPaginate();
        $this->view->setVar('pager', $pager);
    }

    public function createAction()
    {
        Models\Post::findFirst();
        $postForm = new \Eva\EvaBlog\Forms\PostForm();
        $postForm->setModel(new Models\Post());
        $this->view->setVar('postForm', $postForm);

        $textForm = new \Eva\EvaBlog\Forms\TextForm();
        $textForm->setModel(new Models\Text());
        $textForm->setPrefix('Text');
        $this->view->setVar('textForm', $textForm);

        if(!$this->request->isPost()){
            return false;
        }
        $data = $this->request->getPost();
        $post = new Models\Post();
        $post->assign($data);
        /*
        $text = new Models\Text();
        $text->assign($data['Text']);
        */
        if(!$post->create()) {
            p($post->getMessages());
            exit;
        }
    }
}

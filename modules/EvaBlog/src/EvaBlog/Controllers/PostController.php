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
        $textData = $data['Text'];
        //unset($data['Text']);
        $post = new Models\Post();
        $text = new Models\Text();
        $post->Text = $text;
        $post->assign($data);
        $text->assign($textData);
        if(!$post->save()) {
            p($post->getMessages());
            exit;
        }
    }

    public function editAction()
    {
        $this->view->setTemplateAfter('admin');
        $this->view->pick('post/create');

        $post = new Models\Post();

        $postinfo = $post->findFirst($this->dispatcher->getParam('id'));

        $postForm = new \Eva\EvaBlog\Forms\PostForm();
        $postForm->setModel($postinfo);
        $this->view->setVar('postForm', $postForm);

        $textForm = new \Eva\EvaBlog\Forms\TextForm();
        $textForm->setModel($postinfo->Text);
        $textForm->setPrefix('Text');
        $this->view->setVar('textForm', $textForm);
    
    }
}

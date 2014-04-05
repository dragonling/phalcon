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

    public function newAction()
    {
        $form = new \Eva\EvaEngine\Form();
        $form->setModel(Models\Post::findFirst());
        $form->add(new \Phalcon\Forms\Element\Text("title"));
        $this->view->setVar('form', $form);
        /*
        array(
            'username' => 'abc',   //user entity
            'Profile' => array(   //profile entity
                'firstName' => 1
            )
        )
        */
    }
}

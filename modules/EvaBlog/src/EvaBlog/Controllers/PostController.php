<?php

namespace Eva\EvaBlog\Controllers;

use Eva\EvaBlog\Models;
use Eva\EvaBlog\Models\Post;
use Eva\EvaBlog\Forms;


class PostController extends \Eva\EvaEngine\Mvc\Controller\ControllerBase
{
    public function listAction()
    {
        $limit = $this->dispatcher->getParam('limit');
        $limit = $limit ? $limit : 25;
        $query = array(
            'q' => $this->dispatcher->getParam('q'),
            'status' => $this->dispatcher->getParam('status'),
            'uid' => $this->dispatcher->getParam('uid'),
            'cid' => $this->dispatcher->getParam('cid'),
            'username' => $this->dispatcher->getParam('username'),
            'order' => $this->dispatcher->getParam('order'),
            'limit' => $limit,
            'page' => $this->dispatcher->getParam('page'),
            'order' => $this->dispatcher->getParam('order'),
        );
        $post = new Models\Post();
        $posts = $post->findPosts($query);
        $paginator = new \Eva\EvaEngine\Paginator(array(
            "data" => $posts,
            "limit"=> $query['limit'],
            "page" => $query['page']
        ));
        $paginator->setQuery($query);
        $pager = $paginator->getPaginate();
        return $pager;
    }
}

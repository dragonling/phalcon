<?php

namespace WscnGold\Controllers;

use Eva\EvaBlog\Models\Post;
use Eva\EvaEngine\Exception;

class IndexController extends ControllerBase
{
    public function indexAction()
    {
    }

    public function tutorialAction()
    {
        $id = $this->dispatcher->getParam('id');
        $id = $id ? $id : 'huangjingainian';
        $post = Post::findFirst(array(
            'conditions' => "slug = :slug:",
            'bind' => array(
                'slug' => $id,
            ),
        ));
        if(!$post) {
            throw new Exception\ResourceNotFoundException('Request post not found');
        }
        $this->view->setVar('post', $post);
    }

    public function articleAction()
    {
    }

    public function searchAction()
    {
    }

}

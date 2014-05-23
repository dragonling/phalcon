<?php

namespace WscnGold\Controllers;

use WscnGold\Models;
use Eva\EvaBlog\Models\Post;
use Eva\EvaEngine\Exception;

class PostController extends ControllerBase
{
    public function articleAction()
    {
        $id = $this->dispatcher->getParam('id');
        if (is_numeric($id)) {
            $post = Post::findFirst($id);
        } else {
            $post = Post::findFirstBySlug($id);
        }
        if(!$post || $post->status != 'published') {
            throw new Exception\ResourceNotFoundException('Request post not found');
        }
        $this->view->setVar('item', $post);
    }
}

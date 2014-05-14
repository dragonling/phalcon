<?php

namespace WscnGold\Controllers;

use WscnGold\Models;
use Eva\EvaBlog\Models\Post;

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
        $this->view->setVar('item', $post);
    }
}

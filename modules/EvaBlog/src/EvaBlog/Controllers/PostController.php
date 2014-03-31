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

    public function testAction()
    {
    }
}

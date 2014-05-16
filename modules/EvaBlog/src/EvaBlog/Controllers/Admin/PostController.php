<?php

namespace Eva\EvaBlog\Controllers\Admin;

use Eva\EvaBlog\Models;
use Eva\EvaBlog\Models\Post;
use Eva\EvaBlog\Forms;
use Eva\EvaEngine\Exception;

class PostController extends ControllerBase
{
    /**
    * Index action
    */
    public function indexAction()
    {
        $limit = $this->request->getQuery('limit', 'int', 25);
        $limit = $limit > 100 ? 100 : $limit;
        $limit = $limit < 10 ? 10 : $limit;
        $orderMapping = array(
            'id' => 'id ASC',
            '-id' => 'id DESC',
            'created_at' => 'createdAt ASC',
            '-created_at' => 'createdAt DESC',
        );
        $order = $this->request->getQuery('order', 'string', '-created_at');
        $query = array(
            'q' => $this->request->getQuery('q', 'string'),
            'status' => $this->request->getQuery('status', 'string'),
            'uid' => $this->request->getQuery('uid', 'int'),
            'cid' => $this->request->getQuery('cid', 'int'),
            'username' => $this->request->getQuery('username', 'string'),
            'order' => $order,
            'limit' => $limit,
            'page' => $this->request->getQuery('page', 'int', 1),
        );

        $form = new Forms\FilterForm();
        $form->setValues($this->request->getQuery());

        $this->view->setVar('form', $form);
        $post = new Models\Post();
        $posts = $post->findPosts($query);
        $paginator = new \Eva\EvaEngine\Paginator(array(
            "data" => $posts,
            "limit"=> $limit,
            "page" => $query['page']
        ));
        $paginator->setQuery($query);
        $pager = $paginator->getPaginate();
        $this->view->setVar('pager', $pager);

        return $paginator;
    }

    public function createAction()
    {
        $form = new Forms\PostForm();
        $form->setModel(new Models\Post());
        $form->addForm('Text', 'Eva\EvaBlog\Forms\TextForm');
        $this->view->setVar('form', $form);
        $this->view->setVar('item', $post);

        if (!$this->request->isPost()) {
            return false;
        }

        $data = $this->request->getPost();
        if (!$form->isFullValid($data)) {
            return $this->displayInvalidMessages($form);
        }

        try {
            $form->save('createPost');
        } catch (\Exception $e) {
            return $this->displayException($e, $form->getModel()->getMessages());
        }
        $this->flashSession->success('SUCCESS_POST_CREATED');

        return $this->redirectHandler('/admin/post/edit/' . $form->getModel()->id);
    }

    public function editAction()
    {
        $this->view->changeRender('admin/post/create');
        $post = Models\Post::findFirst($this->dispatcher->getParam('id'));
        if (!$post) {
            throw new Exception\ResourceNotFoundException('ERR_BLOG_POST_NOT_FOUND');
        }

        $form = new Forms\PostForm();
        $form->setModel($post);
        $form->addForm('Text', 'Eva\EvaBlog\Forms\TextForm');
        $this->view->setVar('form', $form);
        $this->view->setVar('item', $post);

        if (!$this->request->isPost()) {
            return false;
        }
        $data = $this->request->getPost();

        if (!$form->isFullValid($data)) {
            return $this->displayInvalidMessages($form);
        }

        try {
            $form->save('updatePost');
        } catch (\Exception $e) {
            return $this->displayException($e, $form->getModel()->getMessages());
        }
        $this->flashSession->success('SUCCESS_POST_UPDATED');

        return $this->redirectHandler('/admin/post/edit/' . $post->id);
    }
}

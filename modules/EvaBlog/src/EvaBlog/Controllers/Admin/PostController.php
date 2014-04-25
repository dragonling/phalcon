<?php

namespace Eva\EvaBlog\Controllers\Admin;

use Eva\EvaBlog\Models;
use Eva\EvaBlog\Models\Post;
use Eva\EvaBlog\Forms;


class PostController extends ControllerBase
{

    /**
     * Index action
     */
    public function indexAction()
    {
        $limit = $this->request->getQuery('limit', 'int', 25);
        $limit = $limit > 50 ? 50 : $limit;
        $limit = $limit < 10 ? 10 : $limit;
        $query = array(
            'page' => $this->request->getQuery('page', 'int', 1),
            'limit' => $limit,
            'order' => $this->request->getQuery('order', array('alphanum', 'lower'), 'id DESC'),
            'uid' => $this->request->getQuery('uid', 'int'),
        );

        $form = new Forms\FilterForm();
        $form->setValues($this->request->getQuery());
        $this->view->setVar('form', $form);
        $post = new Models\Post();
        $itemQuery = Models\Post::query();
        $posts = $itemQuery->execute();

        /*
        $posts = $itemQuery->join('Eva\EvaBlog\Entities\CategoriesPosts', 'id = r.post_id', 'r')
        ->where('category_id = :id:', array('id' => 5))
        ->execute();
        */
        $paginator = new \Eva\EvaEngine\Paginator(array(
            "data" => $posts,
            "limit"=> $limit,
            "page" => $query['page']
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
        $post = new Models\Post();
        $postForm = new \Eva\EvaBlog\Forms\PostForm();
        $postForm->setModel($post);
        $this->view->setVar('postForm', $postForm);
        $this->view->setVar('item', $post);

        $textForm = new \Eva\EvaBlog\Forms\TextForm();
        $textForm->setModel(new Models\Text());
        $textForm->setPrefix('Text');
        $this->view->setVar('textForm', $textForm);

        if(!$this->request->isPost()){
            return false;
        }
        $data = $this->request->getPost();

        try {
            $post->createPost($data);
        } catch(\Exception $e) {
            return $this->errorHandler($e, $post->getMessages());
        }
        $this->flashSession->success('SUCCESS_POST_CREATED');
        return $this->redirectHandler('/admin/post/edit/' . $post->id);
    }

    public function editAction()
    {
        $this->view->changeRender('admin/post/create');

        $post = Models\Post::findFirst($this->dispatcher->getParam('id'));
        $postForm = new \Eva\EvaBlog\Forms\PostForm();
        $postForm->setModel($post ? $post : new Models\Post());
        $this->view->setVar('postForm', $postForm);

        $textForm = new \Eva\EvaBlog\Forms\TextForm();
        $textForm->setModel($post->Text ? $post->Text : new Models\Text());
        $textForm->setPrefix('Text');
        $this->view->setVar('textForm', $textForm);
        $this->view->setVar('item', $post);

        if(!$this->request->isPost()){
            return false;
        }
        $data = $this->request->getPost();

        try {
            $post->updatePost($data);
        } catch(\Exception $e) {
            return $this->errorHandler($e, $post->getMessages());
        }
        $this->flashSession->success('SUCCESS_POST_UPDATED');
        return $this->redirectHandler('/admin/post/edit/' . $post->id);

    }

    public function deleteAction()
    {
        if(!$this->request->isDelete()){
            $this->response->setStatusCode('405', 'Method Not Allowed');
            $this->response->setContentType('application/json', 'utf-8');
            return $this->response->setJsonContent(array(
                'errors' => array(
                    array(
                        'code' => 405,
                        'message' => 'ERR_POST_REQUEST_METHOD_NOT_ALLOW'
                    )
                ),
            ));
        }

        $id = $this->dispatcher->getParam('id');
        $post =  new Models\Post();
        try {
            $post->removePost($id);
        } catch(\Exception $e) {
            return $this->jsonErrorHandler($e, $post->getMessages());
        }

        $this->response->setContentType('application/json', 'utf-8');
        return $this->response->setJsonContent($post);
    }

    public function statusAction()
    {
        if(!$this->request->isPut()){
            $this->response->setStatusCode('405', 'Method Not Allowed');
            $this->response->setContentType('application/json', 'utf-8');
            return $this->response->setJsonContent(array(
                'errors' => array(
                    array(
                        'code' => 405,
                        'message' => 'ERR_POST_REQUEST_METHOD_NOT_ALLOW'
                    )
                ),
            ));
        }

        $id = $this->dispatcher->getParam('id');
        $post =  Models\Post::findFirst($id); 
        try {
            $post->status = $this->request->getPut('status');
            $post->save();
        } catch(\Exception $e) {
            return $this->jsonErrorHandler($e, $post->getMessages());
        }

        $this->response->setContentType('application/json', 'utf-8');
        return $this->response->setJsonContent($post);
    }

    public function batchAction()
    {
        if(!$this->request->isPut()){
            $this->response->setStatusCode('405', 'Method Not Allowed');
            $this->response->setContentType('application/json', 'utf-8');
            return $this->response->setJsonContent(array(
                'errors' => array(
                    array(
                        'code' => 405,
                        'message' => 'ERR_POST_REQUEST_METHOD_NOT_ALLOW'
                    )
                ),
            ));
        }

        $idArray = $this->request->getPut('id');
        if(!is_array($idArray) || count($idArray) < 1) {
            return false;
        }
        $status = $this->request->getPut('status');
        $posts = array();

        try {
            foreach($idArray as $id) {
                $post =  Models\Post::findFirst($id);
                if($post) {
                    $post->status = $status;
                    $post->save();
                    $posts[] = $post;
                }
            }

        } catch(\Exception $e) {
            return $this->jsonErrorHandler($e, $post->getMessages());
        }

        $this->response->setContentType('application/json', 'utf-8');
        return $this->response->setJsonContent($posts);
    
    }
}

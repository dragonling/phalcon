<?php

namespace Eva\EvaBlog\Controllers\Admin;

use Eva\EvaBlog\Models;
use Eva\EvaEngine\Mvc\Controller\JsonControllerInterface;
use Eva\EvaEngine\Exception;

class ProcessController extends ControllerBase implements JsonControllerInterface
{
    public function deleteAction()
    {
        if (!$this->request->isDelete()) {
            return $this->displayJsonErrorResponse(405, 'ERR_REQUEST_METHOD_NOT_ALLOW');
        }

        $id = $this->dispatcher->getParam('id');
        $post =  new Models\Post();
        try {
            $post->removePost($id);
        } catch (\Exception $e) {
            return $this->displayExceptionForJson($e, $post->getMessages());
        }

        return $this->response->setJsonContent($post);
    }

    public function statusAction()
    {
        if (!$this->request->isPut()) {
            return $this->displayJsonErrorResponse(405, 'ERR_REQUEST_METHOD_NOT_ALLOW');
        }

        $id = $this->dispatcher->getParam('id');
        $post =  Models\Post::findFirst($id);
        try {
            $post->status = $this->request->getPut('status');
            $post->save();
        } catch (\Exception $e) {
            return $this->displayExceptionForJson($e, $post->getMessages());
        }

        return $this->response->setJsonContent($post);
    }

    public function batchAction()
    {
        if (!$this->request->isPut()) {
            return $this->displayJsonErrorResponse(405, 'ERR_REQUEST_METHOD_NOT_ALLOW');
        }

        $idArray = $this->request->getPut('id');
        if (!is_array($idArray) || count($idArray) < 1) {
            return $this->displayJsonErrorResponse(401, 'ERR_REQUEST_PARAMS_INCORRECT');
        }

        $status = $this->request->getPut('status');
        $posts = array();

        try {
            foreach ($idArray as $id) {
                $post =  Models\Post::findFirst($id);
                if ($post) {
                    $post->status = $status;
                    $post->save();
                    $posts[] = $post;
                }
            }
        } catch (\Exception $e) {
            return $this->displayExceptionForJson($e, $post->getMessages());
        }

        return $this->response->setJsonContent($posts);
    }


    public function slugAction()
    {
        $slug = $this->request->get('slug');
        $exclude = $this->request->get('exclude');
        if ($slug) {
            $conditions = array(
                "columns" => array('id'),
                "conditions" => 'slug = :slug:',
                "bind" => array(
                    'slug' => $slug
                )
            );
            if($exclude) {
                $conditions['conditions'] .= ' AND id != :id:';
                $conditions['bind']['id'] = $exclude;
            }
            $post = Models\Post::findFirst($conditions);
        } else {
            $post = array();
        }

        if ($post) {
            $this->response->setStatusCode('409', 'Post Already Exists');
        }

        return $this->response->setJsonContent(array(
            'exist' => $post ? true : false,
            'id' => $post ? $post->id : 0,
        ));
    }

}

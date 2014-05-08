<?php

namespace Eva\EvaUser\Controllers\Admin;

use Eva\EvaUser\Models;
use Eva\EvaEngine\Mvc\Controller\JsonControllerInterface;

class ProcessController extends ControllerBase implements JsonControllerInterface
{
    public function suggestionsAction()
    {
        $query = $this->request->get('query');
        if($query) {
            $users = Models\User::find(array(
                "columns" => array('id', 'username', 'status'),
                "conditions" => "username like '%$query%'",
                "limit" => 10,
            ));
            $users = $users ? $users->toArray() : array();
        } else {
            $users = array();
        }

        return $this->response->setJsonContent($users);
    }

    public function statusAction()
    {
        if(!$this->request->isPut()){
            $this->response->setStatusCode('405', 'Method Not Allowed');
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
            return $this->displayExceptionForJson($e, $post->getMessages());
        }

        $this->response->setContentType('application/json', 'utf-8');
        return $this->response->setJsonContent($post);
    }
}

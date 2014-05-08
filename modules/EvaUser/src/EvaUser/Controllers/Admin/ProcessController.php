<?php

namespace Eva\EvaUser\Controllers\Admin;

use Eva\EvaUser\Models;
use Eva\EvaEngine\Mvc\Controller\JsonControllerInterface;
use Eva\EvaEngine\Exception;

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
            throw new Exception\ResourceNotFoundException('ERR_USER_REQUEST_USER_NOT_FOUND');
            return $this->displayJsonErrorResponse(405, 'ERR_REQUEST_METHOD_NOT_ALLOW');
        }

        $id = $this->dispatcher->getParam('id');
        $user =  Models\User::findFirst($id); 
        if(!$user) {
            return $this->displayJsonErrorResponse(404, 'ERR_USER_NOT_FOUND');
        }

        try {
            $user->status = $this->request->getPut('status');
            $user->save();
        } catch(\Exception $e) {
            return $this->displayExceptionForJson($e, $user->getMessages());
        }
        return $this->response->setJsonContent($user);
    }

    public function deleteAction()
    {
        if(!$this->request->isDelete()){
            return $this->displayJsonErrorResponse(405, 'ERR_REQUEST_METHOD_NOT_ALLOW');
        }

        $id = $this->dispatcher->getParam('id');
        $user =  Models\User::findFirst($id);
        try {
            if($user) {
                $user->delete();
            }
        } catch(\Exception $e) {
            return $this->displayExceptionForJson($e, $user>getMessages());
        }
        return $this->response->setJsonContent($user);
    }

}

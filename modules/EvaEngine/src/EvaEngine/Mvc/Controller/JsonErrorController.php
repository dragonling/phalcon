<?php

namespace Eva\EvaEngine\Mvc\Controller;

use Phalcon\Mvc\Controller;
use Eva\EvaEngine\Exception;

class JsonErrorController extends Controller
{
    public function indexAction()
    {
        $error = $this->dispatcher->getParam('error');
        $this->response->setContentType('application/json', 'utf-8');
        $this->response->setJsonContent(array(
            'errors' => array(
                array(
                    'code' => $error->getCode(),
                    'message' => $error->getMessage()
                )
            ),
        ));
        $callback = $this->request->getQuery('callback');
        if($callback) {
            $this->response->setContent($callback . '(' . $this->response->getContent() . ')');
        }
    }
}

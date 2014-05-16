<?php

namespace Eva\EvaFileSystem\Controllers\Admin;

use Eva\EvaFileSystem\Models;

class MediaController extends ControllerBase
{
    public function uploadAction()
    {
    }

    /**
     * Index action
     */
     public function indexAction()
     {
        $currentPage = $this->request->getQuery('page', 'int'); // GET
        $limit = $this->request->getQuery('limit', 'int');
        $limit = $limit > 50 ? 50 : $limit;
        $limit = $limit < 10 ? 10 : $limit;

        $posts = Models\FileManager::find(array(
            'order' => 'id DESC',
        ));
        $paginator = new \Eva\EvaEngine\Paginator(array(
            "data" => $posts,
            "limit"=> $limit,
            "page" => $currentPage
        ));
        $paginator->setQuery(array(
            'limit' => $limit,
        ));
        $pager = $paginator->getPaginate();
        $this->view->setVar('pager', $pager);
    }

}

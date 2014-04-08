<?php

namespace Eva\EvaBlog\Controllers;

use Eva\EvaBlog\Models;

class CategoryController extends ControllerBase
{

    /**
     * Index action
     */
    public function indexAction()
    {
        $currentPage = $this->request->getQuery('page', 'int'); // GET
        $limit = $this->request->getQuery('limit', 'int');
        $order = $this->request->getQuery('order', 'int');
        $limit = $limit > 50 ? 50 : $limit;
        $limit = $limit < 10 ? 10 : $limit;

        $items = Models\Category::find(array(
            'order' => 'id DESC',
        ));
        $paginator = new \Eva\EvaEngine\Paginator(array(
            "data" => $items,
            "limit"=> $limit,
            "page" => $currentPage
        ));
        $pager = $paginator->getPaginate();
        $this->view->setVar('pager', $pager);
    }

    public function listAction()
    {

    }

    public function createAction()
    {
        $form = new \Eva\EvaBlog\Forms\CategoryForm();
        $category = new Models\Category();
        $form->setModel($category);
        $this->view->setVar('form', $form);

        if(!$this->request->isPost()){
            return false;
        }

        $form->bind($this->request->getPost(), $category);
        if(!$form->isValid()){
            p($form->getMessages());
            exit;
        }
        $category = $form->getEntity();
        $category->save();
    }

    public function editAction()
    {
    
    }
}

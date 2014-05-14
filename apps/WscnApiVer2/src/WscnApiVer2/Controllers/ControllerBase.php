<?php

namespace WscnApiVer2\Controllers;

class ControllerBase extends \Eva\EvaEngine\Mvc\Controller\ControllerBase
{
    public function afterExecuteRoute($dispatcher)
    {
        $this->response->setContentType('application/json', 'utf-8');
        $callback = $this->request->getQuery('callback');
        if ($callback) {
            $this->response->setContent($callback . '(' . $this->response->getContent() . ')');
        }
    }

    public function toFullUrl($paramsOrUrl, $params = null)
    {
        $path = is_array($paramsOrUrl) ? $this->router->getRewriteUri() : $paramsOrUrl;
        $url = clone $this->url;
        $url->setBaseUri($this->getDI()->get('config')->apiUri);
        $params = is_array($paramsOrUrl) ? $paramsOrUrl : $params;

        return $url->get($path, $params);
    }

    public function getApiPaginator(\Phalcon\Paginator\AdapterInterface $paginator)
    {
        $pager = $paginator->getPaginate();
        if ($pager->total_pages <= 1) {
            return array();
        }
        $query = $pager->query;

        return array(
            'total' => $pager->total_items,
            'previous' => $this->toFullUrl(array_merge($query, array('page' => $pager->before))),
            'next' => $this->toFullUrl(array_merge($query, array('page' => $pager->next))),
            'last' => $this->toFullUrl(array_merge($query, array('page' => $pager->last))),
        );
    }

    public function initialize()
    {
        $this->view->setModuleLayout('WscnApiVer2', '/views/layouts/default');
        $this->view->setModuleViewsDir('WscnApiVer2', '/views');
        $this->view->setModulePartialsDir('WscnApiVer2', '/views');
    }

}

<?php

namespace Eva\EvaBlog\Models;


use Eva\EvaBlog\Entities;
use \Phalcon\Mvc\Model\Message as Message;
use Eva\EvaEngine\Exception;

class Post extends Entities\Posts
{
    public function initialize()
    {
        $modelManager = $this->getDI()->get('modelsManager');
        $modelManager->registerNamespaceAlias('Text', 'Eva\EvaBlog\Models\Text');
        //$modelManager->load('Text');
        //exit;
        //$this->hasOne("id", 'Eva\EvaBlog\Models\Text', "post_id");
        $this->hasOne("id", 'Text', "post_id");
        parent::initialize();
    }

}

<?php

namespace Eva\EvaEngine\Mvc\Model;

class Manager extends \Phalcon\Mvc\Model\Manager
{
    public function getReadConnection(\Phalcon\Mvc\ModelInterface $model)
    {
        if($this->getDI()->get('dbSlave')) {
            $this->setReadConnectionService($model, 'dbSlave');
        }
        return parent::getReadConnection($model);
    }

    public function getWriteConnection(\Phalcon\Mvc\ModelInterface $model)
    {
        if($this->getDI()->get('dbMaster')) {
            $this->setReadConnectionService($model, 'dbMaster');
        }
        return parent::getWriteConnection($model);
    }
}

<?php

namespace Eva\EvaEngine\Mvc;

class Model extends \Phalcon\Mvc\Model
{
    protected $prefix = 'eva_';

    protected $tableName;

    protected $useMasterSlave = true;

    public function getSource() {
        return $this->prefix . $this->tableName;
    }

    public function dump(array $dataStructure)
    {
        $data = null;
        foreach ($dataStructure as $key => $subdata) {
            if(is_numeric($key)) {
                $data[$subdata] = $this->$subdata;
            } elseif (is_array($subdata)) {
                if($this->$key instanceof \Phalcon\Mvc\Model\Resultset\Simple) {
                    $subdatas = array();
                    foreach($this->$key as $child) {
                        $subdatas[] = $child->dump($subdata);
                    }
                    $data[$key] = $subdatas;
                } else {
                    $data[$key] = $this->$key->dump($subdata);
                }
            } elseif (is_string($subdata)) {
                $data[$key] = $this->$subdata();
            }
        }
        return $data;
    }

    public function initialize()
    {
        if(true === $this->useMasterSlave) {
            $this->setWriteConnectionService('dbMaster');
            $this->setReadConnectionService('dbSlave');
        }
    }
}

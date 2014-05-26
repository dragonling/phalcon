<?php
namespace Eva\EvaComment\Models;

use Eva\EvaComment\Entities\Threads;
use Eva\EvaEngine\Mvc\Model as BaseModel;

class ThreadManager extends BaseModel
{
    function test()
    {
        $phql = 'SELECT * FROM Eva\EvaComment\Entities\Comments';


        $manager = $this->getModelsManager();
        $data = $manager->executeQuery($phql);
        foreach ($data as $k => $comment) {
            echo $comment->id;
        }
//        var_dump($data);
    }

    public function findThreadByUniqueKey($uniqueKey)
    {

        $phql = 'SELECT * FROM Eva\EvaComment\Entities\Threads AS t WHERE t.uniqueKey = :uniqueKey: LIMIT 1';


        $manager = $this->getModelsManager();
        $data = $manager->executeQuery($phql, array('uniqueKey' => $uniqueKey));
        foreach ($data as $k => $thread) {
        }
        return $thread;

    }

    public function addCommentNumber($thread,$num = 1)
    {
        $num = intval($num);
        $phql = "UPDATE Eva\EvaComment\Entities\Threads SET numComments=numComments+$num, lastCommentAt=:now: WHERE id = :id:";

        $manager = $this->getModelsManager();
        $data = $manager->executeQuery($phql, array('id'=>$thread->id,'now'=>time()));
        return $data;
    }
}
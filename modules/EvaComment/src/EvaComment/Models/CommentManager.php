<?php
namespace Eva\EvaComment\Models;

use Eva\EvaComment\Entities\Comments;

use Eva\EvaEngine\Mvc\Model as BaseModel;

use InvalidArgumentException;

class CommentManager extends BaseModel
{
    function test()
    {
        $phql = 'SELECT * FROM Eva\EvaComment\Entities\Comment';


        $manager = $this->getModelsManager();
        $data = $manager->executeQuery($phql);
        foreach ($data as $k => $comment) {
            echo $comment->id;
        }
//        var_dump($data);
    }

    /**
     * {@inheritdoc}
     */
    public function createComment($thread, $parent = null)
    {
        $comment = new Comments;

        $comment->threadId = $thread->id;

        if (null !== $parent) {
            $comment->parentId = $parent->id;
            $comment->rootId = $parent->rootId ? $parent->rootId : $parent->id;

            $comment->parentPath = $parent->parentPath ? $parent->parentPath.'/'.$parent->id : $parent->id;

            $comment->depth = $comment->depth+1;
        }

//        $event = new CommentEvent($comment);
//        $this->dispatcher->dispatch(Events::COMMENT_CREATE, $event);

        return $comment;
    }

    /**
     * {@inheritdoc}
     */
    public function saveComment($comment)
    {
        $thread = $comment->getThread();
        if (null === $thread) {
            throw new InvalidArgumentException('The comment must have a thread');
        }

        $comment->save();

        $threadManager = new ThreadManager();
        $threadManager->addCommentNumber($thread);

        return true;
    }

    function findCommentById($id)
    {
        $comment = Comments::findFirstById($id);
        return $comment;
    }


    function findCommentsByThread($thread, $sorter, $displayDepth)
    {
        $phql = 'SELECT * FROM Eva\EvaComment\Entities\Comments AS c WHERE c.threadId = :threadId: ORDER BY c.createdAt DESC';

        $manager = $this->getModelsManager();
        $comments = $manager->executeQuery($phql, array('threadId' => $thread->id));

        return $comments;
    }

    function findCommentTreeByThread($thread, $sorter, $displayDepth)
    {
        $phql = 'SELECT * FROM Eva\EvaComment\Entities\Comments AS c
                WHERE c.threadId = :threadId: AND c.rootId = 0 ORDER BY c.createdAt DESC';

        $manager = $this->getModelsManager();
        $comments = $manager->executeQuery($phql, array('threadId' => $thread->id));

        return $comments;
    }
} 
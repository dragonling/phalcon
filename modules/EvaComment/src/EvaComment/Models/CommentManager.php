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
            $comment->ancestorId = $parent->ancestorId ? : 0;
        } else {
            $comment->parentId = 0;
            $comment->ancestorId = 0;
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
        $thread->numComments += 1;
        $thread->save();


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
        $phql = 'SELECT * FROM Eva\EvaComment\Entities\Comments AS c WHERE c.threadId = :threadId: ORDER BY c.createdAt DESC';

        $manager = $this->getModelsManager();
        $comments = $manager->executeQuery($phql, array('threadId' => $thread->id));

        return $comments;
    }
} 
<?php
namespace Eva\EvaComment\Controllers;

use Eva\EvaComment\Entities\Comments;
use Eva\EvaComment\Entities\Threads;

use Eva\EvaComment\Models\ThreadManager;
use Eva\EvaComment\Models\CommentManager;

use Eva\EvaEngine\Mvc\Controller\ControllerBase;
use Eva\EvaBlog\Forms;


class ThreadController extends ControllerBase
{
    const VIEW_FLAT = 'flat';
    const VIEW_TREE = 'tree';

    public function initialize()
    {
//        $this->view->setModuleLayout('WscnGold', '/views/layouts/default');
        $this->view->setModuleViewsDir('EvaComment', '/views');
        $this->view->setModulePartialsDir('EvaComment', '/views');
    }

    public function indexAction()
    {
        $this->view->setModuleLayout('WscnGold', '/views/layouts/default');
        $this->getDI()->get('eventsManager')->attach(
            "view",
            function ($event, $view) {
                p($view);
//                exit;
            }
        );
        echo 'index';
    }

    /**
     * Get the comments of a thread. Creates a new thread if none exists.
     *
     * @param string $id Id of the thread
     *
     * @todo Add support page/pagesize/sorting/tree-depth parameters
     */
    public function getThreadCommentsAction($uniqueKey)
    {
        $displayDepth = $this->request->getQuery('displayDepth');
        $sorter = $this->request->getQuery('sorter');

        $threadManager = new ThreadManager();

        $thread = $threadManager->findThreadByUniqueKey($uniqueKey);

        // We're now sure it is no duplicate id, so create the thread
        if (null === $thread) {
            // Decode the permalink for cleaner storage (it is encoded on the client side)
            $permalink = urldecode($this->request->getQuery('permalink'));

            $thread = new Threads();
            $thread->uniqueKey = $uniqueKey;
            $thread->permalink = $permalink;

            //todo validate
            if ($thread->save() == false) {

                foreach ($thread->getMessages() as $message) {
                    echo $message, "\n";
                }
                exit;
//                throw new \Exception('Save failed');
            }
        }

//        $viewMode = $this->dispatcher->getParam('view');

        $commentManager = new CommentManager();

        $viewMode = 'tree';

        switch ($viewMode) {
            case self::VIEW_FLAT:
                $comments = $commentManager->findCommentsByThread($thread, $sorter, $displayDepth);

                // We need nodes for the api to return a consistent response, not an array of comments
//                $comments = array_map(function($comment) {
//                        return array('comment' => $comment, 'children' => array());
//                    },
//                    $comments
//                );
                break;
            case self::VIEW_TREE:

            default:
                $comments = $commentManager->findCommentTreeByThread($thread, $sorter, $displayDepth);
                break;
        }

        $this->view->setVars(
            array(
                'comments' => $comments,
                'displayDepth' => $displayDepth,
                'sorter' => 'date',
                'thread' => $thread,
                'view' => $viewMode,
            )
        );
    }

    /**
     * Creates a new Comment for the Thread from the submitted data.
     *
     * @param string $id The id of the thread
     *
     * @return View
     * @todo Add support for comment parent (in form?)
     */
    public function postThreadCommentsAction($threadKey)
    {
        $threadManager = new ThreadManager();
        $thread = $threadManager->findThreadByUniqueKey($threadKey);
        if (!$thread) {
            throw new \Exception(sprintf('Thread with identifier of "%s" does not exist', $threadKey));
        }

//        if (!$thread->isCommentable()) {
//            throw new \Exception(sprintf('Thread "%s" is not commentable', $threadKey));
//        }


        $parentId = $this->request->getPost('parentId');
        $parent = $this->getValidCommentParent($thread, $parentId);


        $content = $this->request->getPost("content");
        $commentManager = new CommentManager();
        $comment = $commentManager->createComment($thread, $parent);

//        if ($form->isValid()) {
        $comment->content = $content;

        if ($commentManager->saveComment($comment) !== false) {
            $errors = $comment->getMessages();
            p($errors);
//                return $this->getViewHandler()->handle($this->onCreateCommentSuccess($form, $id, $parent));
        }

//        }
        $this->view->setVars(
            array(
                'comment' => $comment,
            )
        );
//        return $this->getViewHandler()->handle($this->onCreateCommentError($form, $id, $parent));
    }

    /**
     * Checks if a comment belongs to a thread. Returns the comment if it does.
     *
     * @param ThreadInterface $thread Thread object
     * @param mixed $commentId Id of the comment.
     *
     * @return CommentInterface|null The comment.
     */
    private function getValidCommentParent($thread, $commentId)
    {
        if (null !== $commentId) {
            $commentManager = new CommentManager();
            $comment = $commentManager->findCommentById($commentId);
            if (!$comment) {
                exit('Parent comment with identifier "%s" does not exist');
//                throw new NotFoundHttpException(sprintf('Parent comment with identifier "%s" does not exist', $commentId));
            }

            if ($comment->getThread() !== $thread) {
                exit('Parent comment is not a comment of the given thread.');
//                throw new NotFoundHttpException('Parent comment is not a comment of the given thread.');
            }

            return $comment;
        }
    }

}

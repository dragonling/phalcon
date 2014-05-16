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
        $this->getDI()->get('eventsManager')->attach("view", function($event, $view){
                p($view);
//                exit;
            });
        echo 'index';
    }

    /**
     * Get the comments of a thread. Creates a new thread if none exists.
     *
     * @param string  $id      Id of the thread
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

        $this->view->setVars(array(
                    'comments' => $comments,
                    'displayDepth' => $displayDepth,
                    'sorter' => 'date',
                    'thread' => $thread,
                    'view' => $viewMode,
                ));
    }

    /**
     * Creates a new Comment for the Thread from the submitted data.
     *
     * @param string  $id      The id of the thread
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
        $this->view->setVars(array(
                'comment' => $comment,
            ));
//        return $this->getViewHandler()->handle($this->onCreateCommentError($form, $id, $parent));
    }

    /**
     * Gets the threads for the specified ids.
     *
     * @param Request $request
     *
     * @return View
     */
    public function getThreadsActions(Request $request)
    {
        $ids = $request->query->get('ids');

        $threads = $this->container->get('fos_comment.manager.thread')->findThreadsBy(array('id' => $ids));

        $view = View::create()
            ->setData(array('threads' => $threads));

        return $this->getViewHandler()->handle($view);
    }





    /**
     * Presents the form to use to create a new Thread.
     *
     * @return View
     */
    public function newThreadsAction()
    {
        exit('fdsafdsa');
        $form = $this->container->get('fos_comment.form_factory.thread')->createForm();

        $view = View::create()
            ->setData(array('form' => $form->createView()))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'new'));

        return $this->getViewHandler()->handle($view);
    }

    /**
     * Gets the thread for a given id.
     *
     * @param string $id
     *
     * @return View
     */
    public function getThreadAction($id)
    {
        $manager = $this->container->get('fos_comment.manager.thread');
        $thread = $manager->findThreadById($id);

        if (null === $thread) {
            throw new NotFoundHttpException(sprintf("Thread with id '%s' could not be found.", $id));
        }

        $view = View::create()
            ->setData(array('thread' => $thread));

        return $this->getViewHandler()->handle($view);
    }


    /**
     * Creates a new Thread from the submitted data.
     *
     * @param Request $request The current request
     *
     * @return View
     */
    public function postThreadsAction(Request $request)
    {
        $threadManager = $this->container->get('fos_comment.manager.thread');
        $thread = $threadManager->createThread();
        $form = $this->container->get('fos_comment.form_factory.thread')->createForm();
        $form->setData($thread);

        if ('POST' === $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                if (null !== $threadManager->findThreadById($thread->getId())) {
                    $this->onCreateThreadErrorDuplicate($form);
                }

                // Add the thread
                $threadManager->saveThread($thread);

                return $this->getViewHandler()->handle($this->onCreateThreadSuccess($form));
            }
        }

        return $this->getViewHandler()->handle($this->onCreateThreadError($form));
    }

    /**
     * Get the edit form the open/close a thread.
     *
     * @param Request $request Currenty request
     * @param mixed   $id      Thread id
     *
     * @return View
     */
    public function editThreadCommentableAction(Request $request, $id)
    {
        $manager = $this->container->get('fos_comment.manager.thread');
        $thread = $manager->findThreadById($id);

        if (null === $thread) {
            throw new NotFoundHttpException(sprintf("Thread with id '%s' could not be found.", $id));
        }

        $thread->setCommentable($this->getRequest()->query->get('value', 1));

        $form = $this->container->get('fos_comment.form_factory.commentable_thread')->createForm();
        $form->setData($thread);

        $view = View::create()
            ->setData(array('form' => $form, 'id' => $id, 'isCommentable' => $thread->isCommentable()))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'commentable'));

        return $this->getViewHandler()->handle($view);
    }

    /**
     * Edits the thread.
     *
     * @param Request $request Currenty request
     * @param mixed   $id      Thread id
     *
     * @return View
     */
    public function patchThreadCommentableAction(Request $request, $id)
    {
        $manager = $this->container->get('fos_comment.manager.thread');
        $thread = $manager->findThreadById($id);

        if (null === $thread) {
            throw new NotFoundHttpException(sprintf("Thread with id '%s' could not be found.", $id));
        }

        $form = $this->container->get('fos_comment.form_factory.commentable_thread')->createForm();
        $form->setData($thread);

        if ('PATCH' === $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {
                $manager->saveThread($thread);

                return $this->getViewHandler()->handle($this->onOpenThreadSuccess($form));
            }
        }

        return $this->getViewHandler()->handle($this->onOpenThreadError($form));
    }

    /**
     * Presents the form to use to create a new Comment for a Thread.
     *
     * @param string $id
     *
     * @return View
     */
    public function newThreadCommentsAction($id)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        if (!$thread) {
            throw new NotFoundHttpException(sprintf('Thread with identifier of "%s" does not exist', $id));
        }

        $comment = $this->container->get('fos_comment.manager.comment')->createComment($thread);

        $parent = $this->getValidCommentParent($thread, $this->getRequest()->query->get('parentId'));

        $form = $this->container->get('fos_comment.form_factory.comment')->createForm();
        $form->setData($comment);

        $view = View::create()
            ->setData(array(
                    'form' => $form->createView(),
                    'first' => 0 === $thread->getNumComments(),
                    'thread' => $thread,
                    'parent' => $parent,
                    'id' => $id,
                ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'comment_new'));

        return $this->getViewHandler()->handle($view);
    }

    /**
     * Get a comment of a thread.
     *
     * @param string $id        Id of the thread
     * @param mixed  $commentId Id of the comment
     *
     * @return View
     */
    public function getThreadCommentAction($id, $commentId)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        $comment = $this->container->get('fos_comment.manager.comment')->findCommentById($commentId);
        $parent = null;

        if (null === $thread || null === $comment || $comment->getThread() !== $thread) {
            throw new NotFoundHttpException(sprintf("No comment with id '%s' found for thread with id '%s'", $commentId, $id));
        }

        $ancestors = $comment->getAncestors();
        if (count($ancestors) > 0) {
            $parent = $this->getValidCommentParent($thread, $ancestors[count($ancestors) - 1]);
        }

        $view = View::create()
            ->setData(array('comment' => $comment, 'thread' => $thread, 'parent' => $parent, 'depth' => $comment->getDepth()))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'comment'));

        return $this->getViewHandler()->handle($view);
    }

    /**
     * Get the delete form for a comment.
     *
     * @param Request $request   Current request
     * @param string  $id        Id of the thread
     * @param mixed   $commentId Id of the comment
     *
     * @return View
     */
    public function removeThreadCommentAction(Request $request, $id, $commentId)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        $comment = $this->container->get('fos_comment.manager.comment')->findCommentById($commentId);

        if (null === $thread || null === $comment || $comment->getThread() !== $thread) {
            throw new NotFoundHttpException(sprintf("No comment with id '%s' found for thread with id '%s'", $commentId, $id));
        }

        $form = $this->container->get('fos_comment.form_factory.delete_comment')->createForm();
        $comment->setState($request->query->get('value', $comment::STATE_DELETED));

        $form->setData($comment);

        $view = View::create()
            ->setData(array('form' => $form, 'id' => $id, 'commentId' => $commentId))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'comment_remove'));

        return $this->getViewHandler()->handle($view);
    }

    /**
     * Edits the comment state
     *
     * @param Request $request   Current request
     * @param mixed   $id        Thread id
     * @param mixed   $commentId Id of the comment
     *
     * @return View
     */
    public function patchThreadCommentStateAction(Request $request, $id, $commentId)
    {
        $manager = $this->container->get('fos_comment.manager.comment');
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        $comment = $manager->findCommentById($commentId);

        if (null === $thread || null === $comment || $comment->getThread() !== $thread) {
            throw new NotFoundHttpException(sprintf("No comment with id '%s' found for thread with id '%s'", $commentId, $id));
        }

        $form = $this->container->get('fos_comment.form_factory.delete_comment')->createForm();
        $form->setData($comment);

        $form->bind($request);

        if ($form->isValid()) {
            if ($manager->saveComment($comment) !== false) {
                return $this->getViewHandler()->handle($this->onRemoveThreadCommentSuccess($form, $id));
            }
        }

        return $this->getViewHandler()->handle($this->onRemoveThreadCommentError($form, $id));
    }

    /**
     * Presents the form to use to edit a Comment for a Thread.
     *
     * @param string $id        Id of the thread
     * @param mixed  $commentId Id of the comment
     *
     * @return View
     */
    public function editThreadCommentAction($id, $commentId)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        $comment = $this->container->get('fos_comment.manager.comment')->findCommentById($commentId);

        if (null === $thread || null === $comment || $comment->getThread() !== $thread) {
            throw new NotFoundHttpException(sprintf("No comment with id '%s' found for thread with id '%s'", $commentId, $id));
        }

        $form = $this->container->get('fos_comment.form_factory.comment')->createForm();
        $form->setData($comment);

        $view = View::create()
            ->setData(array(
                    'form' => $form->createView(),
                    'comment' => $comment,
                ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'comment_edit'));

        return $this->getViewHandler()->handle($view);
    }

    /**
     * Edits a given comment.
     *
     * @param Request $request   Current request
     * @param string  $id        Id of the thread
     * @param mixed   $commentId Id of the comment
     *
     * @return View
     */
    public function putThreadCommentsAction(Request $request, $id, $commentId)
    {
        $commentManager = $this->container->get('fos_comment.manager.comment');

        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        $comment = $commentManager->findCommentById($commentId);

        if (null === $thread || null === $comment || $comment->getThread() !== $thread) {
            throw new NotFoundHttpException(sprintf("No comment with id '%s' found for thread with id '%s'", $commentId, $id));
        }

        $form = $this->container->get('fos_comment.form_factory.comment')->createForm();
        $form->setData($comment);
        $form->bind($request);

        if ($form->isValid()) {
            if ($commentManager->saveComment($comment) !== false) {
                return $this->getViewHandler()->handle($this->onEditCommentSuccess($form, $id, $comment->getParent()));
            }
        }

        return $this->getViewHandler()->handle($this->onEditCommentError($form, $id, $comment->getParent()));
    }





    /**
     * Get the votes of a comment.
     *
     * @param string $id        Id of the thread
     * @param mixed  $commentId Id of the comment
     *
     * @return View
     */
    public function getThreadCommentVotesAction($id, $commentId)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        $comment = $this->container->get('fos_comment.manager.comment')->findCommentById($commentId);

        if (null === $thread || null === $comment || $comment->getThread() !== $thread) {
            throw new NotFoundHttpException(sprintf("No comment with id '%s' found for thread with id '%s'", $commentId, $id));
        }

        $view = View::create()
            ->setData(array(
                    'commentScore' => $comment->getScore(),
                ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'comment_votes'));

        return $this->getViewHandler()->handle($view);
    }

    /**
     * Presents the form to use to create a new Vote for a Comment.
     *
     * @param string $id        Id of the thread
     * @param mixed  $commentId Id of the comment
     *
     * @return View
     */
    public function newThreadCommentVotesAction($id, $commentId)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        $comment = $this->container->get('fos_comment.manager.comment')->findCommentById($commentId);

        if (null === $thread || null === $comment || $comment->getThread() !== $thread) {
            throw new NotFoundHttpException(sprintf("No comment with id '%s' found for thread with id '%s'", $commentId, $id));
        }

        $vote = $this->container->get('fos_comment.manager.vote')->createVote($comment);
        $vote->setValue($this->getRequest()->query->get('value', 1));

        $form = $this->container->get('fos_comment.form_factory.vote')->createForm();
        $form->setData($vote);

        $view = View::create()
            ->setData(array(
                    'id' => $id,
                    'commentId' => $commentId,
                    'form' => $form->createView()
                ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'vote_new'));

        return $this->getViewHandler()->handle($view);
    }

    /**
     * Creates a new Vote for the Comment from the submitted data.
     *
     * @param string $id        Id of the thread
     * @param mixed  $commentId Id of the comment
     *
     * @return View
     */
    public function postThreadCommentVotesAction($id, $commentId)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        $comment = $this->container->get('fos_comment.manager.comment')->findCommentById($commentId);

        if (null === $thread || null === $comment || $comment->getThread() !== $thread) {
            throw new NotFoundHttpException(sprintf("No comment with id '%s' found for thread with id '%s'", $commentId, $id));
        }

        $voteManager = $this->container->get('fos_comment.manager.vote');
        $vote = $voteManager->createVote($comment);

        $form = $this->container->get('fos_comment.form_factory.vote')->createForm();
        $form->setData($vote);

        $form->bind($this->container->get('request'));

        if ($form->isValid()) {
            $voteManager->saveVote($vote);

            return $this->getViewHandler()->handle($this->onCreateVoteSuccess($form, $id, $commentId));
        }

        return $this->getViewHandler()->handle($this->onCreateVoteError($form, $id, $commentId));
    }

    /**
     * Forwards the action to the comment view on a successful form submission.
     *
     * @param FormInterface    $form   Form with the error
     * @param string           $id     Id of the thread
     * @param CommentInterface $parent Optional comment parent
     *
     * @return View
     */
    protected function onCreateCommentSuccess(FormInterface $form, $id, CommentInterface $parent = null)
    {
        return RouteRedirectView::create('fos_comment_get_thread_comment', array('id' => $id, 'commentId' => $form->getData()->getId()));
    }

    /**
     * Returns a HTTP_BAD_REQUEST response when the form submission fails.
     *
     * @param FormInterface    $form   Form with the error
     * @param string           $id     Id of the thread
     * @param CommentInterface $parent Optional comment parent
     *
     * @return View
     */
    protected function onCreateCommentError(FormInterface $form, $id, CommentInterface $parent = null)
    {
        $view = View::create()
            ->setStatusCode(Codes::HTTP_BAD_REQUEST)
            ->setData(array(
                    'form' => $form,
                    'id' => $id,
                    'parent' => $parent,
                ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'comment_new'));

        return $view;
    }

    /**
     * Forwards the action to the thread view on a successful form submission.
     *
     * @param FormInterface $form
     *
     * @return View
     */
    protected function onCreateThreadSuccess(FormInterface $form)
    {
        return RouteRedirectView::create('fos_comment_get_thread', array('id' => $form->getData()->getId()));
    }

    /**
     * Returns a HTTP_BAD_REQUEST response when the form submission fails.
     *
     * @param FormInterface $form
     *
     * @return View
     */
    protected function onCreateThreadError(FormInterface $form)
    {
        $view = View::create()
            ->setStatusCode(Codes::HTTP_BAD_REQUEST)
            ->setData(array(
                    'form' => $form,
                ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'new'));

        return $view;
    }

    /**
     * Returns a HTTP_BAD_REQUEST response when the Thread creation fails due to a duplicate id.
     *
     * @param FormInterface $form
     *
     * @return View
     */
    protected function onCreateThreadErrorDuplicate(FormInterface $form)
    {
        return new Response(sprintf("Duplicate thread id '%s'.", $form->getData()->getId()), Codes::HTTP_BAD_REQUEST);
    }

    /**
     * Action executed when a vote was succesfully created.
     *
     * @param FormInterface $form      Form with the error
     * @param string        $id        Id of the thread
     * @param mixed         $commentId Id of the comment
     *
     * @return View
     * @todo Think about what to show. For now the new score of the comment.
     */
    protected function onCreateVoteSuccess(FormInterface $form, $id, $commentId)
    {
        return RouteRedirectView::create('fos_comment_get_thread_comment_votes', array('id' => $id, 'commentId' => $commentId));
    }

    /**
     * Returns a HTTP_BAD_REQUEST response when the form submission fails.
     *
     * @param FormInterface $form      Form with the error
     * @param string        $id        Id of the thread
     * @param mixed         $commentId Id of the comment
     *
     * @return View
     */
    protected function onCreateVoteError(FormInterface $form, $id, $commentId)
    {
        $view = View::create()
            ->setStatusCode(Codes::HTTP_BAD_REQUEST)
            ->setData(array(
                    'id' => $id,
                    'commentId' => $commentId,
                    'form' => $form,
                ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'vote_new'));

        return $view;
    }

    /**
     * Forwards the action to the comment view on a successful form submission.
     *
     * @param FormInterface $form Form with the error
     * @param string        $id   Id of the thread
     *
     * @return View
     */
    protected function onEditCommentSuccess(FormInterface $form, $id)
    {
        return RouteRedirectView::create('fos_comment_get_thread_comment', array('id' => $id, 'commentId' => $form->getData()->getId()));
    }

    /**
     * Returns a HTTP_BAD_REQUEST response when the form submission fails.
     *
     * @param FormInterface $form Form with the error
     * @param string        $id   Id of the thread
     *
     * @return View
     */
    protected function onEditCommentError(FormInterface $form, $id)
    {
        $view = View::create()
            ->setStatusCode(Codes::HTTP_BAD_REQUEST)
            ->setData(array(
                    'form' => $form,
                    'comment' => $form->getData(),
                ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'comment_edit'));

        return $view;
    }

    /**
     * Forwards the action to the open thread edit view on a successful form submission.
     *
     * @param FormInterface $form
     *
     * @return View
     */
    protected function onOpenThreadSuccess(FormInterface $form)
    {
        return RouteRedirectView::create('fos_comment_edit_thread_commentable', array('id' => $form->getData()->getId(), 'value' => !$form->getData()->isCommentable()));
    }

    /**
     * Returns a HTTP_BAD_REQUEST response when the form submission fails.
     *
     * @param FormInterface $form
     *
     * @return View
     */
    protected function onOpenThreadError(FormInterface $form)
    {
        $view = View::create()
            ->setStatusCode(Codes::HTTP_BAD_REQUEST)
            ->setData(array(
                    'form' => $form,
                    'id' => $form->getData()->getId(),
                    'isCommentable' => $form->getData()->isCommentable(),
                ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'commentable'));

        return $view;
    }

    /**
     * Forwards the action to the comment view on a successful form submission.
     *
     * @param FormInterface $form Comment delete form
     * @param integer       $id   Thread id
     *
     * @return View
     */
    protected function onRemoveThreadCommentSuccess(FormInterface $form, $id)
    {
        return RouteRedirectView::create('fos_comment_get_thread_comment', array('id' => $id, 'commentId' => $form->getData()->getId()));
    }

    /**
     * Returns a HTTP_BAD_REQUEST response when the form submission fails.
     *
     * @param FormInterface $form Comment delete form
     * @param integer       $id   Thread id
     *
     * @return View
     */
    protected function onRemoveThreadCommentError(FormInterface $form, $id)
    {
        $view = View::create()
            ->setStatusCode(Codes::HTTP_BAD_REQUEST)
            ->setData(array(
                    'form' => $form,
                    'id' => $id,
                    'value' => $form->getData()->getState(),
                ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'comment_remove'));

        return $view;
    }

    /**
     * Checks if a comment belongs to a thread. Returns the comment if it does.
     *
     * @param ThreadInterface $thread    Thread object
     * @param mixed           $commentId Id of the comment.
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

    /**
     * @return \FOS\RestBundle\View\ViewHandler
     */
    private function getViewHandler()
    {
        return $this->container->get('fos_rest.view_handler');
    }
}

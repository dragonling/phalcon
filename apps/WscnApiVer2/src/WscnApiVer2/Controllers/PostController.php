<?php

namespace WscnApiVer2\Controllers;

use Swagger\Annotations as SWG;
use Eva\EvaBlog\Models;
use Eva\EvaBlog\Forms;
use Eva\EvaEngine\Exception;

/**
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Resource(
 *  apiVersion="0.2",
 *  swaggerVersion="1.2",
 *  resourcePath="/post",
 *  basePath="http://l.api.goldtoutiao.com/v2"
 * )
 */
class PostController extends ControllerBase
{

    /**
     *
     * @SWG\Api(
     *   path="/post",
     *   description="Post related api",
     *   produces="['application/json']",
     *   @SWG\Operations(
     *     @SWG\Operation(
     *       method="GET",
     *       summary="Get post list",
     *       notes="Returns a post based on ID",
     *       @SWG\Parameters(
     *         @SWG\Parameter(
     *           name="q",
     *           description="Keyword",
     *           paramType="query",
     *           required=false,
     *           type="string"
     *         ),
     *         @SWG\Parameter(
     *           name="status",
     *           description="Status, allow value : pending | published | deleted | draft",
     *           paramType="query",
     *           required=false,
     *           type="string"
     *         ),
     *         @SWG\Parameter(
     *           name="uid",
     *           description="User ID",
     *           paramType="query",
     *           required=false,
     *           type="int"
     *         ),
     *         @SWG\Parameter(
     *           name="cid",
     *           description="Category ID",
     *           paramType="query",
     *           required=false,
     *           type="int"
     *         ),
     *         @SWG\Parameter(
     *           name="order",
     *           description="Order, allow value : +-id, +-created_at, default is -created_at",
     *           paramType="query",
     *           required=false,
     *           type="string"
     *         ),
     *         @SWG\Parameter(
     *           name="limit",
     *           description="Limit max:100 | min:3; default is 25",
     *           paramType="query",
     *           required=false,
     *           type="int"
     *         )
     *       )
     *     )
     *   )
     * )
     */
    public function indexAction()
    {
        $limit = $this->request->getQuery('limit', 'int', 25);
        $limit = $limit > 100 ? 100 : $limit;
        $limit = $limit < 3 ? 3 : $limit;
        $orderMapping = array(
            'id' => 'id ASC',
            '-id' => 'id DESC',
            'created_at' => 'createdAt ASC',
            '-created_at' => 'createdAt DESC',
        );
        $order = $this->request->getQuery('order', 'string', '-created_at');
        $query = array(
            'q' => $this->request->getQuery('q', 'string'),
            'status' => $this->request->getQuery('status', 'string'),
            'uid' => $this->request->getQuery('uid', 'int'),
            'cid' => $this->request->getQuery('cid', 'int'),
            'username' => $this->request->getQuery('username', 'string'),
            'order' => $order,
            'limit' => $limit,
            'page' => $this->request->getQuery('page', 'int', 1),
        );

        $form = new Forms\FilterForm();
        $form->setValues($this->request->getQuery());

        $post = new Models\Post();
        $posts = $post->findPosts($query);
        $paginator = new \Eva\EvaEngine\Paginator(array(
            "data" => $posts,
            "limit"=> $limit,
            "page" => $query['page']
        ));
        $paginator->setQuery($query);
        $pager = $paginator->getPaginate();

        $postArray = array();
        if($pager->items) {
            foreach($pager->items as $key => $post) {
                $postArray[] = $post->dump(Models\Post::$defaultDump);
            }
        }
        return $this->response->setJsonContent(array(
            'paginator' => $this->getApiPaginator($paginator),
            'results' => $postArray,
        ));
    }

    /**
     *
     * @SWG\Api(
     *   path="/post/{postId}",
     *   description="Post related api",
     *   produces="['application/json']",
     *   @SWG\Operations(
     *     @SWG\Operation(
     *       method="GET",
     *       summary="Find post by ID",
     *       notes="Returns a post based on ID",
     *       @SWG\Parameters(
     *         @SWG\Parameter(
     *           name="postId",
     *           description="ID of post",
     *           paramType="path",
     *           required=true,
     *           type="int"
     *         )
     *       ),
     *       @SWG\ResponseMessages(
     *          @SWG\ResponseMessage(
     *            code=400,
     *            message="Invalid ID supplied"
     *          ),
     *          @SWG\ResponseMessage(
     *            code=404,
     *            message="post not found"
     *          )
     *       )
     *     )
     *   )
     * )
     */
    public function getAction()
    {
        $id = $this->dispatcher->getParam('id');
        $postModel = new Models\Post();
        $post = $postModel->findFirst($id);
        if($post) {
            $post = $post->dump(Models\Post::$defaultDump);
        }
        $this->response->setContentType('application/json', 'utf-8');
        return $this->response->setJsonContent($post);
    }


    /**
     *
     * @SWG\Api(
     *   path="/post/{postId}",
     *   description="Post related api",
     *   produces="['application/json']",
     *   @SWG\Operations(
     *     @SWG\Operation(
     *       method="PUT",
     *       summary="Update post by ID",
     *       notes="Returns updated post",
     *       @SWG\Parameters(
     *         @SWG\Parameter(
     *           name="postId",
     *           description="ID of post",
     *           paramType="path",
     *           required=true,
     *           type="int"
     *         )
     *       ),
     *       @SWG\Parameters(
     *         @SWG\Parameter(
     *           name="postData",
     *           description="Post info",
     *           paramType="body",
     *           required=true,
     *           type="string"
     *         )
     *       ),
     *       @SWG\ResponseMessages(
     *          @SWG\ResponseMessage(
     *            code=400,
     *            message="Invalid ID supplied"
     *          ),
     *          @SWG\ResponseMessage(
     *            code=404,
     *            message="post not found"
     *          )
     *       )
     *     )
     *   )
     * )
     */
     public function putAction()
     {
         $id = $this->dispatcher->getParam('id');
         $data = $this->request->getRawBody();
         if(!$data) {
             throw new Exception\InvalidArgumentException('No data input');
         }
         if(!$data = json_decode($data, true)) {
             throw new Exception\InvalidArgumentException('Data not able to decode as JSON');
         }

         $post = Models\Post::findFirst($id);
         if(!$post) {
             throw new Exception\ResourceNotFoundException('Request post not exist');
         }
         try {
             $post->updatePost($data);
             $data = $post->dump(Models\Post::$defaultDump);
             return $this->response->setJsonContent($data);
         } catch(\Exception $e) {
             return $this->errorHandler($e, $post->getMessages());
         }
     }

     /**
     *
     * @SWG\Api(
     *   path="/post",
     *   description="Post related api",
     *   produces="['application/json']",
     *   @SWG\Operations(
     *     @SWG\Operation(
     *       method="POST",
     *       summary="Create new post",
     *       notes="Returns a post based on ID",
     *       @SWG\Parameters(
     *         @SWG\Parameter(
     *           name="post json",
     *           description="Post info",
     *           paramType="body",
     *           required=true,
     *           type="string"
     *         )
     *       ),
     *       @SWG\ResponseMessages(
     *          @SWG\ResponseMessage(
     *            code=400,
     *            message="Invalid ID supplied"
     *          ),
     *          @SWG\ResponseMessage(
     *            code=404,
     *            message="post not found"
     *          )
     *       )
     *     )
     *   )
     * )
     */
    public function postAction()
    {
        $post = new Models\Post();
        $postForm = new \Eva\EvaBlog\Forms\PostForm();
        $postForm->setModel($post);

        $textForm = new \Eva\EvaBlog\Forms\TextForm();
        $textForm->setModel(new Models\Text());
        $textForm->setPrefix('Text');

        $data = $this->request->getRawBody();
        if(!$data) {
            throw new Exception\InvalidArgumentException('No data input');
        }
        if(!$data = json_decode($data, true)) {
            throw new Exception\InvalidArgumentException('Data not able to decode as JSON');
        }
        try {
            $post->createPost($data);
            $data = $post->dump(Models\Post::$defaultDump);
            return $this->response->setJsonContent($data);
        } catch(\Exception $e) {
            return $this->errorHandler($e, $post->getMessages());
        }
    }

    /**
     *
     * @SWG\Api(
     *   path="/post/{postId}",
     *   description="Post related api",
     *   produces="['application/json']",
     *   @SWG\Operations(
     *     @SWG\Operation(
     *       method="DELETE",
     *       summary="Delete post by ID",
     *       notes="Returns deleted post",
     *       @SWG\Parameters(
     *         @SWG\Parameter(
     *           name="postId",
     *           description="ID of post",
     *           paramType="path",
     *           required=true,
     *           type="int"
     *         )
     *       )
     *     )
     *   )
     * )
     */
    public function deleteAction()
    {
         $id = $this->dispatcher->getParam('id');
         $post = Models\Post::findFirst($id);
         if(!$post) {
             throw new Exception\ResourceNotFoundException('Request post not exist');
         }
         $postinfo = $post->dump(Models\Post::$defaultDump);
         try {
             $post->removePost($id);
             return $this->response->setJsonContent($postinfo);
         } catch(\Exception $e) {
             return $this->errorHandler($e, $post->getMessages());
         }
    }
}

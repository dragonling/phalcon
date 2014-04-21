<?php

namespace WscnApiVer2\Controllers;

use Swagger\Annotations as SWG;
use Eva\EvaBlog\Models;
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
        //$format = $this->dispatcher->getParam('format');
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
             $this->response->setContentType('application/json', 'utf-8');
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
            $this->response->setContentType('application/json', 'utf-8');
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
             $this->response->setContentType('application/json', 'utf-8');
             return $this->response->setJsonContent($postinfo);
         } catch(\Exception $e) {
             return $this->errorHandler($e, $post->getMessages());
         }
    }
}

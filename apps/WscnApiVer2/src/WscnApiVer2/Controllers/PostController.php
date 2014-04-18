<?php

namespace WscnApiVer2\Controllers;

use Swagger\Annotations as SWG;
use Eva\EvaBlog\Models;

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
            $post = $post->dump(array(
                'id',
                'title',
                'sourceCode',
                'createdAt',
                'summary',
                'summaryHtml' => 'getSummaryHtml',
                'commentStatus',
                //'source',
                //'sourceUrl',
                'url' => 'getUrl',
                'imageUrl' => 'getImageUrl',
                'Tags' => array(
                    'id',
                    'tagName',
                ),
                'Categories' => array(
                    'id',
                    'categoryName',
                ),
                'User' => array(
                    'id',
                    'username',
                ),
            ));
        }
        return $this->response->setJsonContent($post);
        //$format = $this->dispatcher->getParam('format');
    }
}

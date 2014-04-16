<?php

namespace WscnApiVer2\Controllers;

use Swagger\Annotations as SWG;
/**
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Resource(
 *  apiVersion="0.2",
 *  swaggerVersion="1.2",
 *  resourcePath="/resource/post",
 *  basePath="http://api.goldtoutiao.com/v2"
 * )
 */
class PostController extends ControllerBase
{
    /**
     *
     * @SWG\Api(
     *   path="/post/news{format}/{postId}",
     *   description="Operations about facets",
     *   produces="['application/json']",
     *   @SWG\Operations(
     *     @SWG\Operation(
     *       method="GET",
     *       summary="Find facet by ID",
     *       notes="Returns a facet based on ID",
     *       type="FacetResult",
     *       nickname="getfacetById",
     *       @SWG\Parameters(
     *         @SWG\Parameter(
     *           name="facetId",
     *           description="ID of facet that needs to be fetched",
     *           paramType="path",
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
     *            message="facet not found"
     *          )
     *       )
     *     )
     *   )
     * )
     */
    public function newsAction()
    {
        $id = $this->dispatcher->getParam('id');
        $format = $this->dispatcher->getParam('format');
    }
}

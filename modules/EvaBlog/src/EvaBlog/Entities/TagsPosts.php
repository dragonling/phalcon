<?php


namespace Eva\EvaBlog\Entities;


class TagsPosts extends \Eva\EvaEngine\Model
{
    protected $tableName = 'blog_tags_posts';

    /**
     *
     * @var integer
     */
    public $tag_id;
     
    /**
     *
     * @var integer
     */
    public $post_id;
     
    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'tag_id' => 'tag_id', 
            'post_id' => 'post_id'
        );
    }

}

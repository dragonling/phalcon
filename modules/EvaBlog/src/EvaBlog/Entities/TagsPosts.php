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


    public function initialize()
    {
        $this->belongsTo('tag_id', 'Eva\EvaBlog\Entities\Tags', 'id', 
            array('alias' => 'Tag')
        );
        $this->belongsTo('post_id', 'Eva\EvaBlog\Entities\Posts', 'id', 
            array('alias' => 'Post')
        );
    }

}

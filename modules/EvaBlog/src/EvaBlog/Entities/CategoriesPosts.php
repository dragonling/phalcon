<?php


namespace Eva\EvaBlog\Entities;


class CategoriesPosts extends \Eva\EvaEngine\Model
{
    protected $tableName = 'blog_categories_posts';

    /**
     *
     * @var integer
     */
    public $category_id;
     
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
            'category_id' => 'category_id', 
            'post_id' => 'post_id'
        );
    }


    public function initialize()
    {
        $this->belongsTo('category_id', 'Eva\EvaBlog\Entities\Categories', 'id', 
            array('alias' => 'Category')
        );
        $this->belongsTo('post_id', 'Eva\EvaBlog\Entities\Posts', 'id', 
            array('alias' => 'Post')
        );
    }
}

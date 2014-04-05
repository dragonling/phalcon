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
}

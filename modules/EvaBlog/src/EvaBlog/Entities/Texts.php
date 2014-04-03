<?php


namespace Eva\EvaBlog\Entities;


class Texts extends \Eva\EvaEngine\Model
{

    /**
     *
     * @var integer
     */
    public $post_id;
     
    /**
     *
     * @var string
     */
    public $metaKeywords;
     
    /**
     *
     * @var string
     */
    public $metaDescription;
     
    /**
     *
     * @var string
     */
    public $toc;
     
    /**
     *
     * @var string
     */
    public $content;
     
    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'post_id' => 'post_id', 
            'metaKeywords' => 'metaKeywords', 
            'metaDescription' => 'metaDescription', 
            'toc' => 'toc', 
            'content' => 'content'
        );
    }

    protected $tableName = 'blog_texts';
}

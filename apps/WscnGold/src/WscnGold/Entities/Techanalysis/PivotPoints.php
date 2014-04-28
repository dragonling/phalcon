<?php

use WscnGold\Entities\Techanalysis;

class PivotPoints extends \Eva\EvaEngine\Mvc\Model
{
    protected $tableName = 'techanalysis_pivot_points';

    /**
     *
     * @var integer
     */
    public $id;
     
    /**
     *
     * @var string
     */
    public $symbol;
     
    /**
     *
     * @var string
     */
    public $period;
     
    /**
     *
     * @var string
     */
    public $name;
     
    /**
     *
     * @var double
     */
    public $s3;
     
    /**
     *
     * @var double
     */
    public $s2;
     
    /**
     *
     * @var double
     */
    public $s1;
     
    /**
     *
     * @var double
     */
    public $pivotPoints;
     
    /**
     *
     * @var double
     */
    public $r1;
     
    /**
     *
     * @var double
     */
    public $r2;
     
    /**
     *
     * @var double
     */
    public $r3;
     
    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'symbol' => 'symbol', 
            'period' => 'period', 
            'name' => 'name', 
            's3' => 's3', 
            's2' => 's2', 
            's1' => 's1', 
            'pivotPoints' => 'pivotPoints', 
            'r1' => 'r1', 
            'r2' => 'r2', 
            'r3' => 'r3'
        );
    }

}

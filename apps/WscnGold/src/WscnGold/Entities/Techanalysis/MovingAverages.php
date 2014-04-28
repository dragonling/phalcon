<?php

namespace WscnGold\Entities\Techanalysis;

class MovingAverages extends \Eva\EvaEngine\Mvc\Model
{
    protected $tableName = 'techanalysis_moving_averages';

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
    public $maPeriod;
     
    /**
     *
     * @var double
     */
    public $maSimpleValue;
     
    /**
     *
     * @var string
     */
    public $maSimpleAction;
     
    /**
     *
     * @var double
     */
    public $maExponentialValue;
     
    /**
     *
     * @var string
     */
    public $maExponentialAction;
     
    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'symbol' => 'symbol', 
            'period' => 'period', 
            'maPeriod' => 'maPeriod', 
            'maSimpleValue' => 'maSimpleValue', 
            'maSimpleAction' => 'maSimpleAction', 
            'maExponentialValue' => 'maExponentialValue', 
            'maExponentialAction' => 'maExponentialAction'
        );
    }

}

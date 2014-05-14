<?php

namespace WscnGold\Entities\Techanalysis;

class Summaries extends \Eva\EvaEngine\Mvc\Model
{
    protected $tableName = 'techanalysis_summaries';

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
     * @var string
     */
    public $action;

    /**
     *
     * @var integer
     */
    public $buy;

    /**
     *
     * @var integer
     */
    public $sell;

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
            'action' => 'action',
            'buy' => 'buy',
            'sell' => 'sell'
        );
    }

}

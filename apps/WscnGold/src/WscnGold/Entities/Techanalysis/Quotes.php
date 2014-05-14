<?php

namespace WscnGold\Entities\Techanalysis;

class Quotes extends \Eva\EvaEngine\Mvc\Model
{
    protected $tableName = 'techanalysis_quotes';

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
    public $status;

    /**
     *
     * @var string
     */
    public $title;

    /**
     *
     * @var string
     */
    public $type;

    /**
     *
     * @var string
     */
    public $tag;

    /**
     *
     * @var string
     */
    public $period1m;

    /**
     *
     * @var string
     */
    public $period5m;

    /**
     *
     * @var string
     */
    public $period15m;

    /**
     *
     * @var string
     */
    public $period30m;

    /**
     *
     * @var string
     */
    public $period1h;

    /**
     *
     * @var string
     */
    public $period5h;

    /**
     *
     * @var string
     */
    public $period1d;

    /**
     *
     * @var string
     */
    public $periodmn;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id',
            'symbol' => 'symbol',
            'status' => 'status',
            'title' => 'title',
            'type' => 'type',
            'tag' => 'tag',
            'period1m' => 'period1m',
            'period5m' => 'period5m',
            'period15m' => 'period15m',
            'period30m' => 'period30m',
            'period1h' => 'period1h',
            'period5h' => 'period5h',
            'period1d' => 'period1d',
            'periodmn' => 'periodmn'
        );
    }

    public function initialize()
    {
        $this->hasMany(
            'symbol',
            'WscnGold\Entities\Techanalysis\Summaries',
            'symbol',
            array('alias' => 'Summaries')
        );

        $this->hasMany(
            'symbol',
            'WscnGold\Entities\Techanalysis\PivotPoints',
            'symbol',
            array('alias' => 'PivotPoints')
        );

        $this->hasMany(
            'symbol',
            'WscnGold\Entities\Techanalysis\MovingAverages',
            'symbol',
            array('alias' => 'MovingAverages')
        );

        $this->hasMany(
            'symbol',
            'WscnGold\Entities\Techanalysis\TechnicalIndicators',
            'symbol',
            array('alias' => 'TechnicalIndicators')
        );
    }

}

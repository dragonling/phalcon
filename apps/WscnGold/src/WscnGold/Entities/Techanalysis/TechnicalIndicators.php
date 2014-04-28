<?php


namespace WscnGold\Entities\Techanalysis;

class TechnicalIndicators extends \Eva\EvaEngine\Mvc\Model
{
    protected $tableName = 'techanalysis_technical_indicators';

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
    public $tiSymbol;
     
    /**
     *
     * @var double
     */
    public $tiValue;
     
    /**
     *
     * @var string
     */
    public $tiAction;
     
    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'symbol' => 'symbol', 
            'period' => 'period', 
            'tiSymbol' => 'tiSymbol', 
            'tiValue' => 'tiValue', 
            'tiAction' => 'tiAction'
        );
    }

}

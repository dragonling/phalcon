<?php

namespace WscnGold\Controllers;


use WscnGold\Entities\Techanalysis\Quotes;

class TechanalysisController extends ControllerBase
{
    public function indexAction()
    {
        $period = $this->dispatcher->getParam('period');
        $period = $period ? $period : '1h';
        $period = in_array($period, array('1m', '5m', '15m', '30m', '1h', '5h', '1d', 'mn')) ? $period : '1h';
        $symbol = $this->dispatcher->getParam('symbol');
        $symbol = !$symbol || $symbol == 'index' ? 'XAUUSD' : $symbol;
        $periodkey =  "period$period";
        $quotes = Quotes::find(array(
            'columns' => array(
                'id',
                'title',
                'symbol',
            )
        ));
        $this->view->setVar('quotes', $quotes);

        $quote = Quotes::findFirst(array(
            "columns" => array(
                "id",
                "title",
                $periodkey,
                'symbol',
            ),
            "conditions" => "symbol = :symbol:",
            "bind"       => array('symbol' => $symbol)
        ));
        $data = array();
        $this->view->setVar('quote', $quote);
        if(!empty($quote->$periodkey)) {
            $data = json_decode($quote->$periodkey, true);
        }
        $this->view->setVar('data', $data);
        /*
        $data = $quote->dump(array(
            'symbol',
            'title',
            'updateTime',
            'Summaries' => array(
                'period',
                'name',
                'action',
                'buy',
                'sell',
            ),
            'MovingAverages' => array(
                'period',
                'maPeriod',
                'maSimpleValue',
                'maSimpleAction',
                'maExponentialValue',
                'maExponentialAction',
            ),
            'TechnicalIndicators' => array(
                'period',
                'tiSymbol',
                'tiValue',
                'tiAction',
            ),
        ));
        $reGroup = function($data, $groupBy = 'period') {
            $newData = array();
            foreach($data as $key => $value) {
                $newData[$value[$groupBy]][] = $value;
            }
            return $newData;
        };
        $data['Summaries'] = $reGroup($data['Summaries']);
        $data['MovingAverages'] = $reGroup($data['MovingAverages']);
        $data['TechnicalIndicators'] = $reGroup($data['TechnicalIndicators']);
        */
    }
}

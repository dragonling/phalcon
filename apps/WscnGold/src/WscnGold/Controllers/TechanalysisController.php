<?php

namespace WscnGold\Controllers;


use WscnGold\Entities\Techanalysis\Quotes;

class TechanalysisController extends ControllerBase
{
    public function indexAction()
    {
        $quote = Quotes::findFirstBySymbol('XAGUSD');
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
    }
}

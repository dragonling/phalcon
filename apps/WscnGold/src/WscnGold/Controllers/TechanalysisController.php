<?php

namespace WscnGold\Controllers;

use WscnGold\Entities\Techanalysis\Quotes;

class TechanalysisController extends ControllerBase
{
    protected function getSuggestion($input)
    {
        $input = trim(strtolower($input));
        $mapping = array(
            'buy' => '买入',
            'sell' => '卖出',
            'overbought' => '超买',
            'oversold' => '超卖',
            'neutral' => '中性',
            'strong buy' => '积极买入',
            'strong sell' => '积极卖出',
            'less volatility' => '低波动',
            'high volatility' => '高波动',
        );

        return isset($mapping[$input]) ? $mapping[$input] : $input;
    }

    protected function getData($json)
    {
        $data = json_decode($json, true);
        $data['summary']['datas']['MovingAverages']['action'] = $this->getSuggestion($data['summary']['datas']['MovingAverages']['action']);
        $data['summary']['datas']['TechnicalIndicators']['action'] = $this->getSuggestion($data['summary']['datas']['TechnicalIndicators']['action']);
        $data['summary']['conclusion']['action'] = $this->getSuggestion($data['summary']['conclusion']['action']);
        foreach ($data['technicalIndicators']['datas'] as $key => $value) {
            $data['technicalIndicators']['datas'][$key]['action'] = $this->getSuggestion($data['technicalIndicators']['datas'][$key]['action']);
        }

        foreach ($data['movingAverages']['datas'] as $key => $value) {
            $data['movingAverages']['datas'][$key]['simpleAction'] = $this->getSuggestion($data['movingAverages']['datas'][$key]['simpleAction']);
            $data['movingAverages']['datas'][$key]['exponentialAction'] = $this->getSuggestion($data['movingAverages']['datas'][$key]['exponentialAction']);
        }

        $data['technicalIndicators']['conclusion']['summary'] = $this->getSuggestion($data['technicalIndicators']['conclusion']['summary']);
        $data['movingAverages']['conclusion']['summary'] = $this->getSuggestion($data['movingAverages']['conclusion']['summary']);

        return $data;
    }

    public function quoteAction()
    {
        $period = $this->dispatcher->getParam('period');
        $period = $period ? $period : '1h';
        $period = in_array($period, array('1m', '5m', '15m', '30m', '1h', '5h', '1d', 'mn')) ? $period : '1h';
        $symbol = $this->dispatcher->getParam('symbol');
        $symbol = !$symbol || $symbol == 'index' ? 'XAUUSD' : $symbol;
        $periodkey =  "period$period";
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
        if (!empty($quote->$periodkey)) {
            $quote->data = $this->getData($quote->$periodkey);
            unset($quote->$periodkey);
        }
        $this->response->setContentType('application/json', 'utf-8');
        $callback = $this->request->getQuery('callback');
        if ($callback) {
            $this->response->setJsonContent($quote);

            return $this->response->setContent($callback . '(' . $this->response->getContent() . ')');
        }

        return $this->response->setJsonContent($quote);
    }

    public function indexAction()
    {
        //$period = $this->dispatcher->getParam('period');
        //$period = $period ? $period : '1h';
        //$period = in_array($period, array('1m', '5m', '15m', '30m', '1h', '5h', '1d', 'mn')) ? $period : '1h';
        $period = '1h';
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
            /*
            "columns" => array(
                "id",
                "title",
                $periodkey,
                'symbol',
            ),
            */
            "conditions" => "symbol = :symbol:",
            "bind"       => array('symbol' => $symbol)
        ));
        $data = array();
        $this->view->setVar('quote', $quote);
        $periods = array('period1m', 'period5m', 'period15m', 'period30m', 'period1h', 'period5h', 'period1d', 'periodmn');
        foreach ($periods as $periodkey) {
            if (!empty($quote->$periodkey)) {
                $data[$periodkey] = $this->getData($quote->$periodkey);
            } else {
                $data[$periodkey] = null;
            }
        }
        $this->view->setVar('dataArray', $data);
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
        $reGroup = function ($data, $groupBy = 'period') {
            $newData = array();
            foreach ($data as $key => $value) {
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

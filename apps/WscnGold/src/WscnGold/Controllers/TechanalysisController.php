<?php

namespace WscnGold\Controllers;


use WscnGold\Entities\Techanalysis\Quotes;

class TechanalysisController extends ControllerBase
{
    public function indexAction()
    {
        $quote = Quotes::findBySymbol('XAGUSD');
    }
}

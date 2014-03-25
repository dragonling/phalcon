<?php

namespace Eva\EvaEngine;

class Tag extends \Phalcon\Tag
{
    static public function _($message = null, $replacement = null)
    {
        $translate = self::getDI()->get('translate');
        if($message) {
            return $translate->_($message, $replacement);
        }
        return $translate;
    }
}

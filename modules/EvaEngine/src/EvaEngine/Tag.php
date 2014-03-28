<?php

namespace Eva\EvaEngine;

class Tag extends \Phalcon\Tag
{
    public static function _($message = null, $replacement = null)
    {
        $translate = self::getDI()->get('translate');
        if($message) {
            return $translate->_(trim($message), $replacement);
        }
        return $translate;
    }

    public static function flashOutput()
    {
        $flash = self::getDI()->get('flash');
        if(!$flash) {
            return '';
        }
        $messages = $flash->getMessages();
        $classMapping = array(
            'error' => 'alert alert-danger',
            'warning' => 'alert alert-warning',
            'success' => 'alert alert-success',
            'notice' => 'alert alert-info',
        );

        $messageString = '';
        $escaper = self::getDI()->get('escaper');
        foreach($messages as $type => $submessages) {
            foreach($submessages as $message) {
                $messageString .= '<div class="alert ' . $classMapping[$type] . '" data-raw-message="' . $escaper->escapeHtmlAttr($message) . '">' . self::_($message) . '</div>';
            }
        }
        return $messageString;

        /*
        <?if($this->flash):?>
        <?$messages = $this->flash->getMessages();?>
        <?$classMapping = array(
            'error' => 'alert alert-danger',
            'warning' => 'alert alert-warning',
            'success' => 'alert alert-success',
            'notice' => 'alert alert-info',
        );?>
        <?foreach($messages as $type => $submessages):?>
        <?foreach($submessages as $message):?>
        <div class="alert <?=$classMapping[$type]?>" data-raw-message="<?=$this->escaper->escapeHtml($message);?>"><?=$this->tag->_($message)?></div>
        <?endforeach?>
        <?endforeach?>
        <?endif?>
        */
    }

    /**
    * Get either a Gravatar URL or complete image tag for a specified email address.
    *
    * @param string $email The email address
    * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
    * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
    * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
    * @param boole $img True to return a complete IMG tag False for just the URL
    * @param array $atts Optional, additional key/value attributes to include in the IMG tag
    * @return String containing either just a URL or a complete image tag
    * @source http://gravatar.com/site/implement/images/php/
    */
    public static function gravatar( $email, $s = 80, $d = 'mm', $r = 'g') {
        $url = 'http://www.gravatar.com/avatar/';
        $url .= md5( strtolower( trim( $email ) ) );
        $url .= "?s=$s&d=$d&r=$r";
        return $url;
    }

}

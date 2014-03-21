<?php
/**
 * Eva\EvaEngine
 */

namespace Eva\EvaEngine\Exception;

class StandardException extends \Phalcon\Exception implements ExceptionInterface
{
    protected $statusCode = 500;

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public __construct ($message, $code = 10000, $previous = null, $statusCode = null)
    {
        if($statusCode) {
            $this->statusCode = $statusCode;
        }
        parent::__construct($message, $code, $previous);
    }

}

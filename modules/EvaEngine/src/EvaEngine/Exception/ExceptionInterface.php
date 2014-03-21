<?php
/**
 * Eva\EvaEngine
 */

namespace Eva\EvaEngine\Exception;

interface ExceptionInterface
{
    protected $statusCode;

    public function getStatusCode();
}

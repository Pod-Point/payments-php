<?php

namespace PodPoint\Payments\Exceptions;

class RefundException extends \Exception
{
    /**
     * @param \Throwable|null $previous
     */
    public function __construct($message, $code, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

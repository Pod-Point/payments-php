<?php

namespace PodPoint\Payments\Exceptions;

class PaymentException extends \Exception
{
    /**
     * @param string $message
     * @param \Throwable|null $previous
     */
    public function __construct(string $message, \Throwable $previous = null)
    {
        parent::__construct($message, 1, $previous);
    }
}

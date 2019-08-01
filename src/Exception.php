<?php

namespace PodPoint\Payments;

class Exception extends \Exception
{
    /**
     * @param \Throwable|null $previous
     */
    public function __construct(\Throwable $previous = null)
    {
        parent::__construct('Failed to create payment due to provider exception', 1, $previous);
    }
}

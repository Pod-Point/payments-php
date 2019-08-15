<?php

namespace PodPoint\Payments\Providers\Stripe\Payment;

use PodPoint\Payments\Token;

class Exception extends \Exception
{
    /**
     * The response from the Stripe API.
     *
     * @var Token
     */
    private $response;

    /**
     * @param Token $response
     * @param \Throwable|null $previous
     */
    public function __construct($response, \Throwable $previous = null)
    {
        parent::__construct('Failed to create payment due to Stripe API response', 1, $previous);

        $this->response = $response;
    }

    /**
     * Returns the response from the Stripe API.
     *
     * @return Token
     */
    public function getResponse()
    {
        return $this->response;
    }
}

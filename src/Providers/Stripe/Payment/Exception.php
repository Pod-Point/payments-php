<?php

namespace PodPoint\Payments\Providers\Stripe\Payment;

use Stripe\PaymentIntent;

class Exception extends \Exception
{
    /**
     * The response from the Stripe API.
     *
     * @var PaymentIntent
     */
    private $response;

    /**
     * @param PaymentIntent $response
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
     * @return PaymentIntent
     */
    public function getResponse()
    {
        return $this->response;
    }
}

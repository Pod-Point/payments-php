<?php

namespace PodPoint\Payments\Providers\Stripe;

use Stripe\PaymentIntent;
use Stripe\SetupIntent;

class Exception extends \Exception
{
    /**
     * The response from the Stripe API.
     *
     * @var PaymentIntent|SetupIntent
     */
    private $response;

    /**
     * @param PaymentIntent $response
     * @param \Throwable|null $previous
     */
    public function __construct($response, \Throwable $previous = null)
    {
        parent::__construct('Failed to create payment due to Stripe API response', 2, $previous);

        $this->response = $response;
    }

    /**
     * Returns the response from the Stripe API.
     *
     * @return PaymentIntent|SetupIntent
     */
    public function getResponse()
    {
        return $this->response;
    }
}

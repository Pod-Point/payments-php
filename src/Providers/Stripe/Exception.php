<?php

namespace PodPoint\Payments\Providers\Stripe;

class Exception extends \Exception
{
    /**
     * The response from the Stripe API.
     *
     * @var mixed
     */
    private $response;

    /**
     * @param mixed $response
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
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }
}

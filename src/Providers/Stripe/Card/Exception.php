<?php

namespace PodPoint\Payments\Providers\Stripe\Card;

use Stripe\SetupIntent;

class Exception extends \Exception
{
    /**
     * The response from the Stripe API.
     *
     * @var SetupIntent
     */
    private $response;

    /**
     * @param SetupIntent $response
     * @param \Throwable|null $previous
     */
    public function __construct($response, \Throwable $previous = null)
    {
        parent::__construct('Failed to create a new card due to Stripe API response', 1, $previous);

        $this->response = $response;
    }

    /**
     * Returns the response from the Stripe API.
     *
     * @return SetupIntent
     */
    public function getResponse()
    {
        return $this->response;
    }
}

<?php

namespace PodPoint\Payments\Providers\Stripe\Payment;

use PodPoint\Payments\Token;

class Exception extends \Exception
{
    /**
     * The response from the API.
     *
     * @var Token
     */
    private $response;

    /**
     * @param Token $response
     * @param \Throwable|null $previous
     */
    public function __construct(Token $response, \Throwable $previous = null)
    {
        parent::__construct('Failed to create payment due to Stripe API response.', 1, $previous);

        $this->response = $response;
    }

    /**
     * Returns the response from the API.
     *
     * @return Token
     */
    public function getResponse(): Token
    {
        return $this->response;
    }
}

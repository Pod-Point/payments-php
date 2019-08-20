<?php

namespace PodPoint\Payments\Providers\Stripe\Card;

use PodPoint\Payments\Token;
use PodPoint\Payments\ExceptionInterface;

class Exception extends \Exception implements ExceptionInterface
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
    public function __construct($response, \Throwable $previous = null)
    {
        parent::__construct('Failed to create a new card due to Stripe API response', 1, $previous);

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

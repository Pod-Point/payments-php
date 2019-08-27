<?php

namespace PodPoint\Payments\Providers\Stripe\Card;

use PodPoint\Payments\Token;

class Exception extends \Exception
{
    /**
     * The token returned from the Stripe API.
     *
     * @var Token
     */
    private $token;

    /**
     * @param Token $token
     * @param \Throwable|null $previous
     */
    public function __construct(Token $token, \Throwable $previous = null)
    {
        parent::__construct('Failed to create a new card due to Stripe API response', 1, $previous);

        $this->token = $token;
    }

    /**
     * Returns the token returned from the Stripe API.
     *
     * @return Token
     */
    public function getToken(): Token
    {
        return $this->token;
    }
}

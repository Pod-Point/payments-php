<?php

namespace PodPoint\Payments;

abstract class Token
{
    /**
     * The token.
     *
     * @var string
     */
    public $value;

    /**
     * The type of token.
     *
     * @var string
     */
    public $type;

    /**
     * @param string $value
     * @param string|null $type
     */
    public function __construct(string $value, ?string $type = null)
    {
        $this->value = $value;
        $this->type = $this->getTokenType($type);
    }

    /**
     * Returns StripeToken type base on incoming token.
     *
     * @param string|null $token
     *
     * @return string|null
     */
    protected abstract function getTokenType(?string $token): ?string;
}

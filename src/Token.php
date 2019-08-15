<?php

namespace PodPoint\Payments;

use PodPoint\Payments\Providers\Stripe\Token as StripeToken;

class Token
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
     */
    public function __construct(string $value)
    {
        $this->value = $value;
        $this->type = $this->getTokenType();
    }

    /**
     * Returns StripeToken type base on incoming token.
     *
     * @return string
     */
    private function getTokenType(): string
    {
        if (strpos($this->value, 'secret') !== false) {
            return StripeToken::SECRET;
        }

        switch (true) {
            case $this->startsWith('pi'):
                return StripeToken::PAYMENT_INTENT;
            case $this->startsWith('pm'):
                return StripeToken::PAYMENT_METHOD;
            case $this->startsWith('card'):
                return StripeToken::CARD;
            case $this->startsWith('cus'):
                return StripeToken::CUSTOMER;
            case $this->startsWith('ch'):
                return StripeToken::CHARGE;
            case $this->startsWith('seti'):
                return StripeToken::SETUP_INTENT;
            case $this->startsWith('tok'):
                return StripeToken::TOKEN;
            default:
                return StripeToken::UNDEFINED;
        }
    }

    /**
     * Checks chars from the beginning of the token.
     *
     * @param string $needle
     * @param string|null $token
     *
     * @return bool
     */
    private function startsWith(string $needle, string $token = null): bool
    {
        $length = strlen($needle);
        return (substr(trim($token ?? $this->value), 0, $length) === $needle);
    }

    /**
     * Identifies card token.
     *
     * @param string $token
     *
     * @return bool
     */
    public function isCard(string $token): bool
    {
        return $this->startsWith('card', $token);
    }
}

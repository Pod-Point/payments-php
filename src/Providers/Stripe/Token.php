<?php

namespace PodPoint\Payments\Providers\Stripe;

use PodPoint\Payments\Providers\Stripe\Token as StripeToken;

class Token extends \PodPoint\Payments\Token
{
    const CUSTOMER = 'customer';
    const PAYMENT_INTENT = 'payment_intent_id';
    const PAYMENT_METHOD = 'payment_method';
    const CHARGE = 'charge';
    const CARD = 'card';

    /**
     * Returns StripeToken type base on incoming token.
     *
     * @param string|null
     *
     * @return string|null
     */
    protected function getTokenType(?string $token): ?string
    {
        switch (true) {
            case $this->startsWith('pi', $token):
                return StripeToken::PAYMENT_INTENT;
            case $this->startsWith('pm', $token):
                return StripeToken::PAYMENT_METHOD;
            case $this->startsWith('cus', $token):
                return StripeToken::CUSTOMER;
            case $this->startsWith('ch', $token):
                return StripeToken::CHARGE;
            case $this->startsWith('card', $token):
                return StripeToken::CARD;
            default:
                return null;
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
        $type = $this->getTokenType($token);
        return $type === self::CARD;
    }
}

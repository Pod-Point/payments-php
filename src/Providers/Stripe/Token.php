<?php

namespace PodPoint\Payments\Providers\Stripe;

class Token extends \PodPoint\Payments\Token
{
    const CUSTOMER = 'customer';
    const TOKEN = 'token';
    const PAYMENT_INTENT = 'payment_intent_id';
    const SECRET_PAYMENT_INTENT = 'secret_payment_intent';
    const PAYMENT_METHOD = 'payment_method';
    const SETUP_INTENT = 'setup_intent_id';
    const SECRET_SETUP_INTENT = 'secret_setup_intent';
    const SETUP_CARD_CREATION = 'setup_card_creation';
    const CARD = 'card';
    const CHARGE = 'charge';
    const SETUP = 'setup';

    /**
     * Token constructor.
     *
     * @param string $value
     * @param string|null $type
     */
    public function __construct(string $value, ?string $type = null)
    {
        parent::__construct($value, $type);

        $this->type = $this->getTokenType($value);
    }

    /**
     * Determines and returns the token type from it's value.
     *
     * @param string
     *
     * @return string|null
     */
    protected function getTokenType(): ?string
    {
        if (strpos($this->value, 'secret') !== false) {
            switch (true) {
                case $this->startsWith('pi'):

                    return self::SECRET_PAYMENT_INTENT;
                case $this->startsWith('seti'):

                    return self::SECRET_SETUP_INTENT;
                default:
                    throw new \Exception('Unknown type of secret token');
            }
        }

        switch (true) {
            case $this->startsWith('pi'):

                return self::PAYMENT_INTENT;
            case $this->startsWith('pm'):

                return self::PAYMENT_METHOD;
            case $this->startsWith('card'):

                return self::CARD;
            case $this->startsWith('cus'):

                return self::CUSTOMER;
            case $this->startsWith('ch'):

                return self::CHARGE;
            case $this->startsWith('seti'):

                return self::SETUP_INTENT;
            case $this->startsWith('tok'):

                return self::TOKEN;
            case $this->startsWith('setup'):

                return self::SETUP_CARD_CREATION;
            default:
                throw new \Exception('Unknown type of token');
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
}

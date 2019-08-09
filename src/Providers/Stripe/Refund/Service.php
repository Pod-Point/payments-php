<?php

namespace PodPoint\Payments\Providers\Stripe\Refund;

use PodPoint\Payments\Refund;
use PodPoint\Payments\Providers\Stripe\Token as StripeToken;
use PodPoint\Payments\Token;
use Stripe\Charge;
use Stripe\PaymentIntent;

class Service
{
    /**
     * Creates Refund based on incoming token type chargeId|paymentIntentId.
     *
     * @param Token $token
     * @param int $amount
     * @param string $reason
     * @param array $metadata
     *
     * @return Refund
     */
    public function create(Token $token, int $amount, string $reason, array $metadata): Refund
    {
        switch ($token->type) {
            case StripeToken::PAYMENT_INTENT:
                $refund = PaymentIntent::retrieve($token);

                /** @var Charge $charge */
                $charge = $refund->charges->data[0];
                $charge->refund([
                    'amount' => $amount,
                    'reason' => $reason,
                    'metadata' => $metadata,
                ]);
                break;
            case StripeToken::CUSTOMER:
                $refund = \Stripe\Refund::create([
                    'charge' => $token,
                    'amount' => $amount,
                    'reason' => $reason,
                    'metadata' => $metadata,
                ]);
                break;
        }

        return new Refund($refund->id);
    }
}

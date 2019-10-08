<?php

namespace PodPoint\Payments\Providers\Stripe\Refund;

use PodPoint\Payments\Refund;
use PodPoint\Payments\Providers\Stripe\Token as StripeToken;
use PodPoint\Payments\Token;
use Stripe\Charge;
use Stripe\PaymentIntent;
use PodPoint\Payments\Refund\Service as RefundServiceInterface;

class Service implements RefundServiceInterface
{
    /**
     * Tries to perform a refund using the Stripe SDK.
     *
     * @param Token $token
     * @param int $amount
     * @param string $reason
     * @param array $metadata
     *
     * @return Refund
     *
     * @throws \Stripe\Error\Api
     */
    public function create(Token $token, int $amount, string $reason, array $metadata): Refund
    {
        switch ($token->type) {
            case StripeToken::PAYMENT_INTENT:
                $paymentIntent = PaymentIntent::retrieve($token->value);

                /** @var Charge $charge */
                $charge = $paymentIntent->charges->data[0];

                $chargeId = $charge->id;

                break;
            case StripeToken::CHARGE:
            default:
                $chargeId = $token->value;

                break;
        }

        $refund = \Stripe\Refund::create([
            'charge' => $chargeId,
            'amount' => $amount,
            'reason' => $reason,
            'metadata' => $metadata,
        ]);

        return new Refund($refund->id);
    }
}

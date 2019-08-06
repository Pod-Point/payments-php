<?php

namespace PodPoint\Payments\Providers\Stripe;

use PodPoint\Payments\Exception;
use PodPoint\Payments\Payment;
use PodPoint\Payments\Providers\Stripe\Exception as StripeException;
use PodPoint\Payments\Service as ServiceInterface;
use Stripe\PaymentIntent;

class Service extends Base implements ServiceInterface
{
    /**
     * Tries make a payment using the Stripe SDK.
     *
     * @param string $token
     * @param int $amount
     * @param string $currency
     *
     * @return Payment
     *
     * @throws Exception
     * @throws StripeException
     */
    public function create(string $token, int $amount, string $currency = 'GBP'): Payment
    {
        try {
            $response = PaymentIntent::create([
                'payment_method' => $token,
                'amount' => $amount,
                'currency' => $currency,
                'confirmation_method' => 'manual',
                'confirm' => true,
            ]);
        } catch (\Exception $exception) {
            throw new Exception($exception);
        }

        if ($response->status !== PaymentIntent::STATUS_SUCCEEDED) {
            throw new StripeException($response);
        }

        return new Payment($response->id, $response->currency, $response->amount, $response->created);
    }

    /**
     * Tries update a payment using the Stripe SDK.
     *
     * @param string $uid
     *
     * @return Payment
     *
     * @throws Exception
     * @throws StripeException
     */
    public function update(string $uid): Payment
    {
        try {
            $response = PaymentIntent::retrieve($uid);
            $response->confirm();
        } catch (\Exception $exception) {
            throw new Exception($exception);
        }

        if ($response->status !== PaymentIntent::STATUS_SUCCEEDED) {
            throw new StripeException($response);
        }

        return new Payment($response->id, $response->currency, $response->amount, $response->created);
    }
}

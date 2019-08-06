<?php

namespace PodPoint\Payments\Providers\Stripe;

use PodPoint\Payments\Entity\Customer;
use PodPoint\Payments\Entity\Payment;
use PodPoint\Payments\Entity\Refund;
use PodPoint\Payments\Exceptions\PaymentException;
use PodPoint\Payments\Exceptions\RefundException;
use PodPoint\Payments\Providers\Stripe\Exception as StripeException;
use PodPoint\Payments\Service as ServiceInterface;
use Stripe\Charge;
use Stripe\Customer as StripeCustomer;
use Stripe\PaymentIntent;
use Stripe\Refund as StripeRefund;
use Stripe\Stripe;

class Service implements ServiceInterface
{
    /**
     * @param string $key
     */
    public function __construct(string $key)
    {
        Stripe::setApiKey($key);
    }

    /**
     * Tries make a payment using the Stripe SDK.
     *
     * @param string $token
     * @param int $amount
     * @param string|null $customerId
     * @param string|null $description
     * @param array $metadata
     * @param string $currency
     *
     * @return Payment
     *
     * @throws PaymentException
     * @throws StripeException
     */
    public function create(
        string $token,
        int $amount,
        ?string $customerId = null,
        ?string $description = null,
        array $metadata = [],
        string $currency = 'GBP'
    ): Payment {
        try {
            $response = PaymentIntent::create([
                'payment_method' => $token,
                'amount' => $amount,
                'currency' => $currency,
                'confirmation_method' => 'manual',
                'confirm' => true,
                'customer' => $customerId,
                'description' => $description,
                'metadata' => $metadata
            ]);
        } catch (\Exception $exception) {
            throw new PaymentException(
                'Failed to create payment due to provider exception',
                $exception
            );
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
     * @throws PaymentException
     * @throws StripeException
     */
    public function update(string $uid): Payment
    {
        try {
            $response = PaymentIntent::retrieve($uid);
            $response->confirm();
        } catch (\Exception $exception) {
            throw new PaymentException(
                'Failed to update payment due to provider exception',
                $exception
            );
        }

        if ($response->status !== PaymentIntent::STATUS_SUCCEEDED) {
            throw new StripeException($response);
        }

        return new Payment($response->id, $response->currency, $response->amount, $response->created);
    }

    /**
     * Creates Stripe customer.
     *
     * @param string $email
     * @param string $paymentMethod
     * @param string|null $description
     *
     * @return Customer
     */
    public function createCustomer(string $email, string $paymentMethod, ?string $description = null): Customer
    {
        $customer = StripeCustomer::create([
            'description' => $description,
            'email'       => $email,
            'payment_method' => $paymentMethod,
        ]);

        return new Customer($customer->id, $customer->email);
    }

    /**
     * Creates Refund for provided Payment Intent id.
     *
     * @param string $intentId
     * @param int|null $amount
     * @param string|null $reason
     * @param array|null $metadata
     *
     * @return Refund
     *
     * @throws PaymentException
     * @throws RefundException
     */
    public function refund(
        string $intentId,
        ?int $amount = null,
        ?string $reason = null,
        ?array $metadata = []
    ): Refund {
        try {
            $intent = PaymentIntent::retrieve($intentId);

            /** @var Charge $charge */
            $charge = $intent->charges->data[0];

            if (!$amount) {
                $amount = $charge->amount;
            }

            /** @var StripeRefund $refund */
            $refund = StripeRefund::create([
                'charge' => $charge->id,
                'amount' => $amount,
                'reason' => $reason,
                'metadata' => $metadata,
            ]);
        } catch (\Exception $exception) {
            throw new PaymentException(
                'Failed to refund payment due to provider exception',
                $exception
            );
        }

        if ($refund->status !== StripeRefund::STATUS_SUCCEEDED) {
            throw new RefundException(
                "Reason: {$refund->failure_reason}, Status: {$refund->status}"
            );
        }

        return new Refund($refund->id);
    }
}

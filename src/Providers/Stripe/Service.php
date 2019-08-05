<?php

namespace PodPoint\Payments\Providers\Stripe;

use PodPoint\Payments\Entity\Customer;
use PodPoint\Payments\Entity\Payment;
use PodPoint\Payments\Exception;
use PodPoint\Payments\Providers\Stripe\Exception as StripeException;
use PodPoint\Payments\Service as ServiceInterface;
use Stripe\Customer as StripeCustomer;
use Stripe\PaymentIntent;
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
     * @throws Exception
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
}

<?php

namespace PodPoint\Payments\Providers\Stripe\Payment;

use PodPoint\Payments\Card\Service as CardServiceInterface;
use PodPoint\Payments\Customer\Service as CustomerServiceInterface;
use PodPoint\Payments\Exceptions\InvalidToken;
use PodPoint\Payments\Payment;
use PodPoint\Payments\Payment\Service as ServiceInterface;
use PodPoint\Payments\Providers\Stripe\Card\Service as CardService;
use PodPoint\Payments\Providers\Stripe\Customer\Service as CustomerService;
use PodPoint\Payments\Providers\Stripe\Payment\Exception as StripeException;
use PodPoint\Payments\Providers\Stripe\Refund\Service as RefundService;
use PodPoint\Payments\Providers\Stripe\Token as StripeToken;
use PodPoint\Payments\Refund\Service as RefundServiceInterface;
use PodPoint\Payments\Token;
use Stripe\Charge;
use Stripe\Error\InvalidRequest;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class Service implements ServiceInterface
{
    /**
     * @var string
     */
    const CANCELLATION_ABANDONED = 'abandoned';

    /**
     * @param string $key
     */
    public function __construct(string $key)
    {
        Stripe::setApiKey($key);
    }

    /**
     * @var array
     */
    const CANCELLABLE_PAYMENT_INTENT_STATUSES = array(
        'requires_payment_method',
        'requires_capture',
        'requires_confirmation',
        'requires_action',
        'processing'
    );

    /**
     * Tries to make a payment using the Stripe SDK.
     *
     * @param Token $token
     * @param int $amount
     * @param string $currency
     * @param string|null $description
     * @param array $metadata
     * @param string|null $customerUid
     *
     * @return Payment
     *
     * @throws Exception
     * @throws \Stripe\Error\Api
     */
    public function create(
        Token $token,
        int $amount,
        string $currency = 'GBP',
        string $description = null,
        array $metadata = [],
        string $customerUid = null,
        array $params = []
    ): Payment {
        switch ($token->type) {
            case StripeToken::CUSTOMER:
                $cards = $this->customers()->getCards($token->value);
                $card = $cards[0];

                /** @var PaymentIntent $response */
                $response = PaymentIntent::create([
                    'payment_method' => $card->uid,
                    'customer' => $token->value,
                    'amount' => $amount,
                    'currency' => $currency,
                    'confirmation_method' => 'manual',
                    'confirm' => true,
                    'payment_method_types' => ['card'],
                    'description' => $description,
                    'metadata' => $metadata,
                    'use_stripe_sdk' => true,
                ]);

                break;
            case StripeToken::PAYMENT_INTENT:
                /** @var PaymentIntent $response */
                $response = PaymentIntent::retrieve($token->value);
                $response->confirm();

                break;
            case StripeToken::PAYMENT_METHOD:
            case StripeToken::CARD:
                /** @var PaymentIntent $response */
                $response = PaymentIntent::create(array_merge(
                    [
                        'payment_method' => $token->value,
                        'customer' => $customerUid,
                        'amount' => $amount,
                        'currency' => $currency,
                        'confirmation_method' => 'manual',
                        'confirm' => true,
                        'payment_method_types' => ['card'],
                        'description' => $description,
                        'metadata' => $metadata,
                        'use_stripe_sdk' => true,
                    ],
                    $params
                ));

                break;
            case StripeToken::CHARGE:
            default:
                /** @var Charge $response */
                $response = Charge::create([
                    'amount' => $amount,
                    'currency' => $currency,
                    'source' => $token->value,
                    'metadata' => $metadata,
                ]);

                break;
        }

        if ($response instanceof PaymentIntent) {
            $requiresCapture = $response->status === PaymentIntent::STATUS_REQUIRES_CAPTURE;
            $succeeded = $response->status !== PaymentIntent::STATUS_SUCCEEDED;

            if (!$requiresCapture && $succeeded) {
                $token = new StripeToken($response->client_secret);

                throw new StripeException($token);
            }
        }

        return new Payment($response->id, $response->amount, $response->currency, $response->created);
    }

    /**
     * Tries to reserve funds on a payment method using the Stripe SDK.
     *
     * @param Token $token
     * @param int $amount
     * @param string $currency
     * @param array $params
     *
     * @return Payment
     *
     * @throws Exception|\Stripe\Error\Api
     */
    public function reserve(
        Token $token,
        int $amount,
        string $currency = 'GBP',
        array $params = []
    ): Payment {
        $params['capture_method'] = 'manual';

        return $this->create(
            $token,
            $amount,
            $currency,
            $params['description'] ?? null,
            $params['metadata'] ?? [],
            $params['customer'] ?? null,
            $params
        );
    }

    public function retrievePaymentIntent(string $token): PaymentIntent
    {
        return PaymentIntent::retrieve($token);
    }

    /**
     * Tries to capture funds on a payment intent using the Stripe SDK.
     *
     * @param Token $token
     * @param int $amount
     *
     * @return Payment
     *
     * @throws AmountTooLarge
     * @throws Canceled
     * @throws InvalidRequest
     * @throws InvalidToken
     */
    public function capture(Token $token, int $amount): Payment
    {
        if ($token->type === StripeToken::PAYMENT_INTENT) {
            $intent = $this->retrievePaymentIntent($token->value);
            if ($intent->status == 'canceled') {
                throw new Canceled();
            }

            try {
                $response = $intent->capture([
                    'amount_to_capture' => $amount,
                ]);

                return new Payment($response->id, $response->amount, $response->currency, $response->created);
            } catch (InvalidRequest $exception) {
                if ($exception->getStripeCode() === 'amount_too_large') {
                    throw new AmountTooLarge($intent->amount_capturable);
                }

                throw $exception;
            }
        }

        throw new InvalidToken(
            "Provided token type: $token->type is invalid, use " . StripeToken::PAYMENT_INTENT . " type"
        );
    }

    /**
     * Tries to cancel a payment.
     *
     * @param Token $token
     *
     * @return Payment
     *
     * @throws InvalidToken
     * @throws AlreadyCanceled
     */
    public function cancel(Token $token): Payment
    {
        if ($token->type === StripeToken::PAYMENT_INTENT) {
            $intent = $this->retrievePaymentIntent($token->value);
            if (!in_array($intent->status, self::CANCELLABLE_PAYMENT_INTENT_STATUSES)) {
                throw new AlreadyCanceled();
            }

            $response = $intent->cancel([
                'cancellation_reason' => self::CANCELLATION_ABANDONED,
            ]);

            return new Payment($response->id, $response->amount, $response->currency, $response->created);
        }

        throw new InvalidToken(
            "Provided token type: $token->type is invalid, use " . StripeToken::PAYMENT_INTENT . " type"
        );
    }

    /**
     * Returns card service.
     *
     * @return CardServiceInterface
     */
    public function cards(): CardServiceInterface
    {
        return new CardService();
    }

    /**
     * Returns customer service.
     *
     * @return CustomerServiceInterface
     */
    public function customers(): CustomerServiceInterface
    {
        return new CustomerService();
    }

    /**
     * Returns refund service.
     *
     * @return RefundServiceInterface
     */
    public function refunds(): RefundServiceInterface
    {
        return new RefundService();
    }
}

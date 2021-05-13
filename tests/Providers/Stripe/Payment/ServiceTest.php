<?php

namespace PodPoint\Payments\Tests\Providers\Stripe\Payment;

use PodPoint\Payments\Exceptions\InvalidToken;
use PodPoint\Payments\Payment;
use PodPoint\Payments\Providers\Stripe\Payment\AmountTooLarge;
use PodPoint\Payments\Providers\Stripe\Payment\Service;
use PodPoint\Payments\Providers\Stripe\Token;
use PodPoint\Payments\Tests\TestCase;
use PodPoint\Payments\Providers\Stripe\Payment\Exception as StripeException;
use PodPoint\Payments\Providers\Stripe\Customer\Service as CustomerService;
use PodPoint\Payments\Providers\Stripe\Refund\Service as RefundService;

class ServiceTest extends TestCase
{
    /**
     * @var Service
     */
    private $service;

    /**
     * Creates an instance of the Stripe payment service.
     */
    public function setUp()
    {
        parent::setUp();

        $this->service = new Service(getenv('STRIPE_KEY'));
    }

    /**
     * Tests that customer service can be returned.
     */
    public function testCanReturnCustomerService()
    {
        $service = $this->service->customers();

        $this->assertInstanceOf(CustomerService::class, $service);
    }

    /**
     * Tests that refund service can be returned.
     */
    public function testCanReturnRefundService()
    {
        $service = $this->service->refunds();

        $this->assertInstanceOf(RefundService::class, $service);
    }

    /**
     * Tests that a payment can be created successfully.
     */
    public function testCreatePaymentIntent()
    {
        $paymentMethodToken = new Token('pm_card_visa');

        $customer = $this->service->customers()->create(
            $paymentMethodToken,
            'software@pod-point.com',
            'test'
        );

        $token = new Token($customer->uid);

        $this->assertEquals($token->type, Token::CUSTOMER);

        $payment = $this->service->create($token, 100);

        $this->assertInstanceOf(Payment::class, $payment);

        $paymentIntent = new Token($payment->uid);

        $this->assertEquals($paymentIntent->type, Token::PAYMENT_INTENT);
    }

    /**
     * Tests that a payment can be created successfully from old token linked to a customer.
     */
    public function testCreateChargeForBackwardCompatibility()
    {
        $sourceToken = new Token('tok_visa');

        $customer = $this->service->customers()->create(
            $sourceToken,
            'software@pod-point.com',
            'test'
        );

        $token = new Token($customer->uid);

        $this->assertEquals($token->type, Token::CUSTOMER);

        $payment = $this->service->create($token, 100);

        $this->assertInstanceOf(Payment::class, $payment);

        $charge = new Token($payment->uid);

        $this->assertEquals($charge->type, Token::PAYMENT_INTENT);
    }

    /**
     * Tests that a payment can be created successfully from old token.
     */
    public function testCreateChargeFromToken()
    {
        $sourceToken = new Token('tok_visa');

        $payment = $this->service->create($sourceToken, 100);

        $this->assertInstanceOf(Payment::class, $payment);

        $charge = new Token($payment->uid);

        $this->assertEquals($charge->type, Token::CHARGE);
    }

    /**
     * Tests that payment intent can be confirmed.
     */
    public function testPaymentIntentConfirmation()
    {
        $needAuthToken = new Token('pm_card_authenticationRequiredOnSetup');

        $customer = $this->service->customers()->create(
            $needAuthToken,
            'software@pod-point.com',
            'test'
        );

        $token = new Token($customer->uid);

        $this->expectException(StripeException::class);

        $payment = $this->service->create($token, 100);

        $confirmedPayment = $this->service->create(new Token($payment->uid), 100);

        $this->assertInstanceOf(Payment::class, $confirmedPayment);
    }

    /**
     * Tests that a payment can be created successfully with a payment method.
     */
    public function testCreatePaymentIntentWithPaymentMethod()
    {
        $paymentMethodToken = new Token('pm_card_visa');

        $customer = $this->service->customers()->create(
            $paymentMethodToken,
            'software@pod-point.com',
            'test'
        );

        $payment = $this->service->create($paymentMethodToken, 100, 'GBP', null, [], $customer->uid);

        $this->assertInstanceOf(Payment::class, $payment);

        $paymentIntent = new Token($payment->uid);

        $this->assertEquals($paymentIntent->type, Token::PAYMENT_INTENT);
    }

    /**
     * Tests that a pre authorised payment can be created successfully with a payment method.
     */
    public function testPreAuthorise()
    {
        $paymentMethodToken = new Token('pm_card_visa');

        $payment = $this->service->reserve(
            $paymentMethodToken,
            1000
        );

        $this->assertInstanceOf(Payment::class, $payment);

        $paymentToken = new Token($payment->uid);

        $this->assertEquals( Token::PAYMENT_INTENT, $paymentToken->type);
    }

    /**
     * Tests that a pre authorised payment which requires confirmation can be created successfully with a payment method.
     */
    public function testPreAuthorisePaymentIntentConfirmation()
    {
        $paymentMethodToken = new Token('pm_card_authenticationRequiredOnSetup');

        try {
            $this->service->reserve(
                $paymentMethodToken,
                1000
            );
        } catch (StripeException $exception) {
            $this->assertEquals(Token::SECRET_PAYMENT_INTENT, $exception->getToken()->type);
        }
    }

    /**
     * Tests reserved funds capture.
     */
    public function testFundsCapture()
    {
        $paymentMethodToken = new Token('pm_card_visa');

        $reserveAmount = 1000;

        $payment = $this->service->reserve(
            $paymentMethodToken,
            $reserveAmount
        );

        $token = new Token($payment->uid);

        $capturedPayment = $this->service->capture(
            $token,
            $reserveAmount
        );

        $this->assertEquals($reserveAmount, $capturedPayment->amount);
    }

    /**
     * Tests that reserved funds can be only captured using payment intent token.
     */
    public function testFundsCanBeCapturedOnlyUsingPaymentIntentToken()
    {
        $token = new Token('pm_some_other_token');

        $this->expectException(InvalidToken::class);

        $this->service->capture(
            $token,
            1000
        );
    }

    /**
     * Tests that AmountTooLarge exeption is thrown if capture amount is higher than reserved funds.
     */
    public function testCorrectExceptionIsThrownWhenCaptureAmountIsHigherThanReservedFunds()
    {
        $paymentMethodToken = new Token('pm_card_visa');

        $reserveAmount = 1000;

        $payment = $this->service->reserve(
            $paymentMethodToken,
            $reserveAmount
        );

        $token = new Token($payment->uid);

        $this->expectException(AmountTooLarge::class);

        $this->service->capture(
            $token,
            $reserveAmount + 500
        );
    }

    /**
     * Data provider of testPaymentIntentCanBeCancelled.
     *
     * @return array
     */
    public function getCancelPaymentIntentData(): array
    {
        return [
            'abandoned' => [
                Service::CANCELLATION_ABANDONED,
            ],
            'duplicate' => [
                Service::CANCELLATION_DUPLICATE,
            ],
            'fraudulent' => [
                Service::CANCELLATION_FRAUDULENT,
            ],
            'requested by customer' => [
                Service::CANCELLATION_REQUESTED_BY_CUSTOMER,
            ],
        ];
    }

    /**
     * Tests that payment intent can be cancelled.
     *
     * @param string $reason
     *
     * @dataProvider getCancelPaymentIntentData
     *
     * @throws InvalidToken
     * @throws StripeException
     * @throws \Stripe\Error\Api
     */
    public function testPaymentIntentCanBeCancelled(string $reason)
    {
        $amount = 1000;
        $paymentMethodToken = new Token('pm_card_visa');

        $payment = $this->service->reserve(
            $paymentMethodToken,
            $amount
        );

        $paymentIntentToken = new Token($payment->uid);

        $cancelledPayment = $this->service->cancel($paymentIntentToken, $reason);

        $this->assertEquals($amount, $cancelledPayment->amount);
    }

    /**
     * Tests that reserved fund can only be cancelled using payment intent token.
     *
     * @param string $reason
     *
     * @dataProvider getCancelPaymentIntentData
     */
    public function testFundsCanBeOnlyCancelledWithPaymentIntentToken(string $reason)
    {
        $token = new Token('pm_some_other_token');

        $this->expectException(InvalidToken::class);

        $this->service->cancel($token, $reason);
    }
}

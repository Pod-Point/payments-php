<?php

namespace PodPoint\Payments\Tests\Providers\Stripe\Payment;

use PodPoint\Payments\Payment;
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

        $this->assertEquals($charge->type, Token::CHARGE);
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

        $customerToken = new Token($customer->uid);

        $payment = $this->service->create($paymentMethodToken, 100, 'GBP', null, [], $customerToken);

        $this->assertInstanceOf(Payment::class, $payment);

        $paymentIntent = new Token($payment->uid);

        $this->assertEquals($paymentIntent->type, Token::PAYMENT_INTENT);
    }
}

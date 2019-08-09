<?php

namespace PodPoint\Payments\Tests\Providers\Stripe\Payment;

use PodPoint\Payments\Payment;
use PodPoint\Payments\Providers\Stripe\Payment\Service;
use PodPoint\Payments\Providers\Stripe\Token as StripeToken;
use PodPoint\Payments\Tests\TestCase;
use PodPoint\Payments\Token;
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

    public function testCanReturnCustomerService()
    {
        $service = $this->service->customers();

        $this->assertInstanceOf(CustomerService::class, $service);
    }

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
        $customerToken = new Token('pm_card_visa');

        $customer = $this->service->customers()->create(
            $customerToken,
            'john@pod-point.com',
            'test'
        );
        $token = new Token($customer->uid);

        $this->assertEquals($token->type, StripeToken::CUSTOMER);

        $payment = $this->service->create($token, 100);

        $this->assertInstanceOf(Payment::class, $payment);

        $paymentIntent = new Token($payment->uid);

        $this->assertEquals($paymentIntent->type, StripeToken::PAYMENT_INTENT);
    }

    /**
     * Tests that a payment can be created successfully from old token.
     */
    public function testCreateChargeForBackwardCompatibility()
    {
        $customerToken = new Token('tok_visa');

        $customer = $this->service->customers()->create(
            $customerToken,
            'john@pod-point.com',
            'test'
        );
        $token = new Token($customer->uid);

        $this->assertEquals($token->type, StripeToken::CUSTOMER);

        $payment = $this->service->create($token, 100);

        $this->assertInstanceOf(Payment::class, $payment);

        $charge = new Token($payment->uid);

        $this->assertEquals($charge->type, StripeToken::CHARGE);
    }

    /**
     * Tests that payment intent can be confirmed.
     */
    public function testPaymentIntentConfirmation()
    {
        $needAuthToken = new Token('pm_card_authenticationRequiredOnSetup');

        $customer = $this->service->customers()->create(
            $needAuthToken,
            'john@pod-point.com',
            'test'
        );

        $token = new Token($customer->uid);

        $this->expectException(StripeException::class);
        $payment = $this->service->create($token, 100);

        $confirmedPayment = $this->service->create(new Token($payment->uid), 100);

        $this->assertInstanceOf(Payment::class, $confirmedPayment);
    }
}

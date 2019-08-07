<?php

namespace PodPoint\Payments\Tests\Providers\Stripe;

use PodPoint\Payments\Payment;
use PodPoint\Payments\Providers\Stripe\Payment\Exception as StripeException;
use PodPoint\Payments\Providers\Stripe\Payment\Service;
use PodPoint\Payments\Providers\Stripe\Token;
use PodPoint\Payments\Tests\TestCase;
use Stripe\Customer;

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
     * Tests that a payment can be created successfully.
     */
    public function testCreatePaymentIntent()
    {
        $token = new Token('pm_card_visa', Token::PAYMENT_METHOD);

        $payment = $this->service->create($token, 100);

        $this->assertInstanceOf(Payment::class, $payment);
    }

    /**
     * Tests that an exception is thrown if the payment requires authentication.
     */
    public function testCreatePaymentIntentRequiringAuthentication()
    {
        $token = new Token('pm_card_authenticationRequired', Token::PAYMENT_METHOD);

        $this->expectException(StripeException::class);

        $this->service->create($token, 100);
    }

    /**
     * Tests that a payment can be created successfully for a given customer.
     */
    public function testCreateCustomerCharge()
    {
        $customer = Customer::create([
            'email' => 'software@pod-point.com',
            'card' => 'tok_visa',
        ]);

        $token = new Token($customer->id, Token::CUSTOMER);

        $payment = $this->service->create($token, 100);

        $this->assertInstanceOf(Payment::class, $payment);
    }
}

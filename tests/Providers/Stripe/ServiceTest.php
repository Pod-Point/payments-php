<?php

namespace PodPoint\Payments\Tests\Providers\Stripe;

use PodPoint\Payments\Entity\Customer;
use PodPoint\Payments\Entity\Payment;
use PodPoint\Payments\Entity\Refund;
use PodPoint\Payments\Exceptions\PaymentException;
use PodPoint\Payments\Providers\Stripe\Exception as StripeException;
use PodPoint\Payments\Providers\Stripe\Service;
use PodPoint\Payments\Tests\TestCase;

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
    public function testCreate()
    {
        $token = 'pm_card_visa';

        $payment = $this->service->create($token, 100);

        $this->assertInstanceOf(Payment::class, $payment);
    }

    /**
     * Tests that an exception is thrown if the payment requires authentication.
     */
    public function testCreateRequiringAuthentication()
    {
        $token = 'pm_card_authenticationRequired';

        $this->expectException(StripeException::class);

        $this->service->create($token, 100);
    }

    /**
     * Tests customer creation.
     */
    public function testCreateCustomer()
    {
        $paymentMethod = 'pm_card_visa';

        $customer = $this->service->createCustomer(
            'doe@doe.com',
            $paymentMethod
        );

        $this->assertInstanceOf(Customer::class, $customer);
    }

    /**
     * Tests refund based on PaymentIntent.
     */
    public function testRefund()
    {
        $paymentMethod = 'pm_card_visa';

        $intent = $this->service->create($paymentMethod, 100);

        $refund = $this->service->refund(
            $intent->uid,
            100
        );

        $this->assertInstanceOf(Refund::class, $refund);
    }

    /**
     * Tests refund API failure.
     */
    public function testRefundFailure()
    {
        $this->expectException(PaymentException::class);

        $this->service->refund(
            'xxx',
            100
        );
    }
}

<?php

namespace PodPoint\Payments\Tests\Providers\Stripe\Customer;

use PodPoint\Payments\Customer;
use PodPoint\Payments\Providers\Stripe\Payment\Service;
use PodPoint\Payments\Tests\TestCase;
use PodPoint\Payments\Token;

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
     * Tests if user can be created with payment method.
     */
    public function testItCanCreateCustomerWithPaymentMethod()
    {
        $customer = $this->service->customers()->create(
            new Token('pm_card_visa'),
            'john@pod-point.com',
            'test'
        );

        $this->assertInstanceOf(Customer::class, $customer);
    }

    /**
     * Tests backwards compatibility for old tokens.
     */
    public function testItCanCreateCustomerWithCardToken()
    {
        $customer = $this->service->customers()->create(
            new Token('tok_visa'),
            'john@pod-point.com',
            'test'
        );

        $this->assertInstanceOf(Customer::class, $customer);
    }
}

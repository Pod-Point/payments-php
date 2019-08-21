<?php

namespace PodPoint\Payments\Tests\Providers\Stripe\Customer;

use PodPoint\Payments\Customer;
use PodPoint\Payments\Providers\Stripe\Payment\Exception;
use PodPoint\Payments\Providers\Stripe\Payment\Service;
use PodPoint\Payments\Providers\Stripe\Token;
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
     * Tests customer can be created.
     */
    public function testCanCreateCustomer()
    {
        $customer = $this->service->customers()->create(
            new Token('pm_card_visa'),
            'software@pod-point.com',
            'test'
        );

        $this->assertInstanceOf(Customer::class, $customer);
    }

    /**
     * Tests that a customer can be created with a payment method token.
     */
    public function testItCanCreateCustomerWithPaymentMethod()
    {
        $customer = $this->service->customers()->create(
            new Token('pm_card_visa'),
            'software@pod-point.com',
            'test'
        );

        $this->assertInstanceOf(Customer::class, $customer);
    }

    /**
     * Tests can retrieve an existing customer.
     */
    public function testRetrieveCustomer()
    {
        $email = 'john@pod-point.com';
        $description = "This is $email test decription";

        $customer = $this->service->customers()->create(
            new Token('pm_card_visa'),
            $email,
            $description
        );

        $token = new Token($customer->uid);

        $customer = $this->service->customers()->find($token);

        $this->assertInstanceOf(Customer::class, $customer);
    }

    /**
     * Tests that an exception is thrown when trying to retrieve a customer with a wrong token type.
     */
    public function testRetrieveCustomerWithWrongTokenTypeThrowException()
    {
        $paymentMethodToken = new Token('pm_card_visa');

        $this->expectException(\Exception::class);

        $this->service->customers()->find($paymentMethodToken);
    }

    /**
     * Tests that a customer can be created with a card token.
     */
    public function testItCanCreateCustomerWithCardToken()
    {
        $customer = $this->service->customers()->create(
            new Token('tok_visa'),
            'software@pod-point.com',
            'test'
        );

        $this->assertInstanceOf(Customer::class, $customer);
    }

    /**
     * Tests throw exception if trying to create customer with a bad token.
     */
    public function testThrowExceptionIfBadTokenType()
    {
        $this->expectException(\Exception::class);

        $customer = $this->service->customers()->create(
            new Token('tok_token'),
            'software@pod-point.com',
            'test'
        );

        $this->assertInstanceOf(Customer::class, $customer);
    }
}

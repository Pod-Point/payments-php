<?php

namespace PodPoint\Payments\Tests\Providers\Stripe\Customer;

use PodPoint\Payments\Customer;
use PodPoint\Payments\Token;
use PodPoint\Payments\Providers\Stripe\Token as StripeToken;
use PodPoint\Payments\Providers\Stripe\Customer\Service;
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
     * Tests can create a new customer.
     */
    public function testCreateCustomer()
    {
        $email = uniqid() . '@test.test';
        $description = "This is $email test decription";

        $customer = $this->service->create($email, $description);

        $this->assertInstanceOf(Customer::class, $customer);
    }

    /**
     * Tests can retrieve an existing customer.
     */
    public function testUpdateCustomer()
    {
        $email = uniqid() . '@test.test';
        $description = "This is $email test decription";

        $customer = $this->service->create($email, $description);

        $token = new Token($customer->uid, StripeToken::CUSTOMER);

        $customer = $this->service->update($token);

        $this->assertInstanceOf(Customer::class, $customer);
    }

    /**
     * Tests that an exception is thrown when sending wrong token type.
     */
    public function testUpdateWithWrongTokenTypeThrowException()
    {
        $token = new Token('wrong _value', 'wrong_type');

        $this->expectException(\Exception::class);

        $this->service->update($token);
    }
}

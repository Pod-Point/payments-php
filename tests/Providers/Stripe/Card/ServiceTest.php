<?php

namespace PodPoint\Payments\Tests\Providers\Stripe\Card;

use PodPoint\Payments\Token;
use PodPoint\Payments\Providers\Stripe\Card\Exception as StripeException;
use PodPoint\Payments\Providers\Stripe\Payment\Service;
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
     * Tests that a Stripe exception is thrown with a client secret in its response.
     */
    public function testCreateSetupIntentThrowStripeException()
    {
        try {
            $this->service->cards()->create();
        } catch (StripeException $exception) {
            $actual = $exception->getResponse()->client_secret;

            $this->assertNotNull($actual);
        }
    }

    /**
     * Tests that an exception is thrown when sending wrong token type.
     */
    public function testCreateWithWrongTokenTypeThrowException()
    {
        $token = new Token('wrong _value', 'wrong_type');

        $this->expectException(\Exception::class);

        $this->service->cards()->create($token);
    }
}

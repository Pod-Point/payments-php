<?php

namespace PodPoint\Payments\Tests\Providers\Stripe;

use PodPoint\Payments\Providers\Stripe\CardService;
use PodPoint\Payments\Providers\Stripe\Exception as StripeException;
use PodPoint\Payments\Tests\TestCase;

class CardServiceTest extends TestCase
{
    /**
     * @var CardService
     */
    private $service;

    /**
     * Creates an instance of the Stripe payment service.
     */
    public function setUp()
    {
        parent::setUp();

        $this->service = new CardService(getenv('STRIPE_KEY'));
    }

    /**
     * Tests that creating a new Card will throw an exception because the status won't be STATUS_SUCCEEDED.
     */
    public function testCreate()
    {
        $this->expectException(StripeException::class);

        $this->service->create();
    }
}
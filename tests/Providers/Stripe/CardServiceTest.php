<?php

namespace PodPoint\Payments\Tests\Providers\Stripe;

use PodPoint\Payments\Card;
use PodPoint\Payments\Providers\Stripe\CardService;
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
     * Tests that a payment can be created successfully.
     */
    public function testCreate()
    {
        $card = $this->service->create();

        $this->assertInstanceOf(Card::class, $card);
    }
}
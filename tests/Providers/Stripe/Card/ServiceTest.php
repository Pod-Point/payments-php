<?php

namespace PodPoint\Payments\Tests\Providers\Stripe\Card;

use PodPoint\Payments\Providers\Stripe\Card\Exception;
use PodPoint\Payments\Providers\Stripe\Token;
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
     * Test card can be setup.
     */
    public function testSetupCard()
    {
        try {
            $this->service->cards()->create();
        } catch (Exception $e) {
            $this->assertEquals(Token::SECRET_SETUP_INTENT, $e->getResponse()->type);
        }
    }
}

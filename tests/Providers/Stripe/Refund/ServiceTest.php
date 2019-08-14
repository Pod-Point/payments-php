<?php

namespace PodPoint\Payments\Tests\Providers\Stripe\Refund;

use PodPoint\Payments\Providers\Stripe\Payment\Service;
use PodPoint\Payments\Refund;
use PodPoint\Payments\Tests\TestCase;
use PodPoint\Payments\Token;
use Stripe\Charge;
use Stripe\PaymentIntent;

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
     * Tests refund based on payment intent.
     */
    public function testItCanRefundBasedOnPaymentIntent()
    {
        $intent = PaymentIntent::create([
            'payment_method' => 'pm_card_visa',
            'amount' => 100,
            'currency' => 'GBP',
        ]);
        $intent->confirm();

        $refund = $this->service->refunds()->create(
            new Token($intent->id),
            100,
            'requested_by_customer',
            []
        );

        $this->assertInstanceOf(Refund::class, $refund);
    }

    /**
     * Tests backwards compatibility for old tokens.
     */
    public function testItCanCreateCustomerWithCardToken()
    {
        $charge = Charge::create([
            'source' => 'tok_visa',
            'amount' => 100,
            'currency' => 'GBP',
        ]);

        $refund = $this->service->refunds()->create(
            new Token($charge->id),
            100,
            'requested_by_customer',
            []
        );

        $this->assertInstanceOf(Refund::class, $refund);
    }
}

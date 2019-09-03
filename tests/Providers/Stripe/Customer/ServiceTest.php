<?php

namespace PodPoint\Payments\Tests\Providers\Stripe\Customer;

use PodPoint\Payments\Customer;
use PodPoint\Payments\Card;
use PodPoint\Payments\Providers\Stripe\Payment\Service;
use PodPoint\Payments\Providers\Stripe\Token;
use PodPoint\Payments\Tests\TestCase;
use Stripe\Customer as StripeCustomer;

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
     * Tests customer can be created with Source API card.
     */
    public function testCanCreateCustomerWithSourceAPIToken()
    {
        $customer = $this->service->customers()->create(
            new Token('tok_visa'),
            'software@pod-point.com',
            'test'
        );

        $this->assertInstanceOf(Customer::class, $customer);
    }

    /**
     * Tests can add card to customer.
     */
    public function testCanAddCard()
    {
        $customer = $this->service->customers()->create(
            new Token('pm_card_visa'),
            'software@pod-point.com',
            'test'
        );

        $card = $this->service->customers()->addCard($customer->uid, 'pm_card_visa');

        $this->assertInstanceOf(Card::class, $card);
    }

    /**
     * Tests can add Source API card to customer.
     */
    public function testCanAddSourceApiCard()
    {
        $customer = $this->service->customers()->create(
            new Token('pm_card_visa'),
            'software@pod-point.com',
            'test'
        );

        $card = $this->service->customers()->addCard($customer->uid, 'tok_visa');

        $this->assertInstanceOf(Card::class, $card);
    }

    /**
     * Tests can retrieve an existing customer.
     */
    public function testCanRetrieveCustomer()
    {
        $email = 'john@pod-point.com';
        $description = "This is $email test decription";

        $customer = $this->service->customers()->create(
            new Token('pm_card_visa'),
            $email,
            $description
        );

        $customer = $this->service->customers()->find($customer->uid);

        $this->assertInstanceOf(Customer::class, $customer);
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
     * Test card can be deleted.
     */
    public function testCanDeleteCard()
    {
        /** @var StripeCustomer $stripeCustomer */
        $stripeCustomer = StripeCustomer::create([
            'email' => 'software@pod-point.com',
            'description' => 'test',
        ]);

        $card = $this->service->customers()->addCard($stripeCustomer->id, 'pm_card_visa');

        $this->service->customers()->deleteCard($stripeCustomer->id, $card->uid);

        $cards = $this->service->customers()->getCards($stripeCustomer->id);

        $this->assertEmpty($cards);
    }
}

<?php

namespace PodPoint\Payments\Tests;

use PodPoint\Payments\Providers\Stripe\Token as StripeToken;
use PodPoint\Payments\Token;

class TokenTest extends TestCase
{
    /**
     * Test it can identify different token types based on incoming token id.
     */
    public function testCanIdentifyCorrectTokenTypes()
    {
        $paymentIntent = new Token('pi_some_xxx');
        $this->assertEquals($paymentIntent->type, StripeToken::PAYMENT_INTENT);

        $paymentIntent = new Token('pm_some_xxx');
        $this->assertEquals($paymentIntent->type, StripeToken::PAYMENT_METHOD);

        $paymentIntent = new Token('cus_243_fd');
        $this->assertEquals($paymentIntent->type, StripeToken::CUSTOMER);

        $paymentIntent = new Token('ch_chskd_dssd');
        $this->assertEquals($paymentIntent->type, StripeToken::CHARGE);
    }

    /**
     * Test it can identify card id.
     */
    public function testCanIdentifyCardToken()
    {
        $token = new Token('pm_some_xxx');

        $this->assertEquals($token->isCard('card_ddsdsd'), true);
    }
}

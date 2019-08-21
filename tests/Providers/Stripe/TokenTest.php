<?php

namespace PodPoint\Payments\Tests\Providers\Stripe;

use PHPUnit\Framework\TestCase;
use PodPoint\Payments\Providers\Stripe\Token;

class TokenTest extends TestCase
{
    /**
     * Test it can identify different token types based on incoming token id.
     */
    public function testCanIdentifyCorrectTokenTypes()
    {
        $token = new Token('pi_some_xxx_secret_xxx');
        $this->assertEquals($token->type, Token::SECRET_PAYMENT_INTENT);

        $token = new Token('pi_some_xxx');
        $this->assertEquals($token->type, Token::PAYMENT_INTENT);

        $token = new Token('pm_some_xxx');
        $this->assertEquals($token->type, Token::PAYMENT_METHOD);

        $token = new Token('cus_243_fd');
        $this->assertEquals($token->type, Token::CUSTOMER);

        $token = new Token('ch_chskd_dssd');
        $this->assertEquals($token->type, Token::CHARGE);

        $token = new Token('card_xxxyyy');
        $this->assertEquals($token->type, Token::CARD);

        $token = new Token('tok_some_xxx');
        $this->assertEquals($token->type, Token::TOKEN);

        $token = new Token('seti_some_xxx_secret_xxx');
        $this->assertEquals($token->type, Token::SECRET_SETUP_INTENT);

        $token = new Token('seti_some_xxx');
        $this->assertEquals($token->type, Token::SETUP_INTENT);

        $token = new Token('setup');
        $this->assertEquals($token->type, Token::SETUP_CARD_CREATION);

        $this->expectException(\Exception::class);
        $token = new Token('bad_token');

        $this->expectException(\Exception::class);
        $token = new Token('bad_secret_xxx');
    }
}

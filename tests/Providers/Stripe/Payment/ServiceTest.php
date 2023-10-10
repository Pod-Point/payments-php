<?php

namespace PodPoint\Payments\Tests\Providers\Stripe\Payment;

use PodPoint\Payments\Exceptions\InvalidToken;
use PodPoint\Payments\Payment;
use PodPoint\Payments\Providers\Stripe\Customer\Service as CustomerService;
use PodPoint\Payments\Providers\Stripe\Payment\Uncancelable;
use PodPoint\Payments\Providers\Stripe\Payment\AmountTooLarge;
use PodPoint\Payments\Providers\Stripe\Payment\Canceled;
use PodPoint\Payments\Providers\Stripe\Payment\Exception as StripeException;
use PodPoint\Payments\Providers\Stripe\Payment\Service;
use PodPoint\Payments\Providers\Stripe\Refund\Service as RefundService;
use PodPoint\Payments\Providers\Stripe\Token;
use PodPoint\Payments\Tests\TestCase;
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
     * Tests that customer service can be returned.
     */
    public function testCanReturnCustomerService()
    {
        $service = $this->service->customers();

        $this->assertInstanceOf(CustomerService::class, $service);
    }

    /**
     * Tests that refund service can be returned.
     */
    public function testCanReturnRefundService()
    {
        $service = $this->service->refunds();

        $this->assertInstanceOf(RefundService::class, $service);
    }

    /**
     * Tests that a payment can be created successfully.
     */
    public function testCreatePaymentIntent()
    {
        $paymentMethodToken = new Token('pm_card_visa');

        $customer = $this->service->customers()->create(
            $paymentMethodToken,
            'software@pod-point.com',
            'test'
        );

        $token = new Token($customer->uid);

        $this->assertEquals($token->type, Token::CUSTOMER);

        $payment = $this->service->create($token, 100);

        $this->assertInstanceOf(Payment::class, $payment);

        $paymentIntent = new Token($payment->uid);

        $this->assertEquals($paymentIntent->type, Token::PAYMENT_INTENT);
    }

    /**
     * Tests that a payment can be created successfully from old token linked to a customer.
     */
    public function testCreateChargeForBackwardCompatibility()
    {
        $sourceToken = new Token('tok_visa');

        $customer = $this->service->customers()->create(
            $sourceToken,
            'software@pod-point.com',
            'test'
        );

        $token = new Token($customer->uid);

        $this->assertEquals($token->type, Token::CUSTOMER);

        $payment = $this->service->create($token, 100);

        $this->assertInstanceOf(Payment::class, $payment);

        $charge = new Token($payment->uid);

        $this->assertEquals($charge->type, Token::PAYMENT_INTENT);
    }

    /**
     * Tests that a payment can be created successfully from old token.
     */
    public function testCreateChargeFromToken()
    {
        $sourceToken = new Token('tok_visa');

        $payment = $this->service->create($sourceToken, 100);

        $this->assertInstanceOf(Payment::class, $payment);

        $charge = new Token($payment->uid);

        $this->assertEquals($charge->type, Token::CHARGE);
    }

    /**
     * Tests that payment intent can be confirmed.
     */
    public function testPaymentIntentConfirmation()
    {
        $needAuthToken = new Token('pm_card_authenticationRequiredOnSetup');

        $customer = $this->service->customers()->create(
            $needAuthToken,
            'software@pod-point.com',
            'test'
        );

        $token = new Token($customer->uid);

        $this->expectException(StripeException::class);

        $payment = $this->service->create($token, 100);

        $confirmedPayment = $this->service->create(new Token($payment->uid), 100);

        $this->assertInstanceOf(Payment::class, $confirmedPayment);
    }

    /**
     * Tests that a payment can be created successfully with a payment method.
     */
    public function testCreatePaymentIntentWithPaymentMethod()
    {
        $paymentMethodToken = new Token('pm_card_visa');

        $customer = $this->service->customers()->create(
            $paymentMethodToken,
            'software@pod-point.com',
            'test'
        );

        $payment = $this->service->create($paymentMethodToken, 100, 'GBP', null, [], $customer->uid);

        $this->assertInstanceOf(Payment::class, $payment);

        $paymentIntent = new Token($payment->uid);

        $this->assertEquals($paymentIntent->type, Token::PAYMENT_INTENT);
    }

    /**
     * Tests that a pre authorised payment can be created successfully with a payment method.
     */
    public function testPreAuthorise()
    {
        $paymentMethodToken = new Token('pm_card_visa');

        $payment = $this->service->reserve(
            $paymentMethodToken,
            1000
        );

        $this->assertInstanceOf(Payment::class, $payment);

        $paymentToken = new Token($payment->uid);

        $this->assertEquals( Token::PAYMENT_INTENT, $paymentToken->type);
    }

    /**
     * Tests that a pre authorised payment which requires confirmation can be created successfully with a payment method.
     */
    public function testPreAuthorisePaymentIntentConfirmation()
    {
        $paymentMethodToken = new Token('pm_card_authenticationRequiredOnSetup');

        try {
            $this->service->reserve(
                $paymentMethodToken,
                1000
            );
        } catch (StripeException $exception) {
            $this->assertEquals(Token::SECRET_PAYMENT_INTENT, $exception->getToken()->type);
        }
    }

    /**
     * Tests reserved funds capture.
     */
    public function testFundsCapture()
    {
        $paymentMethodToken = new Token('pm_card_visa');

        $reserveAmount = 1000;

        $payment = $this->service->reserve(
            $paymentMethodToken,
            $reserveAmount
        );

        $token = new Token($payment->uid);

        $capturedPayment = $this->service->capture(
            $token,
            $reserveAmount
        );

        $this->assertEquals($reserveAmount, $capturedPayment->amount);
    }

    /**
     * Tests that reserved funds can be only captured using payment intent token.
     */
    public function testFundsCanBeCapturedOnlyUsingPaymentIntentToken()
    {
        $token = new Token('pm_some_other_token');

        $this->expectException(InvalidToken::class);

        $this->service->capture(
            $token,
            1000
        );
    }

    /**
     * Tests that AmountTooLarge exeption is thrown if capture amount is higher than reserved funds.
     */
    public function testCorrectExceptionIsThrownWhenCaptureAmountIsHigherThanReservedFunds()
    {
        $paymentMethodToken = new Token('pm_card_visa');

        $reserveAmount = 1000;

        $payment = $this->service->reserve(
            $paymentMethodToken,
            $reserveAmount
        );

        $token = new Token($payment->uid);

        $this->expectException(AmountTooLarge::class);

        $this->service->capture(
            $token,
            $reserveAmount + 500
        );
    }

    /**
     * Tests that payment intent can be cancelled.
     *
     * @throws InvalidToken
     * @throws StripeException
     * @throws \Stripe\Error\Api
     */
    public function testPaymentIntentCanBeCancelled()
    {
        $mockPaymentIntent = new PaymentIntent();
        $mockPaymentIntent->status = 'canceled';

        $mockService = $this->getMockBuilder(Service::class)
            ->setMethodsExcept(['capture'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockService->expects($this->once())
            ->method('retrievePaymentIntent')
            ->willReturn($mockPaymentIntent);

        $this->expectException(Canceled::class);

        $token = new Token('some-uid');
        $token->type = Token::PAYMENT_INTENT;
        $mockService->capture($token, 20);
    }

    /**
     * Tests that reserved fund can only be cancelled using payment intent token.
     */
    public function testFundsCanBeOnlyCancelledWithPaymentIntentToken()
    {
        $token = new Token('pm_some_other_token');

        $this->expectException(InvalidToken::class);

        $this->service->cancel($token);
    }

    /**
     * Tests that cancelling uncancelleable payment intent throws exception.
     *
     * @throws InvalidToken
     * @throws StripeException
     * @throws \Stripe\Error\Api
     */
    public function testCancellingUncancellablePaymentIntentThrowsException()
    {
        $mockPaymentIntent = new PaymentIntent();
        $mockPaymentIntent->status = 'successful';

        $mockService = $this->getMockBuilder(Service::class)
            ->setMethodsExcept(['cancel'])
            ->disableOriginalConstructor()
            ->getMock();

        $mockService->expects($this->once())
            ->method('retrievePaymentIntent')
            ->willReturn($mockPaymentIntent);

        $this->expectException(Uncancelable::class);

        $token = new Token('some-uid');
        $token->type = Token::PAYMENT_INTENT;
        $mockService->cancel($token);
    }
}

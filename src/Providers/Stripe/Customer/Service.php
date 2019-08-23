<?php

namespace PodPoint\Payments\Providers\Stripe\Customer;

use PodPoint\Payments\Customer;
use PodPoint\Payments\Card;
use PodPoint\Payments\Token;
use PodPoint\Payments\Providers\Stripe\Token as StripeToken;
use PodPoint\Payments\Customer\Service as CustomerServiceInterface;
use Stripe\PaymentMethod;
use Stripe\Customer as StripeCustomer;

class Service implements CustomerServiceInterface
{
    /**
     * Creates a customer using the Stripe SDK.
     *
     * @param Token $token
     * @param string $email
     * @param string $description
     *
     * @return Customer
     */
    public function create(Token $token, string $email, string $description): Customer
    {
        $params = [
            'email' => $email,
            'description' => $description,
        ];

        switch ($token->type) {
            case StripeToken::PAYMENT_METHOD:
                $params['payment_method'] = $token->value;

                break;
            case StripeToken::CARD:
            default:
                $params['source'] = $token->value;

                break;
        }

        /** @var StripeCustomer $customer */
        $response = StripeCustomer::create($params);

        return new Customer($response->id, $response->email, $response->description);
    }

    /**
     * Retrieves a customer using the Stripe SDK.
     *
     * @param string $uid
     *
     * @return Customer
     */
    public function find(string $uid): Customer
    {
        /** @var StripeCustomer $customer */
        $response = StripeCustomer::retrieve($token->value);

        return new Customer($response->id, $response->email, $response->description);
    }

    /**
     * Associates a card to a customer using the Stripe SDK.
     *
     * @param Customer $customer
     * @param Card $card
     *
     * @return Card
     */
    public function addCard(Customer $customer, Card $card): Card
    {
        /** @var PaymentMethod $paymentMethod */
        $paymentMethod = PaymentMethod::retrieve($card->uid);

        $response = $paymentMethod->attach(['customer' => $customer->uid]);

        return new Card(
            $response->id,
            $response->card->last4,
            $response->card->brand,
            $response->card->funding,
            $response->card->exp_month,
            $response->card->exp_year
        );
    }

    /**
     * Deletes a customers card using the Stripe SDK.
     *
     * @param Customer $customer
     * @param Card $card
     *
     * @return void
     */
    public function deleteCard(Customer $customer, Card $card): void
    {
        StripeCustomer::deleteSource($customer->uid, $card->uid);
    }

    /**
     * Retrieves a customers cards using the Stripe SDK.
     *
     * @param Customer $customer
     *
     * @return Card[]
     */
    public function getCards(Customer $customer): array
    {
        $cards = [];

        $paymentMethods = PaymentMethod::all([
            'customer' => $customer->uid,
            'type' => 'card',
        ]);

        foreach ($paymentMethods->data as $paymentMethod) {
            $cards[] = new Card(
                $paymentMethod->id,
                $paymentMethod->card->last4,
                $paymentMethod->card->brand,
                $paymentMethod->card->funding,
                $paymentMethod->card->exp_month,
                $paymentMethod->card->exp_year
            );
        }

        return $cards;
    }
}

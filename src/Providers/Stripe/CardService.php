<?php

namespace PodPoint\Payments\Providers\Stripe;

use PodPoint\Payments\Exception;
use PodPoint\Payments\Service as ServiceInterface;
use PodPoint\Payments\Card;
use Stripe\SetupIntent;

class CardService extends Base
{
    /**
     * Tries create a setup card request using the Stripe SDK.
     *
     * @return Card
     *
     * @throws Exception
     */
    public function create(): Card
    {
        try {
            $response = SetupIntent::create([
                'usage' => 'on_session',
            ]);
        } catch (\Exception $exception) {
            throw new Exception($exception);
        }

        if (!in_array($response->status, [
            SetupIntent::STATUS_REQUIRES_PAYMENT_METHOD,
            SetupIntent::STATUS_SUCCEEDED,
        ])) {
            throw new StripeException($response);
        }

        return new Card($response->id, '', $response->client_secret, $response->created);
    }

    /**
     * Tries to update a card request using the Stripe SDK.
     *
     * @return Card
     *
     * @throws Exception
     */
    public function update(string $uid): Card
    {
        try {
            $response = SetupIntent::retrieve($uid);
        } catch (\Exception $exception) {
            throw new Exception($exception);
        }

        if (!in_array($response->status, [
            SetupIntent::STATUS_REQUIRES_PAYMENT_METHOD,
            SetupIntent::STATUS_SUCCEEDED,
        ])) {
            throw new StripeException($response);
        }

        return new Card($response->id, $response->payment_method, $response->client_secret, $response->created);
    }
}

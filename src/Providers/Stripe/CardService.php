<?php

namespace PodPoint\Payments\Providers\Stripe;

use PodPoint\Payments\Card;
use PodPoint\Payments\CardService as CardServiceInterface;
use PodPoint\Payments\Exception;
use PodPoint\Payments\Providers\Stripe\Exception as StripeException;
use Stripe\SetupIntent;

class CardService extends Base implements CardServiceInterface
{
    /**
     * Tries create a setup card request using the Stripe SDK.
     *
     * @return Card
     *
     * @throws Exception
     * @throws StripeException
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

        if ($response->status !== SetupIntent::STATUS_SUCCEEDED) {
            throw new StripeException($response);
        }

        return new Card($response->payment_method, $response->created);
    }
}

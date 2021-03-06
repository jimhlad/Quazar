<?php

namespace Yab\Quazar\Services;

use Illuminate\Support\Facades\DB;
use Yab\Quazar\Models\Transactions;
use Yab\Crypto\Services\Crypto;

class PaymentService
{
    public $user;

    public function __construct(
        Transactions $transactions,
        OrderService $orderService,
        LogisticService $logisticService
) {
        $this->user = auth()->user();
        $this->transaction = $transactions;
        $this->orderService = $orderService;
        $this->logistic = $logisticService;
    }

    /*
    |--------------------------------------------------------------------------
    | Purchases
    |--------------------------------------------------------------------------
    */

    /**
     * Make a purchase.
     *
     * @param string $stripeToken
     * @param Cart   $cart
     *
     * @return mixed
     */
    public function purchase($stripeToken, $cart)
    {
        $user = auth()->user();

        if (is_null($user->meta->stripe_id) && $stripeToken) {
            $user->meta->createAsStripeCustomer($stripeToken);
        } elseif ($stripeToken) {
            $user->meta->updateCard($stripeToken);
        }

        DB::beginTransaction();

        $result = $user->meta->charge(($cart->getCartTotal() * 100), [
            'currency' => config('quazar.currency', 'usd'),
        ]);

        if ($result) {
            $transaction = $this->transaction->create([
                'uuid' => Crypto::uuid(),
                'provider' => 'stripe',
                'state' => 'success',
                'subtotal' => $cart->getCartSubTotal(),
                'tax' => $cart->getCartTax(),
                'total' => $cart->getCartTotal(),
                'shipping' => $this->logistic->shipping($user),
                'provider_id' => $result->id,
                'provider_date' => $result->created,
                'provider_dispute' => '',
                'cart' => json_encode($cart->contents()),
                'response' => json_encode($result),
                'user_id' => $user->id,
            ]);

            $orderedItems = [];
            foreach ($cart->contents() as $item) {
                if (!$item->is_download) {
                    $orderedItems[] = $item;
                }
            }

            if (!empty($orderedItems)) {
                $this->createOrder($user, $transaction, $orderedItems);
            }
        }

        DB::commit();

        return $this->logistic->afterPurchase($user, $transaction, $cart, $result);
    }

    /**
     * Create an order.
     *
     * @param User        $user
     * @param Transaction $transaction
     * @param array       $items
     *
     * @return mixed
     */
    public function createOrder($user, $transaction, $items)
    {
        $customerService = app(CustomerProfileService::class);

        $this->orderService->create([
            'uuid' => Crypto::uuid(),
            'user_id' => $user->id,
            'transaction_id' => $transaction->id,
            'details' => json_encode($items),
            'shipping_address' => json_encode([
                'street' => $customerService->shippingAddress('street'),
                'postal' => $customerService->shippingAddress('postal'),
                'city' => $customerService->shippingAddress('city'),
                'state' => $customerService->shippingAddress('state'),
                'country' => $customerService->shippingAddress('country'),
             ]),
        ]);

        return $this->logistic->afterPlaceOrder($user, $transaction, $items);
    }
}

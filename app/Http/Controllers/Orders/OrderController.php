<?php

namespace App\Http\Controllers\Orders;

use App\Ecommerce\Cart;
use App\Events\Order\OrderCreatedEvent;
use App\Http\Requests\Orders\OrderStoreRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;

class OrderController extends Controller
{
	protected $cart;

	public function __construct()
	{
		$this->middleware(['auth:api', 'cart.sync', 'cart.nonempty']);
	}

	public function store(OrderStoreRequest $request, Cart $cart)
	{
		$order = $this->createOrder($request, $cart);

		$order->products()->sync($cart->products()->forSyncing());

		event(new OrderCreatedEvent($order));

		return new OrderResource($order);
    }

    protected function createOrder(Request $request, Cart $cart)
    {
	    return $request->user()->orders()->create(
		    array_merge($request->only(['address_id', 'shipping_method_id']), [
		    	'subtotal' => $cart->getSubtotal()->getAmount()
		    ])
	    );
    }
}

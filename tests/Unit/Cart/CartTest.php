<?php

namespace Tests\Unit\Cart;

use App\Ecommerce\Cart;
use App\Models\ProductVariation;
use App\Models\User;
use Tests\TestCase;

class CartTest extends TestCase
{
	public function test_can_add_products_to_cart()
    {
        $cart = new Cart(
        	$user = factory(User::class)->create()
        );

        $product = factory(ProductVariation::class)->create();

	    $cart->add([
	    	['id' => $product->id, 'quantity' => 1]
	    ]);

	    $this->assertCount(1, $user->fresh()->cart);
    }
}

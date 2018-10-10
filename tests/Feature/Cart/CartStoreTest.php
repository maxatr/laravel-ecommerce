<?php

namespace Tests\Feature\Cart;

use App\Models\ProductVariation;
use App\Models\User;
use Tests\TestCase;

class CartStoreTest extends TestCase
{
	public function test_fails_if_unauthenticated()
    {
        $this->json('POST', 'api/cart')
	        ->assertStatus(401);
    }

	public function test_requires_products()
	{
		$user = factory(User::class)->create();

		$this->jsonAs($user, 'POST', 'api/cart')
		->assertJsonValidationErrors(['products']);
	}

	public function test_requires_array_of_products()
	{
		$user = factory(User::class)->create();

		$this->jsonAs($user, 'POST', 'api/cart', [
			'products' => 1
		])->assertJsonValidationErrors(['products']);
	}

	public function test_requires_product_id_in_products()
	{
		$user = factory(User::class)->create();

		$this->jsonAs($user, 'POST', 'api/cart', [
			'products' => [
				['quantity' => 1]
			]
		])->assertJsonValidationErrors(['products.0.id']);
	}

	public function test_requires_products_to_exist()
	{
		$user = factory(User::class)->create();

		$this->jsonAs($user, 'POST', 'api/cart', [
			'products' => [
				['id' => 1, 'quantity' => 1]
			]
		])->assertJsonValidationErrors(['products.0.id']);
	}

	public function test_requires_products_quantity_to_be_numeric()
	{
		$user = factory(User::class)->create();

		$this->jsonAs($user, 'POST', 'api/cart', [
			'products' => [
				['id' => 1, 'quantity' => 'one']
			]
		])->assertJsonValidationErrors(['products.0.quantity']);
	}

	public function test_requires_products_quantity_to_be_at_least_one()
	{
		$user = factory(User::class)->create();

		$this->jsonAs($user, 'POST', 'api/cart', [
			'products' => [
				['id' => 1, 'quantity' => 0]
			]
		])->assertJsonValidationErrors(['products.0.quantity']);
	}

	public function test_requires_products_can_be_added_to_cart()
	{
		$user = factory(User::class)->create();

		$product = factory(ProductVariation::class)->create();

		$this->jsonAs($user, 'POST', 'api/cart', [
			'products' => [
				['id' => $product->id, 'quantity' => 2]
			]
		]);

		$this->assertDatabaseHas('cart_user', [
			'product_variation_id' => $product->id,
			'quantity' => 2,
		]);
	}
}

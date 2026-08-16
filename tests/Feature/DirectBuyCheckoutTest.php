<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class DirectBuyCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_direct_buy_creates_checkout_session_and_keeps_cart_items_when_canceled(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role' => 'customer',
        ]);
        $category = Category::create(['name' => 'Fruits', 'slug' => 'fruits']);
        $product = Product::create([
            'name' => 'Mango',
            'price' => 100,
            'stock' => 10,
            'description' => 'Fresh mango',
            'category_id' => $category->id,
        ]);

        Session::put('shopping_cart', [
            $product->id => 2,
        ]);

        $this->actingAs($user)
            ->post(route('checkout.direct-buy', $product))
            ->assertRedirect(route('checkout.show'));

        $this->assertTrue(Session::has('direct_buy_product_id'));
        $this->assertSame($product->id, Session::get('direct_buy_product_id'));
        $this->assertSame(2, Session::get('shopping_cart')[$product->id]);

        $response = $this->actingAs($user)
            ->get(route('checkout.show'));

        $response->assertOk();
        $response->assertSee('Mango');

        $this->actingAs($user)
            ->get(route('checkout.cancel'))
            ->assertRedirect(route('cart.show'));

        $this->assertSame([ $product->id => 3 ], Session::get('shopping_cart'));
        $this->assertFalse(Session::has('direct_buy_product_id'));
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class EcommerceFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Product $product;
    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create([
            'name' => 'Foundation',
            'slug' => 'foundation',
        ]);

        $brand = Brand::create([
            'name' => 'Fenty Beauty',
            'slug' => 'fenty-beauty',
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'brand_id'    => $brand->id,
            'name'        => 'Pro Filt\'r Soft Matte Foundation',
            'slug'        => 'pro-filtr-foundation',
            'subtitle'    => 'Longwear soft-matte foundation',
            'description' => 'A game-changing foundation.',
            'base_price'  => 42.00,
            'is_active'   => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id'  => $this->product->id,
            'sku'         => 'TEST-SKU-001',
            'title'       => '120N - Neutral',
            'shade_name'  => '120N',
            'shade_hex'   => '#D8A279',
            'price'       => 42.00,
            'stock_quantity' => 50,
        ]);

        $this->user = User::factory()->create([
            'email'    => 'test@beautyvault.com',
            'password' => bcrypt('password'),
            'role'     => 'customer',
        ]);
    }

    // ─── Authentication ──────────────────────────────────────────────────────

    /** @test */
    public function user_can_register()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Jane Doe',
            'email'                 => 'jane@beautyvault.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email']]);
    }

    /** @test */
    public function user_can_login()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'test@beautyvault.com',
            'password' => 'password',
        ]);

        $response->assertOk()
                 ->assertJsonStructure(['token', 'user']);
    }

    /** @test */
    public function login_fails_with_wrong_credentials()
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'test@beautyvault.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function user_can_logout_and_token_is_revoked()
    {
        // 1. Login to get token
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email'    => 'test@beautyvault.com',
            'password' => 'password',
        ]);
        $token = $loginResponse->json('token');

        // 2. Call logout with Bearer token
        $logoutResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
                               ->postJson('/api/v1/auth/logout');

        $logoutResponse->assertOk()
                       ->assertJsonFragment(['message' => 'Logged out successfully.']);

        // 3. Clear in-memory test guard & subsequent request with the revoked token must return 401
        $this->app['auth']->forgetGuards();

        $subsequentResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
                                   ->getJson('/api/v1/auth/me');

        $subsequentResponse->assertStatus(401);
    }

    // ─── Product Catalog ─────────────────────────────────────────────────────

    /** @test */
    public function products_are_listed_publicly()
    {
        $response = $this->getJson('/api/v1/products');

        $response->assertOk()
                 ->assertJsonStructure(['data', 'current_page']);
    }

    /** @test */
    public function product_detail_is_accessible_by_slug()
    {
        $response = $this->getJson("/api/v1/products/{$this->product->slug}");

        $response->assertOk()
                 ->assertJsonFragment(['name' => $this->product->name]);
    }

    /** @test */
    public function products_can_be_filtered_by_search()
    {
        $response = $this->getJson('/api/v1/products?search=Foundation');

        $response->assertOk();
        $this->assertIsArray($response->json('data'));
    }

    // ─── Cart ────────────────────────────────────────────────────────────────

    /** @test */
    public function guest_can_add_item_to_cart()
    {
        $sessionId = 'test_session_abc123';

        $response = $this->withHeaders(['X-Session-ID' => $sessionId])
                         ->postJson('/api/v1/cart/items', [
                             'product_variant_id' => $this->variant->id,
                             'quantity'           => 2,
                         ]);

        $response->assertOk()
                 ->assertJsonStructure(['items', 'subtotal', 'total']);

        $this->assertDatabaseHas('cart_items', [
            'product_variant_id' => $this->variant->id,
            'quantity'           => 2,
        ]);
    }

    /** @test */
    public function authenticated_user_can_add_item_to_cart()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/cart/items', [
            'product_variant_id' => $this->variant->id,
            'quantity'           => 1,
        ]);

        $response->assertOk()
                 ->assertJsonStructure(['items', 'subtotal', 'total']);
    }

    /** @test */
    public function cart_quantity_can_be_updated()
    {
        Sanctum::actingAs($this->user);

        // First add
        $this->postJson('/api/v1/cart/items', [
            'product_variant_id' => $this->variant->id,
            'quantity'           => 1,
        ]);

        $cart     = Cart::where('user_id', $this->user->id)->first();
        $cartItem = $cart->items()->first();

        // Update
        $response = $this->patchJson("/api/v1/cart/items/{$cartItem->id}", [
            'quantity' => 3,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('cart_items', [
            'id'       => $cartItem->id,
            'quantity' => 3,
        ]);
    }

    // ─── Coupon System ───────────────────────────────────────────────────────

    /** @test */
    public function valid_coupon_applies_discount()
    {
        // Create a coupon
        Coupon::create([
            'code'              => 'GLOW20',
            'discount_type'     => 'percentage',
            'discount_value'    => 20,
            'min_order_amount'  => 10,
            'usage_limit_total' => 100,
            'usage_count'       => 0,
            'is_active'         => true,
            'expires_at'        => now()->addMonth(),
        ]);

        Sanctum::actingAs($this->user);

        // Add item first
        $this->postJson('/api/v1/cart/items', [
            'product_variant_id' => $this->variant->id,
            'quantity'           => 1,
        ]);

        // Apply coupon
        $response = $this->postJson('/api/v1/cart/coupon', ['code' => 'GLOW20']);

        $response->assertOk();
        $this->assertEquals('GLOW20', $response->json('coupon.code'));
    }

    /** @test */
    public function invalid_coupon_returns_error()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/cart/coupon', ['code' => 'FAKECODE']);

        $response->assertStatus(422);
    }

    // ─── Review System ───────────────────────────────────────────────────────

    /** @test */
    public function authenticated_user_can_submit_review()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson("/api/v1/products/{$this->product->id}/reviews", [
            'rating'      => 5,
            'title'       => 'Best foundation ever!',
            'body'        => 'This product completely transformed my skin routine. Highly recommend.',
            'skin_type'   => 'oily',
            'undertone'   => 'neutral',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure(['id', 'rating', 'title', 'user']);
    }

    /** @test */
    public function unauthenticated_user_cannot_submit_review()
    {
        $response = $this->postJson("/api/v1/products/{$this->product->id}/reviews", [
            'rating' => 5,
            'body'   => 'Great product!',
        ]);

        $response->assertStatus(401);
    }

    // ─── Admin Protection ────────────────────────────────────────────────────

    /** @test */
    public function customer_cannot_access_admin_endpoints()
    {
        Sanctum::actingAs($this->user); // customer role

        $response = $this->getJson('/api/v1/admin/analytics');

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_access_analytics()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/v1/admin/analytics');

        $response->assertOk()
                 ->assertJsonStructure(['revenue', 'orders_count', 'top_products']);
    }

    // ─── Health Check ─────────────────────────────────────────────────────────

    /** @test */
    public function health_check_endpoint_returns_ok()
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertOk()
                 ->assertJsonFragment(['status' => 'healthy']);
    }

    // ─── Wishlist ─────────────────────────────────────────────────────────────

    /** @test */
    public function authenticated_user_can_toggle_and_view_wishlist()
    {
        Sanctum::actingAs($this->user);

        // Toggle add
        $addResponse = $this->postJson('/api/v1/wishlist/toggle', [
            'product_id' => $this->product->id,
        ]);
        $addResponse->assertStatus(201)
                    ->assertJsonFragment(['in_wishlist' => true]);

        // View wishlist
        $listResponse = $this->getJson('/api/v1/wishlist');
        $listResponse->assertOk()
                     ->assertJsonFragment(['count' => 1]);

        // Toggle remove
        $removeResponse = $this->postJson('/api/v1/wishlist/toggle', [
            'product_id' => $this->product->id,
        ]);
        $removeResponse->assertOk()
                       ->assertJsonFragment(['in_wishlist' => false]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use Database\Seeders\CosmeticsDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BeautyCommerceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CosmeticsDatabaseSeeder::class);
    }

    /**
     * Test Health Check Endpoint
     */
    public function test_system_health_check_returns_healthy(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200)
                 ->assertJsonStructure(['status', 'database', 'environment']);
    }

    /**
     * Test Product Catalog and Metadata
     */
    public function test_catalog_and_metadata_endpoints(): void
    {
        $response = $this->getJson('/api/v1/products');
        $response->assertStatus(200)
                 ->assertJsonStructure(['data', 'current_page', 'total']);

        $metadataResponse = $this->getJson('/api/v1/metadata');
        $metadataResponse->assertStatus(200)
                         ->assertJsonStructure(['categories', 'brands', 'skin_types', 'undertones']);
    }

    /**
     * Test Shade Matching Engine
     */
    public function test_shade_matching_algorithm(): void
    {
        $payload = [
            'depth_level' => 4,
            'undertone' => 'warm',
        ];

        $response = $this->postJson('/api/v1/match-shades', $payload);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'criteria' => ['depth_level', 'undertone'],
                     'matches' => [
                         '*' => ['variant_id', 'shade_name', 'match_score', 'match_quality']
                     ]
                 ]);
    }

    /**
     * Test Skin Diagnostic Quiz Evaluation
     */
    public function test_skin_diagnostic_quiz_generates_diagnosis(): void
    {
        $quizData = [
            'oil_level' => 'shiny_tzone_only',
            'vein_color' => 'green',
            'shade_depth' => 4,
            'concerns' => ['large_pores', 'dullness'],
            'reactivity' => 'low',
        ];

        $response = $this->postJson('/api/v1/diagnostic/quiz', $quizData);

        $response->assertStatus(200)
                 ->assertJsonPath('diagnosis.skin_type', 'combination')
                 ->assertJsonPath('diagnosis.undertone', 'warm');
    }

    /**
     * Test Atomic Checkout with FIFO Batch Deduction
     */
    public function test_checkout_deducts_stock_and_creates_order(): void
    {
        $variant = ProductVariant::where('stock_quantity', '>', 5)->first();
        $this->assertNotNull($variant, 'Need a variant with stock to test checkout.');

        $initialStock = $variant->stock_quantity;

        $checkoutData = [
            'customer_name' => 'Test Beauty Shopper',
            'customer_email' => 'shopper@test.com',
            'shipping_address' => [
                'recipient_name' => 'Test Shopper',
                'street' => '100 Sunset Blvd',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'postal_code' => '90001',
                'country' => 'US',
            ],
            'payment_method' => 'stripe',
            'coupon_code' => 'GLOW20',
            'items' => [
                [
                    'product_variant_id' => $variant->id,
                    'quantity' => 2,
                ]
            ]
        ];

        $response = $this->postJson('/api/v1/checkout', $checkoutData);

        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'order' => ['id', 'order_number', 'total_amount']]);

        // Verify stock quantity was accurately decremented
        $variant->refresh();
        $this->assertEquals($initialStock - 2, $variant->stock_quantity);
    }
}

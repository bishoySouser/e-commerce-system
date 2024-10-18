<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class OrderFeatureTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $products;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::factory()->create();
        
        // Create test products
        $this->products = [
            Product::factory()->create([
                'id' => 4,
                'name' => 'voluptatum',
                'description' => 'Dicta quas nihil est dolorem est minus.',
                'price' => 650.09,
                'category' => 'cat3'
            ]),
            Product::factory()->create([
                'id' => 2,
                'name' => 'quaerat',
                'description' => 'Ea aut vitae occaecati natus.',
                'price' => 561.09,
                'category' => 'cat3'
            ])
        ];
    }

    public function test_authenticated_user_can_create_order()
    {
        Sanctum::actingAs($this->user);

        $payload = [
            'products' => [
                [
                    'id' => 4,
                    'quantity' => 2
                ],
                [
                    'id' => 2,
                    'quantity' => 5
                ]
            ]
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'status_code',
                'data' => [
                    'id',
                    'userId',
                    'totalAmount',
                    'status',
                    'products' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'price',
                            'quantity',
                            'priceAtTime',
                            'category'
                        ]
                    ],
                    'createdAt',
                    'updatedAt'
                ],
                'message'
            ])
            ->assertJson([
                'success' => true,
                'status_code' => 200,
                'data' => [
                    'totalAmount' => 4105.63,
                    'status' => 'pending',
                    'userId' => $this->user->id,
                ],
                'message' => 'Order is being processed.'
            ]);

        // Check the database
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'status' => 'pending',
            'total_amount' => 4105.63
        ]);

        // Check order products were saved
        $orderId = $response->json('data.id');
        $this->assertDatabaseHas('order_product', [
            'order_id' => $orderId,
            'product_id' => 4,
            'quantity' => 2,
            'price_at_time' => 650.09
        ]);

        $this->assertDatabaseHas('order_product', [
            'order_id' => $orderId,
            'product_id' => 2,
            'quantity' => 5,
            'price_at_time' => 561.09
        ]);
    }

    public function test_unauthenticated_user_cannot_create_order()
    {
        $payload = [
            'products' => [
                [
                    'id' => 4,
                    'quantity' => 2
                ]
            ]
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(401);
    }

    public function test_cannot_create_order_with_invalid_quantity()
    {
        Sanctum::actingAs($this->user);

        $payload = [
            'products' => [
                [
                    'id' => 4,
                    'quantity' => 0
                ]
            ]
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['products.0.quantity']);
    }

    public function test_cannot_create_order_with_empty_products()
    {
        Sanctum::actingAs($this->user);

        $payload = [
            'products' => []
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['products']);
    }
}
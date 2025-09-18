<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_a_product()
    {
        $data = [
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ];

        $response = $this->postJson('/api/product', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'message' => 'Product created successfully',
                'name' => 'Test Product',
                'price' => 99.99,
                'stock' => 10,
            ]);

        $this->assertDatabaseHas('products', $data);
    }

    public function test_can_filter_products_by_min_and_max_price()
    {
        // منتجات بأسعار مختلفة
        Product::factory()->create(['price' => 50]);
        Product::factory()->create(['price' => 100]);
        Product::factory()->create(['price' => 150]);

        // فلترة بين 60 و 120
        $response = $this->getJson('/api/product?min_price=60&max_price=120');

        $response->assertStatus(200)
            ->assertJsonCount(1) // منتج واحد بس في النطاق
            ->assertJsonFragment(['price' => 100]);
    }


    public function test_can_list_products()
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/product');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    public function test_can_show_a_product()
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/product/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock,
            ]);
    }

    public function test_can_update_a_product()
    {
        $product = Product::factory()->create();

        $updatedData = [
            'name' => 'Updated Product',
            'price' => 150.75,
            'stock' => 5,
        ];

        $response = $this->putJson("/api/product/{$product->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Product updated successfully',
                'name' => 'Updated Product',
                'price' => 150.75,
                'stock' => 5,
            ]);

        $this->assertDatabaseHas('products', $updatedData);
    }

    public function test_can_delete_a_product()
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/product/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Product deleted successfully'
            ]);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}

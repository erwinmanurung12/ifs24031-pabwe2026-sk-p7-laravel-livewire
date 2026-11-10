<?php

namespace Tests\Unit\Models;

use App\Features\Stock\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StockModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $stock = Stock::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $stock->user);
        $this->assertEquals($user->id, $stock->user->id);
    }

    public function test_stock_casts(): void
    {
        $stock = Stock::factory()->create([
            'quantity' => 12,
            'min_stock' => 5,
            'price' => 150000.5,
        ]);

        $this->assertSame(12, $stock->quantity);
        $this->assertSame(5, $stock->min_stock);
        $this->assertEquals('150000.50', $stock->price);
    }

    public function test_stock_factory_states(): void
    {
        $lowStock = Stock::factory()->lowStock()->create();
        $this->assertEquals('low_stock', $lowStock->status);

        $outOfStock = Stock::factory()->outOfStock()->create();
        $this->assertEquals('out_of_stock', $outOfStock->status);
    }

    public function test_cover_image_url_returns_correct_url_or_null(): void
    {
        $stockWithoutCover = Stock::factory()->create(['cover_image_path' => null]);
        $this->assertNull($stockWithoutCover->coverImageUrl());

        Storage::fake('public');
        Storage::disk('public')->put('stock-covers/sample.png', 'content');

        $stockWithCover = Stock::factory()->create(['cover_image_path' => 'stock-covers/sample.png']);
        $this->assertNotNull($stockWithCover->coverImageUrl());
        $this->assertStringContainsString('stock-covers/sample.png', $stockWithCover->coverImageUrl());
    }
}


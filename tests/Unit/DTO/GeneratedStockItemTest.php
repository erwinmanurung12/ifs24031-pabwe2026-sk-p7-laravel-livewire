<?php

namespace Tests\Unit\DTO;

use App\Features\Ai\DTO\GeneratedStockItem;
use PHPUnit\Framework\TestCase;

class GeneratedStockItemTest extends TestCase
{
    public function test_dto_properties_and_to_array(): void
    {
        $dto = new GeneratedStockItem(
            name: 'Monitor Dell',
            sku: 'MON-001',
            description: 'Monitor 24 inch IPS',
            quantity: 12,
            unit: 'unit',
            price: 2500000.0,
            minStock: 5,
            status: 'available'
        );

        $this->assertEquals('Monitor Dell', $dto->name);
        $this->assertEquals('MON-001', $dto->sku);
        $this->assertEquals('Monitor 24 inch IPS', $dto->description);
        $this->assertEquals(12, $dto->quantity);
        $this->assertEquals('unit', $dto->unit);
        $this->assertEquals(2500000.0, $dto->price);
        $this->assertEquals(5, $dto->minStock);
        $this->assertEquals('available', $dto->status);

        $array = $dto->toArray();
        $this->assertEquals([
            'name' => 'Monitor Dell',
            'sku' => 'MON-001',
            'description' => 'Monitor 24 inch IPS',
            'quantity' => 12,
            'unit' => 'unit',
            'price' => 2500000.0,
            'min_stock' => 5,
            'status' => 'available',
        ], $array);
    }
}


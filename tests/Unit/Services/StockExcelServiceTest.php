<?php

namespace Tests\Unit\Services;

use App\Features\Stock\Models\Stock;
use App\Features\Stock\Repositories\StockRepository;
use App\Features\Stock\Services\StockExcelService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

class StockExcelServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StockExcelService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new StockExcelService(new StockRepository());
    }

    public function test_build_spreadsheet_and_export_response(): void
    {
        $user = User::factory()->create();
        $stocks = Stock::factory()->count(2)->create(['user_id' => $user->id]);

        $spreadsheet = $this->service->buildSpreadsheet($stocks, 'Custom Stocks');
        $this->assertInstanceOf(Spreadsheet::class, $spreadsheet);
        $this->assertEquals('Custom Stocks', $spreadsheet->getActiveSheet()->getTitle());

        $response = $this->service->exportToResponse($stocks, 'test.xlsx');
        $this->assertInstanceOf(StreamedResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

        ob_start();
        $response->sendContent();
        $outputContent = ob_get_clean();
        $this->assertNotEmpty($outputContent);
    }

    public function test_import_spreadsheet(): void
    {
        $user = User::factory()->create();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Name');
        $sheet->setCellValue('C1', 'SKU');
        $sheet->setCellValue('D1', 'Description');
        $sheet->setCellValue('E1', 'Quantity');
        $sheet->setCellValue('F1', 'Unit');
        $sheet->setCellValue('G1', 'Price');
        $sheet->setCellValue('H1', 'Min Stock');
        $sheet->setCellValue('I1', 'Status');

        // Row 2: Standard stock with valid status
        $sheet->setCellValue('A2', '1');
        $sheet->setCellValue('B2', 'Imported Stock 1');
        $sheet->setCellValue('C2', 'SKU-IMP-1');
        $sheet->setCellValue('D2', 'Desc 1');
        $sheet->setCellValue('E2', 20);
        $sheet->setCellValue('F2', 'box');
        $sheet->setCellValue('G2', 120000);
        $sheet->setCellValue('H2', 5);
        $sheet->setCellValue('I2', 'available');

        // Row 3: Status invalid fallback to fromQuantity, '-' description and sku, empty unit
        $sheet->setCellValue('A3', '2');
        $sheet->setCellValue('B3', 'Imported Stock 2');
        $sheet->setCellValue('C3', '-');
        $sheet->setCellValue('D3', '-');
        $sheet->setCellValue('E3', 2);
        $sheet->setCellValue('F3', '-');
        $sheet->setCellValue('G3', 5000);
        $sheet->setCellValue('H3', 5);
        $sheet->setCellValue('I3', 'unknown_status');

        // Row 4: Column B empty, fallback to Column A
        $sheet->setCellValue('A4', 'Stock from Col A');
        $sheet->setCellValue('B4', '');
        $sheet->setCellValue('C4', 'SKU-A');
        $sheet->setCellValue('D4', 'Col A Desc');
        $sheet->setCellValue('E4', 0);
        $sheet->setCellValue('F4', 'pcs');
        $sheet->setCellValue('G4', 10000);
        $sheet->setCellValue('H4', 3);
        $sheet->setCellValue('I4', 'out_of_stock');

        // Row 5: Duplicate header row (ignored)
        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'Name');
        $sheet->setCellValue('C5', 'Skip this');

        $tempPath = tempnam(sys_get_temp_dir(), 'test_excel_').'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $result = $this->service->import($tempPath, $user->id);
        @unlink($tempPath);

        $this->assertEquals(3, $result['imported']);
        $this->assertDatabaseHas('stocks', [
            'user_id' => $user->id,
            'name' => 'Imported Stock 1',
            'sku' => 'SKU-IMP-1',
            'status' => 'available',
            'unit' => 'box',
        ]);
        $this->assertDatabaseHas('stocks', [
            'user_id' => $user->id,
            'name' => 'Imported Stock 2',
            'sku' => null,
            'description' => null,
            'status' => 'low_stock',
            'unit' => 'pcs',
        ]);
        $this->assertDatabaseHas('stocks', [
            'user_id' => $user->id,
            'name' => 'Stock from Col A',
            'status' => 'out_of_stock',
        ]);
    }
}


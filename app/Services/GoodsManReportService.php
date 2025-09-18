<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; 

class GoodsManReportService
{
    private $filePath;
    
    public function __construct() {
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '512M');
    }
    
    public function GenerateReport(string $startDate, string $endDate, string $transactionDate, string $storeID)
    {
        $clientCode = '29991';
        $fileName = 'PAD' . str_pad($clientCode, 9, '0', STR_PAD_LEFT) . '_' . date('Ymd') . '.TXT';
        $this->filePath = $fileName;
        

        $data = DB::connection('sqlsrv')->select('EXEC dbo.GoodsMan_EDI ?, ?', [$startDate, $endDate]);
        
   
        $content = [];
        
        // Header ekle
        $content[] = $this->buildHeader($transactionDate, $storeID);
        
        // Her satırı işle ve array'e ekle
        foreach ($data as $row) {
            $rowArr = (array)$row;
            $content[] = $this->buildLine($rowArr);
        }
        
        // Trailer ekle
        $content[] = $this->buildTrailer(count($data), $transactionDate, $storeID);
        
        // Tek seferde dosyaya yaz - BU EN ÖNEMLİ KISIM
        Storage::disk('dataexchange')->put($this->filePath, implode("\n", $content));
    }
    
    protected function buildHeader(string $transactionDate, string $storeID): string
    {
        return str_pad('**INIT**', 8, ' ', STR_PAD_RIGHT)
            . str_pad('MERCTRAN', 8, ' ', STR_PAD_RIGHT)
            . str_pad($transactionDate, 8, '0', STR_PAD_LEFT)
            . str_pad($storeID, 4, '0', STR_PAD_LEFT);
    }
    
    protected function buildLine(array $rowArr): string
    {
        // String birleştirme yerine array kullan - DAHA HIZLI
        return str_pad($rowArr['Procedure Name'] ?? '', 8, ' ', STR_PAD_LEFT)
            . str_pad($rowArr['Transaction-date'] ?? '', 8, '0', STR_PAD_LEFT)
            . str_pad($rowArr['StoreID'] ?? '', 4, '0', STR_PAD_LEFT)
            . str_pad($rowArr['Trans-Code (Cod-classe-Mov)'] ?? '', 4, '0', STR_PAD_LEFT)
            . str_pad($rowArr['EAN-number'] ?? '', 13, ' ', STR_PAD_LEFT)
            . '+' . str_pad((string)($rowArr['Quantity'] ?? '0'), 6, '0', STR_PAD_LEFT)
            . str_pad($rowArr['Sign of quantity'] ?? '', 1, ' ', STR_PAD_LEFT)
            . str_pad($rowArr['Recipient StoreID'] ?? '', 4, '0', STR_PAD_LEFT)
            . str_pad($rowArr['Delivery number'] ?? '', 16, ' ', STR_PAD_LEFT)
            . str_pad($rowArr['Delivery date'] ?? '', 8, '0', STR_PAD_LEFT)
            . str_pad($rowArr['Package EAN-number'] ?? '', 13, '0', STR_PAD_LEFT)
            . str_pad($rowArr['Transfer request EAN number'] ?? '', 13, '0', STR_PAD_LEFT)
            . str_pad($rowArr['TO number'] ?? '', 9, '0', STR_PAD_LEFT)
            . str_pad($rowArr['Return number'] ?? '', 9, '0', STR_PAD_LEFT)
            . str_pad($rowArr['Reconditioning flag'] ?? '', 1, '0', STR_PAD_LEFT)
            . str_pad($rowArr['Currency code'] ?? '', 3, ' ', STR_PAD_LEFT)
            . str_pad($rowArr['Unit cost price'] ?? '', 15, ' ', STR_PAD_LEFT)
            . str_pad($rowArr['Cause'] ?? '', 4, ' ', STR_PAD_LEFT)
            . str_pad($rowArr['Cause description'] ?? '', 25, ' ', STR_PAD_LEFT);
    }
    
    protected function buildTrailer(int $totalRows, string $transactionDate, string $storeID): string
    {
        return str_pad('**FINE**', 8, ' ', STR_PAD_RIGHT)
            . str_pad('VENDTRAN', 8, ' ', STR_PAD_RIGHT)
            . str_pad($transactionDate, 8, '0', STR_PAD_LEFT)
            . str_pad($storeID, 4, '0', STR_PAD_LEFT)
            . str_pad($totalRows, 7, '0', STR_PAD_LEFT);
    }
}



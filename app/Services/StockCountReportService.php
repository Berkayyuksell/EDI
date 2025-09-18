<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StockCountReportService
{
    public function __construct() {
        ini_set('max_execution_time', 0);
    }




    public function generateReport(string $startDate,string $endDate, string $transactionDate , string $storeID)
    {
        
         $clientCode = '29991';
         $fileName = 'STK' . str_pad($clientCode, 9, '0', STR_PAD_LEFT) . '_' . date('Ymd') . '.TXT';

         $this->filePath = $fileName;
         Storage::disk('dataexchange')->put($this->filePath, '');

   

         $data = DB::connection('sqlsrv')->select('EXEC dbo.StockCount_EDI ?, ?', [$startDate, $endDate]);
        
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

    protected function buildHeader(string $transactionDate , $storeID):string{
         return str_pad('**INIT**', 8, ' ', STR_PAD_RIGHT)
            . str_pad('STOCOUNT', 8, ' ', STR_PAD_RIGHT)
            . str_pad($transactionDate ,8, '0', STR_PAD_LEFT)
            . str_pad($storeID, 4, '0', STR_PAD_LEFT);
    }
    
    protected function buildTrailer(int $totalRows, string $transactionDate, string $storeID): string
    {
        return str_pad('**FINE**', 8, ' ', STR_PAD_RIGHT)
            . str_pad('STOCOUNT', 8, ' ', STR_PAD_RIGHT)
            . str_pad($transactionDate, 8, '0', STR_PAD_LEFT)
            . str_pad($storeID, 4, '0', STR_PAD_LEFT)
            . str_pad($totalRows, 7, '0', STR_PAD_LEFT);
    }


    protected function buildLine(array $rowArr):string{
        $qty = $rowArr['Quantity Count'] ?? 0;

        $sign = ($qty < 0) ? '-' : '+';

        $absQty = abs((int)$qty);

        // 6 haneli sıfır doldur
        $formattedQty = str_pad($absQty, 6, '0', STR_PAD_LEFT);

        // En sona işareti koy
        $result = $sign .$formattedQty ;




         $line = 
                       'STOCOUNT'
                      .str_pad($rowArr['StoreID'] ?? '' ,4,' ',STR_PAD_LEFT)
                      .str_pad($rowArr['Inventory type'] ?? '' ,1,' ',STR_PAD_LEFT)
                      .str_pad($rowArr['Inventory date'] ?? '' ,8,' ',STR_PAD_LEFT)
                      .str_pad($rowArr['EAN-number'] ?? '' ,13,' ',STR_PAD_LEFT)
                      .$result
                      .'+'.str_pad($rowArr['Retail Price'] ?? '' ,10,'0',STR_PAD_LEFT);


        return $line;


    }


}
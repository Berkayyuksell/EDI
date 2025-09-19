<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ArrivalConfReportService
{
    public function __construct() {
        ini_set('max_execution_time', 0);
    }


    public function generateReport(string $startDate,string $endDate, string $transactionDate , string $storeID)
    {
        
         $clientCode = 'nnnnnnnnn';
         $fileName = 'AR1' . str_pad($clientCode, 9, '0', STR_PAD_LEFT) . '_' . date('Ymd') . '.TXT';

         $this->filePath = $fileName;
         Storage::disk('dataexchange')->put($this->filePath, '');

   

         $data = DB::connection('sqlsrv')->select('EXEC dbo.MissingEAN_EDI ?, ?', [$startDate, $endDate]);
        
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

    protected function buildHeader(string $transactionDate,string $StoreID): string
    {
        return str_pad('**INIT**', 8, ' ', STR_PAD_RIGHT)
            . str_pad('VENDTRAN', 8, ' ', STR_PAD_RIGHT)
            . str_pad($transactionDate, 8, '0', STR_PAD_LEFT)
            . str_pad($StoreID ?? '' ,4,' ',STR_PAD_LEFT);
    }





}
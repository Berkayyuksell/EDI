<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; 
use App\Services\SystemService;

class SalesReportService
{
    public function __construct(private SystemService $fileService) {
        ini_set('max_execution_time', 0);
        
    }
    


    public function generateReport(string $startDate, string $endDate, string $transactionDate, string $ClientCode)
    {
         $fileName = 'PSL' . str_pad($ClientCode, 9, '0', STR_PAD_LEFT) . '_' . $transactionDate . '.TXT';

         $this->filePath = $fileName;
         
        Storage::disk('dataexchange')->put($this->filePath, '');
        Storage::disk('dataexchange_backupOut')->put($this->filePath, '');

        $Stores = [
         'M06' => '5009',
         'M07' => '9282',
         'M08' => '6412',
         'INT01' => '9283',
         'M05' => '3957'
        ];
        foreach ($Stores as $NebimStoreID=>$StoreID) {
            $data = DB::connection('sqlsrv')->select('EXEC SALES_EDI ?, ?, ?', [$startDate, $endDate, $NebimStoreID]);
            $content = [];
        // Header 
        $content[] = $this->buildHeader($transactionDate,$StoreID);
        
        if(!empty($data)){
            foreach ($data as $row) {
                $rowArr = (array)$row;
                $content[] = $this->buildLine($rowArr,$StoreID,$transactionDate);
        }}
        // Trailer 
        $content[] = $this->buildTrailer(count($data), $transactionDate , $StoreID);
        
         Storage::disk('dataexchange')->append($this->filePath, implode("\n", $content));
         Storage::disk('dataexchange_backupOut')->append($this->filePath,implode("\n", $content));
        }

        
  
        

        
    }

    protected function buildHeader(string $transactionDate,string $StoreID): string
    {
        return str_pad('**INIT**', 8, ' ', STR_PAD_RIGHT)
            . str_pad('VENDTRAN', 8, ' ', STR_PAD_RIGHT)
            . str_pad($transactionDate, 8, '0', STR_PAD_LEFT)
            . str_pad($StoreID ?? '' ,4,' ',STR_PAD_LEFT);
    }

    protected function buildLine(array $rowArr,string $StoreID,$transactionDate): string
    {
        $operatorId = strlen($rowArr['Operator ID'] ?? '0001') > 4 
    ? '0001' 
        
    : str_pad($rowArr['Operator ID'] ?? '0001', 4, '0', STR_PAD_LEFT);

      $terminalId =strlen($rowArr['Terminal-ID'] ?? '00') > 2 ? '00'
       : str_pad($rowArr['Terminal-ID'] ?? '', 2, '0', STR_PAD_LEFT);


        $line = str_pad($rowArr['Procedure Name'] ?? '', 8, ' ', STR_PAD_LEFT)
            . str_pad($rowArr['Action'] ?? '', 1, '0', STR_PAD_LEFT)
            . $transactionDate
            . str_pad($StoreID ?? '' ,4,' ',STR_PAD_LEFT)
            . str_pad($rowArr['EAN-number'] ?? '', 13, ' ', STR_PAD_LEFT)
            . '+' . str_pad((string)($rowArr['Quantity'] ?? '0'), 6, '0', STR_PAD_LEFT)
            . str_pad($rowArr['Currency code'] ?? '', 3, ' ', STR_PAD_LEFT)
            . '+' . str_pad($rowArr['Value'] ?? '', 10, '0', STR_PAD_LEFT)
            . str_pad($rowArr['Discount Code'] ?? '', 2, ' ', STR_PAD_LEFT)
            . '+' . str_pad($rowArr['Total Discount Amount'] ?? '', 8, '0', STR_PAD_LEFT)
            . $operatorId
            . $terminalId
            . str_pad($rowArr['Transaction number'] ?? '', 4, '0', STR_PAD_LEFT)
            . str_pad($rowArr['SKU type of registration'] ?? '',1,' ',STR_PAD_LEFT)
            . str_pad($rowArr['Type of registration of price'] ?? '',1,' ',STR_PAD_LEFT);

        for ($i = 1; $i <= 10; $i++) {
            $line .= str_pad($rowArr["Discount Code $i"] ?? '', 2, ' ', STR_PAD_LEFT)
                . '+' . str_pad($rowArr["Discount Amount $i"] ?? '', 8, '0', STR_PAD_LEFT)
                . str_pad($rowArr["Skeme code $i"] ?? '', 8, ' ', STR_PAD_LEFT)
                . str_pad($rowArr["Promotion code $i"] ?? '', 8, ' ', STR_PAD_LEFT);
        }

        $line .= str_pad($rowArr['Card-ID'] ?? '', 25, ' ', STR_PAD_LEFT)
            . str_pad($rowArr['Supplier Flag'] ?? '', 1, ' ', STR_PAD_LEFT)
            . str_pad($rowArr['Time of ticket issue'] ?? '', 4, '0', STR_PAD_LEFT);

        //Log::error($rowArr);
        return $line;
    }

    protected function buildTrailer(int $totalRows, string $transactionDate, string $StoreID): string
    {
        return str_pad('**FINE**', 8, ' ', STR_PAD_RIGHT)
            . str_pad('VENDTRAN', 8, ' ', STR_PAD_RIGHT)
            . str_pad($transactionDate, 8, '0', STR_PAD_LEFT)
            . str_pad($StoreID ?? '' ,4,' ',STR_PAD_LEFT)
            . str_pad($totalRows, 7, '0', STR_PAD_LEFT);
    }
}

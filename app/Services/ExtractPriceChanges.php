<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExtractPriceChanges
{
    public function extractData(string $fileName,string $StoreID, string $NebimStoreID,$numberOfTransmission): void
    {
        $filePath = $fileName;

        if (!file_exists($filePath)) {
            Log::error("Dosya bulunamadı: $filePath");
            echo "Dosya bulunamadı: $filePath\n";
            return;
        }

        $handle = fopen($filePath, "r");
        if (!$handle) {
            Log::error("Dosya açılamadı: $filePath");
            echo "Dosya açılamadı: $filePath\n";
            return;
        }

        $priceRows = [];
        $lineNumber = 0;

        while (($line = fgets($handle)) !== false) {
            $lineNumber++;
            $line = rtrim($line);

            $procedureName = substr($line, 0, 8);

            // Header ve trailer skip
            if ($procedureName === '**INIT**' || $procedureName === '**FINE**') {
                continue;
            }

            // PRCCHNGS kayıtlarını işle
            if (trim($procedureName) === 'PRCCHNGS') {
                try {
                    $this->processPriceChangeRecord($line, $priceRows,$numberOfTransmission,$NebimStoreID);
                } catch (\Exception $e) {
                    Log::error("PRCCHNGS kaydı işlenirken hata (Satır $lineNumber): " . $e->getMessage());
                    continue;
                }
            }
        }

        fclose($handle);

        $this->saveToDatabase($priceRows);

        echo "Toplam PRICE CHANGE kayıt sayısı: " . count($priceRows) . PHP_EOL;
    }

    private function processPriceChangeRecord(string $line, array &$rows,$numberOfTransmission,$NebimStoreID): void
    {
        $rows[] = [
            'procedure_name' => trim(substr($line, 0, 8)),
            'item_number' => ltrim(substr($line, 8, 9),'0'),
            'price_start_date' => trim(substr($line, 17, 8)),
            'currency_code_1' => trim(substr($line, 25, 3)),
            'new_retail_price_1' => $this->parsePrice(substr($line, 28, 11)),
            'currency_code_2' => trim(substr($line, 39, 3)),
            'new_retail_price_2' => $this->parsePrice(substr($line, 42, 11)),
            'currency_code_3' => trim(substr($line, 53, 3)),
            'old_retail_price_3' => $this->parsePrice(substr($line, 56, 11)),
            'cause' => ltrim(substr($line, 67, 4),'0'),
            'cause_description' => trim(substr($line, 71, 25)),
            'discount_percentage' => $this->parse_percentage(substr($line, 96, 4)),
            'expiry_date' => trim(substr($line, 100, 8)),
            'variation_type' => ltrim(substr($line, 108, 4),'0'),
            'forced_price' => trim(substr($line, 112, 1)),
            'created_at' => now(),
            'updated_at' => now(),
            'numberOfTransmission' => $numberOfTransmission,
            'NebimStoreID' => $NebimStoreID,
        ];
    }   


     function parse_percentage(string $value): float
    {        
        $intValue = ltrim($value,'0');
        $numbericValue = (float)$intValue;
        $floatValue = $intValue / 100;  
        return $floatValue;
    }

      private function parsePrice(string $value): ?float
    {
        $value = trim($value);
        if (empty($value) || $value === '+0000000000' || $value === '00000000000') {
            return null;
        }
        
        $sign = 1;
        if (substr($value, 0, 1) === '+') {
            $value = substr($value, 1);
        } elseif (substr($value, 0, 1) === '-') {
            $sign = -1;
            $value = substr($value, 1);
        }
        

        $numericValue = (float)$value;
        return ($numericValue / 100) * $sign;
    }




    private function saveToDatabase(array $rows): void
    {
        $chunkSize = 5;

        if (!empty($rows)) {
            foreach (array_chunk($rows, $chunkSize) as $chunk) {
                try {
                    DB::table('zt_pricechange_detail')->insert($chunk);
                } catch (\Exception $e) {
                    Log::error("PRCCHNGS kayıtları kaydedilirken hata: " . $e->getMessage());
                }
            }
        }
    }
}

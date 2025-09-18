<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExtractItemList
{
    public function extractData(string $fileName,string $StoreID, string $NebimStoreID,string $numberOfTransmission): void
    {
        $filePath = $fileName;
        
        if (!file_exists($filePath)) {
            Log::error("Dosya bulunamadı: $filePath");
            echo "Dosya bulunamadı: $filePath" . PHP_EOL;
            return;
        }

        $handle = fopen($filePath, "r");
        if (!$handle) {
            Log::error("Dosya açılamadı: $filePath");
            echo "Dosya açılamadı: $filePath" . PHP_EOL;
            return;
        }

        $itemRows = [];
        $barcodeRows = [];
        $lineNumber = 0;

        while (($line = fgets($handle)) !== false) {
            $lineNumber++;
            $line = rtrim($line); // Satır sonu karakterlerini temizle
            
           

            $procedureName = substr($line, 0, 8);
            
            // Header ve trailer kayıtlarını atla
            if ($procedureName === '**INIT**' || $procedureName === '**FINE**') {
                continue;
            }

            // ITEMLIST kayıtlarını işle
            if (trim($procedureName) === 'ITEMLIST') {
                try {
                    $this->processItemRecord($line, $itemRows,$numberOfTransmission,$NebimStoreID,);
                } catch (\Exception $e) {
                    Log::error("ITEMLIST kaydı işlenirken hata (Satır $lineNumber): " . $e->getMessage());
                    continue;
                }
            }
            // BARCODE kayıtlarını işle
            elseif (trim($procedureName) === 'BARCODE' || substr($line, 0, 7) === 'BARCODE') {
                try {
                    $this->processBarcodeRecord($line, $barcodeRows);
                } catch (\Exception $e) {
                    Log::error("BARCODE kaydı işlenirken hata (Satır $lineNumber): " . $e->getMessage());
                    continue;
                }
            }
        }

        fclose($handle);

        // Verileri veritabanına kaydet
        $this->saveToDatabase($itemRows, $barcodeRows);
        
        echo "Toplam işlenen kayıt sayısı: " . (count($itemRows) + count($barcodeRows)) . PHP_EOL;
        echo "ITEM kayıtları: " . count($itemRows) . PHP_EOL;
        echo "BARCODE kayıtları: " . count($barcodeRows) . PHP_EOL;
    }

    private function processItemRecord(string $line, array &$itemRows,string $numberOfTransmission,string $NebimStoreID): void
    {
        

        $itemRows[] = [
            'procedure_name' => trim(substr($line, 0, 8)),
            'action' => trim(substr($line, 8, 1)),
            'item_number' => ltrim(substr($line, 9, 9), '0'),
            'item_description' => trim(substr($line, 18, 25)),
            'goods_type' => ltrim(substr($line, 43, 9),0),
            'colour' => trim(substr($line, 52, 25)),
            'composition' => trim(substr($line, 77, 78)),
            'vat_code' => trim(substr($line, 155, 4)),
            'function_description' => trim(substr($line, 159, 25)),
            'subdepartment_code' => trim(substr($line, 184, 4)),
            'subdepartment_description' => trim(substr($line, 188, 25)),
            'department_code' => trim(substr($line, 213, 4)),
            'department_description' => trim(substr($line, 217, 25)),
            'season_code' => trim(substr($line, 242, 2)),
            'season_description' => trim(substr($line, 244, 25)),
            'item_gender' => trim(substr($line, 269, 3)),
            'item_gender_description' => trim(substr($line, 272, 15)),
            'country_of_origin' => trim(substr($line, 287, 3)),
            'price_validity_start_date' => trim(substr($line, 290, 8)),
            'currency_code_1' => trim(substr($line, 298, 3)),
            'retail_price_1' => $this->parsePrice(substr($line, 301, 11)),
            'currency_code_2' => trim(substr($line, 312, 3)),
            'retail_price_2' => $this->parsePrice(substr($line, 315, 11)),
            'vat' => $this->parsePrice(substr($line, 326, 5)),
            'customs_description' => ltrim(substr($line, 331, 4),'0'),
            'division_of_goods' => trim(substr($line, 335, 4)),
            'year' => trim(substr($line, 339, 4)),
            'category_code' => ltrim(substr($line, 343, 4),'0'),
            'category_description' => trim(substr($line, 347, 25)),
            'colourstory_prefix' => trim(substr($line, 372, 2)),
            'colourstory_code' => trim(substr($line, 374, 20)),
            'colourstory_description' => trim(substr($line, 394, 25)),
            'commercial_reference' => trim(substr($line, 419, 15)),
            'function_code' => ltrim(substr($line, 434, 5),'0'),
            'size_group_code_1' => ltrim(substr($line, 439, 4),'0'),
            'size_group_code_2' => ltrim(substr($line, 443, 4),'0'),
            'italian_customs_code' => trim(substr($line, 447, 9)),
            'net_weight' => $this->parseDecimal(substr($line, 456, 11), 3),
            'gross_weight' => $this->parseDecimal(substr($line, 467, 11), 3),
            'knitted_woven' => trim(substr($line, 478, 1)),
            'kit' => trim(substr($line, 479, 9)),
            'local_month' => trim(substr($line, 488, 9)),
            'composition_code_9' => ltrim(substr($line, 497, 9),'0'),
            'item_master' => ltrim(substr($line, 506, 9),'0'),
            'planned_sales_date' => trim(substr($line, 515, 8)),
            'period' => trim(substr($line, 523, 1)),
            'brand_code' => trim(substr($line, 524, 4)),
            'brand_description' => trim(substr($line, 528, 25)),
            'unit_cost_price' => $this->parseDecimal(substr($line, 553, 15), 6),
            'cost_price_1_currency' => trim(substr($line, 568, 3)),
            'estimated_cost_price_1' => $this->parseDecimal(substr($line, 571, 15), 6),
            'cost_price_2_currency' => trim(substr($line, 586, 3)),
            'estimated_cost_price_2' => $this->parseDecimal(substr($line, 589, 15), 6),
            'item_master_description' => trim(substr($line, 604, 25)),
            'segment_code' => ltrim(substr($line, 629, 4),'0'),
            'segment_description' => trim(substr($line, 633, 25)),
            'suppliers_item_description' => trim(substr($line, 658, 25)),
            'created_at' => now(),
            'updated_at' => now(),
            'numberOfTransmission' => $numberOfTransmission,
            'NebimStoreID'  => $NebimStoreID,
        ];
    }

    private function processBarcodeRecord(string $line, array &$barcodeRows): void
    {
        
        $barcodeRows[] = [
            'procedure_name' => 'BARCODE',
            'barcode_data' => trim($line),
            'line_length' => strlen($line),
            'created_at' => now(),
            'updated_at' => now(),
        ];
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

    private function parseDecimal(string $value, int $decimalPlaces): ?float
    {
        $value = trim($value);
        if (empty($value) || str_repeat('0', strlen($value)) === $value) {
            return null;
        }
        
        // İşaret karakterini kontrol et
        $sign = 1;
        if (substr($value, 0, 1) === '+') {
            $value = substr($value, 1);
        } elseif (substr($value, 0, 1) === '-') {
            $sign = -1;
            $value = substr($value, 1);
        }
        
        $numericValue = (float)$value;
        $divisor = pow(10, $decimalPlaces);
        return ($numericValue / $divisor) * $sign;
    }

    private function saveToDatabase(array $itemRows, array $barcodeRows): void
    {
        $chunkSize = 5;

         if (!empty($itemRows)) {
        foreach (array_chunk($itemRows, $chunkSize) as $chunk) {
            try {
                // Güncellenecek tüm sütunları belirle (item_number hariç)
                $updateColumns = array_diff(array_keys($chunk[0]), ['item_number']);

                DB::table('zt_items_detail')->upsert(
                    $chunk,
                    ['item_number'],   // unique sütun
                    $updateColumns     // tüm diğer sütunlar güncellenecek
                );
            } catch (\Exception $e) {
                Log::error("ITEM kayıtları kaydedilirken hata: " . $e->getMessage());
            }
            }
        }

        // BARCODE kayıtlarını kaydet (eğer ayrı bir tablo kullanıyorsanız)
        if (!empty($barcodeRows)) {
            foreach (array_chunk($barcodeRows, $chunkSize) as $chunk) {
                try {
                    DB::table('zt_barcode_detail')->insert($chunk);
                } catch (\Exception $e) {
                    Log::error("BARCODE kayıtları kaydedilirken hata: " . $e->getMessage());
                }
            }
        }
    }
}
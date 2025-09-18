<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExtractEanList
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

        $eanRows = [];
        $lineNumber = 0;

        while (($line = fgets($handle)) !== false) {
            $lineNumber++;
            $line = rtrim($line);

            $procedureName = substr($line, 0, 8);

            // Header ve trailer skip
            if ($procedureName === '**INIT**' || $procedureName === '**FINE**') {
                continue;
            }

            // EANLIST kayıtlarını işle
            if (trim($procedureName) === 'EANLIST*') {
                try {
                    $this->processEanRecord($line, $eanRows,$numberOfTransmission,$NebimStoreID);
                } catch (\Exception $e) {
                    Log::error("EANLIST kaydı işlenirken hata (Satır $lineNumber): " . $e->getMessage());
                    continue;
                }
            }
        }

        fclose($handle);

        $this->saveToDatabase($eanRows);

        echo "Toplam EAN kayıt sayısı: " . count($eanRows) . PHP_EOL;
    }

    private function processEanRecord(string $line, array &$rows,$numberOfTransmission,$NebimStoreID): void
    {
        $rows[] = [
            'procedure_name' => trim(substr($line, 0, 8)),
            'action' => trim(substr($line, 8, 1)),
            'item_number' => ltrim(substr($line, 9, 9), '0'),
            'size' => ltrim(substr($line, 18, 3),'0'),
            'size_description' => ltrim(substr($line, 21, 5),'0'),
            'sku_number' => trim(substr($line, 26, 7)),
            'ean_upc_number' => trim(substr($line, 33, 13)),
            'italian_customs_code' => trim(substr($line, 46, 10)),
            'customs_gender' => trim(substr($line, 56, 1)),
            'customs_gender_description' => trim(substr($line, 57, 15)),
            'european_size' => trim(substr($line, 72, 10)),
            'filler1' => trim(substr($line, 82, 4)),
            'filler2' => trim(substr($line, 86, 7)),
            'us_size' => trim(substr($line, 93, 10)),
            'filler3' => trim(substr($line, 103, 10)),
            'mexican_size' => trim(substr($line, 113, 10)),
            'size_in_kilograms' => trim(substr($line, 123, 15)),
            'local_size_1' => trim(substr($line, 138, 10)),
            'local_size_2' => trim(substr($line, 148, 10)),
            'chinese_size' => trim(substr($line, 158, 25)),
            'size_in_centimetres' => trim(substr($line, 183, 25)),
            'size_in_inches' => trim(substr($line, 208, 25)),
            'created_at' => now(),
            'updated_at' => now(),
            'numberOfTransmission' => $numberOfTransmission,
            'NebimStoreID' => $NebimStoreID,
        ];
    }

    private function saveToDatabase(array $rows): void
    {
        $chunkSize = 5;

        if (!empty($rows)) {
            foreach (array_chunk($rows, $chunkSize) as $chunk) {
                try {
                    DB::table('zt_eanlist_detail')->insert($chunk);
                } catch (\Exception $e) {
                    Log::error("EANLIST kayıtları kaydedilirken hata: " . $e->getMessage());
                }
            }
        }
    }
}

<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExtractGoodsType
{
    public function extractData(string $fileName,$numberOfTransmission): void
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

        $goodsRows = [];
        $lineNumber = 0;

        while (($line = fgets($handle)) !== false) {
            $lineNumber++;
            $line = rtrim($line);

            $procedureName = substr($line, 0, 8);

            // Header ve trailer skip
            if ($procedureName === '**INIT**' || $procedureName === '**FINE**') {
                continue;
            }

            // UNMER*** kayıtlarını işle
            if (trim($procedureName) === 'UNMER***') {
                try {
                    $this->processGoodsTypeRecord($line, $goodsRows,$numberOfTransmission);
                } catch (\Exception $e) {
                    Log::error("UNMER*** kaydı işlenirken hata (Satır $lineNumber): " . $e->getMessage());
                    continue;
                }
            }
        }

        fclose($handle);

        $this->saveToDatabase($goodsRows);

        echo "Toplam GOODS TYPE kayıt sayısı: " . count($goodsRows) . PHP_EOL;
    }

    private function processGoodsTypeRecord(string $line, array &$rows,$numberOfTransmission): void
    {
        $rows[] = [
            'procedure_name' => trim(substr($line, 0, 8)),
            'goods_type' => ltrim(substr($line, 8, 9),'0'),
            'goods_type_description' => trim(substr($line, 17, 36)),
            'created_at' => now(),
            'updated_at' => now(),
            'numberOfTransmission' => $numberOfTransmission,
        ];
    }

    private function saveToDatabase(array $rows): void
    {
        $chunkSize = 5;

        if (!empty($rows)) {
            foreach (array_chunk($rows, $chunkSize) as $chunk) {
                try {
                    DB::table('zt_goodstype_detail')->insert($chunk);
                } catch (\Exception $e) {
                    Log::error("UNMER*** kayıtları kaydedilirken hata: " . $e->getMessage());
                }
            }
        }
    }
}

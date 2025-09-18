<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExtractCustomsDescription
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

        $custRows = [];
        $lineNumber = 0;

        while (($line = fgets($handle)) !== false) {
            $lineNumber++;
            $line = rtrim($line);

            $procedureName = substr($line, 0, 8);

            // Header ve trailer skip
            if ($procedureName === '**INIT**' || $procedureName === '**FINE**') {
                continue;
            }

            // CUSDESGD kayıtlarını işle
            if (trim($procedureName) === 'CUSDESGD') {
                try {
                    $this->processCustRecord($line, $custRows,$numberOfTransmission);
                } catch (\Exception $e) {
                    Log::error("CUSDESGD kaydı işlenirken hata (Satır $lineNumber): " . $e->getMessage());
                    continue;
                }
            }
        }

        fclose($handle);

        $this->saveToDatabase($custRows);

        echo "Toplam CUSTOMS DESCRIPTION kayıt sayısı: " . count($custRows) . PHP_EOL;
    }

    private function processCustRecord(string $line, array &$rows,$numberOfTransmission): void
    {
        $rows[] = [
            'procedure_name' => trim(substr($line, 0, 8)),
            'customs_code' => ltrim(substr($line, 8, 4),'0'),
            'customs_description' => trim(substr($line, 12, 60)),
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
                    DB::table('zt_custdesc_detail')->insert($chunk);
                } catch (\Exception $e) {
                    Log::error("CUSDESGD kayıtları kaydedilirken hata: " . $e->getMessage());
                }
            }
        }
    }
}

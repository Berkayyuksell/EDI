<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExtractCMPS
{
    public function extractData(string $fileName,$numberOfTransmission): void
    {
        // Dosyanın tam yolunu al
        $filePath = $fileName;


        

        if (!file_exists($filePath)) {
            echo "Dosya bulunamadı: $filePath" . PHP_EOL;
            return;
        }

        $handle = fopen($filePath, "r");
        if (!$handle) {
            echo "Dosya açılamadı: $filePath" . PHP_EOL;
            return;
        }

        $rows = [];

        while (($line = fgets($handle)) !== false) {
            $header = substr($line, 0, 8);

            // Sadece DETAIL kayıtlarını kaydet
            if ($header != '**INIT**' && $header != '**FINE**') {
                $rows[] = [
                    'procedure_name'       => trim(substr($line, 0, 8)),
                    'composition_code_old' => trim(substr($line, 8, 4)),
                    'composition_desc'     => trim(substr($line, 12, 78)),
                    'composition_code'     => ltrim(substr($line, 90, 9),'0'),
                    'created_at'           => now(),
                    'updated_at'           => now(),
                    'numberOfTransmission' => $numberOfTransmission
                ];
            }
        }

        fclose($handle);

        $chunkSize = 100; // her seferde 500 satır insert et
foreach (array_chunk($rows, $chunkSize) as $chunk) {
    DB::table('zt_item_detail')->insert($chunk);
}
    }
}
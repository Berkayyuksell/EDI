<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExtractPackingList
{
    public function extractData(string $fileName): void
    {
        if (!file_exists($fileName)) {
            Log::error("Dosya bulunamadı: $fileName");
            echo "Dosya bulunamadı: $fileName\n";
            return;
        }

        $handle = fopen($fileName, "r");
        if (!$handle) {
            Log::error("Dosya açılamadı: $fileName");
            echo "Dosya açılamadı: $fileName\n";
            return;
        }

        $headerRows = [];
        $detailRows = [];
        $packageRows = [];
        $lineNumber = 0;

        while (($line = fgets($handle)) !== false) {
            $lineNumber++;
            $line = rtrim($line);

            $recordType = substr($line, 0, 2);

            try {
                if ($recordType === 'TS') {
                    $this->processHeader($line, $headerRows);
                } elseif ($recordType === 'DT') {
                    $this->processDetail($line, $detailRows);
                } elseif ($recordType === 'PK') {
                    $this->processPackage($line, $packageRows);
                }
            } catch (\Exception $e) {
                Log::error("Satır $lineNumber hata: " . $e->getMessage());

            }
        }

        fclose($handle);

        $this->saveToDatabase($headerRows, $detailRows, $packageRows);

        echo "HEADER: " . count($headerRows) . "\n";
        echo "DETAIL: " . count($detailRows) . "\n";
        echo "PACKAGE: " . count($packageRows) . "\n";
    }

    private function processHeader(string $line, array &$rows): void
    {
        $rows[] = [
            'record_type' => 'TS',
            'store_id_deliverer' => ltrim(substr($line, 2, 4),0),
            'store_id_receiver' => trim(substr($line, 6, 4)),
            'bill_of_transport' => ltrim(substr($line, 10, 9),0),
            'bill_of_transport_date' => trim(substr($line, 19, 8)),
            'row_number' => (int)trim(substr($line, 27, 5)),
            'deliverer' => trim(substr($line, 32, 70)),
            'recipient' => trim(substr($line, 102, 70)),
            'importer' => trim(substr($line, 172, 70)),
            'vat_number' => trim(substr($line, 242, 11)),
            'goods_collecting_address' => trim(substr($line, 253, 95)),
            'isApprove' => '0',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function processDetail(string $line, array &$rows): void
    {
        $rows[] = [
            'record_type' => 'DT',
            'store_id_deliverer' => ltrim(substr($line, 2, 4),'0'),
            'store_id_receiver' => trim(substr($line, 6, 4)),
            'bill_of_transport' => ltrim(substr($line, 10, 9),'0'),
            'bill_of_transport_date' => trim(substr($line, 19, 8)),
            'row_number' => (int)trim(substr($line, 27, 5)),
            'pallet_code' => trim(substr($line, 32, 13)),
            'distribution_number' => ltrim(substr($line, 45, 9),'0'),
            'package_grouping_number' => ltrim(substr($line, 54, 9),'0'),
            'number_of_packages' => ltrim(substr($line, 63, 9),'0'),
            'sku_number' => ltrim(substr($line, 72, 9),'0'),
            'item_description' => trim(substr($line, 81, 51)),
            'item_composition' => trim(substr($line, 132, 78)),
            'gender_description' => trim(substr($line, 210, 10)),
            'fabric' => trim(substr($line, 220, 10)),
            'country_of_origin' => trim(substr($line, 230, 3)),
            'customs_code' => ltrim(substr($line, 233, 9),'0'),
            'quantity' => (int)trim(substr($line, 242, 9)),
            'global_gross_weight' => ltrim(substr($line, 251, 11),'0'),
            'global_net_weight' => ltrim(substr($line, 262, 11),'0'),
            'unit_cost_price' => ltrim(substr($line, 273, 11),'0'),
            'ue_extra' => trim(substr($line, 284, 1)),
            'italian_customs_code' => ltrim(substr($line, 285, 9),'0'),
            'unit_gross_weight' => ltrim(substr($line, 294, 11),'0'),
            'cost_currency_code' => trim(substr($line, 305, 3)),
            'invoice_number' => ltrim(substr($line, 308, 10),'0'),
            'invoice_date' => trim(substr($line, 318, 8)),
            'ean_number' => trim(substr($line, 326, 13)),
            'other_pallet_codes' => trim(substr($line, 339, 13*12)),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function processPackage(string $line, array &$rows): void
    {
        $rows[] = [
            'record_type' => 'PK',
            'store_id_deliverer' => ltrim(substr($line, 2, 4),'0'),
            'store_id_receiver' => trim(substr($line, 6, 4)),
            'bill_of_transport' => ltrim(substr($line, 10, 9),'0'),
            'bill_of_transport_date' => trim(substr($line, 19, 8)),
            'row_number' => (int)trim(substr($line, 27, 5)),
            'package_grouping_number' => ltrim(substr($line, 32, 9),'0'),
            'box_ean_number' => trim(substr($line, 41, 13)),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function parseDecimal(string $value, int $decimalPlaces): ?float
    {
        $value = trim($value);
        if (empty($value) || str_repeat('0', strlen($value)) === $value) {
            return null;
        }

        $sign = 1;
        if ($value[0] === '-') {
            $sign = -1;
            $value = substr($value, 1);
        } elseif ($value[0] === '+') {
            $value = substr($value, 1);
        }

        $numericValue = (float)$value;
        return ($numericValue / pow(10, $decimalPlaces)) * $sign;
    }

    private function saveToDatabase(array $headerRows, array $detailRows, array $packageRows): void
    {
        $chunkSize = 5;

        if (!empty($headerRows)) {
            foreach (array_chunk($headerRows, $chunkSize) as $chunk) {
                DB::table('zt_packing_header')->insert($chunk);
            }
        }

        if (!empty($detailRows)) {
            foreach (array_chunk($detailRows, $chunkSize) as $chunk) {
                DB::table('zt_packing_detail')->insert($chunk);
            }
        }

        if (!empty($packageRows)) {
            foreach (array_chunk($packageRows, $chunkSize) as $chunk) {
                DB::table('zt_packing_package')->insert($chunk);
            }
        }
    }
}

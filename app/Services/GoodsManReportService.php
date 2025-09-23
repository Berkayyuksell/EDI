<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GoodsManReportService
{
    protected string $filePath;

    public function __construct() {
        ini_set('max_execution_time', 0);
    }

    public function generateReport()
    {
        $transactionDateTime = date('Ymd'); 

        // isSend = 0 olan kayıtları çek
        $records = DB::table('package_reports')
            ->where('isSend', 0)
            ->orderBy('store_id_receiver')
            ->get();

        if ($records->isEmpty()) {
            return null; // veri yoksa dosya oluşturma
        }

        // Dosya adı
        $this->filePath = 'PAD' . '000029991_' . $transactionDateTime . '.TXT';

        $content = [];

        // Store ID’ye göre gruplama
        $grouped = $records->groupBy('store_id_receiver');

        foreach ($grouped as $storeId => $storeRecords) {
            // Header
            $content[] = $this->buildHeader($transactionDateTime, $storeId);

            // Detail
            foreach ($storeRecords as $row) {
                $content[] = $this->buildDetailLine($row,$transactionDateTime);
            }

            // Trailer
            $content[] = $this->buildTrailer($transactionDateTime, count($storeRecords), $storeId);

            // Kayıtları güncelle
            DB::table('package_reports')
                ->whereIn('id', $storeRecords->pluck('id'))
                ->update(['isSend' => 1]);
        }

        // Dosyaya yaz
        Storage::disk('dataexchange')->put($this->filePath, implode("\n", $content));

        return $this->filePath;
    }

    protected function buildHeader(string $transactionDateTime, string $storeId): string
    {
        return str_pad('**INIT**', 8, ' ', STR_PAD_RIGHT)
            . str_pad('MERCTRAN', 8, ' ', STR_PAD_RIGHT)
            . $transactionDateTime
            . str_pad($storeId, 4, '0', STR_PAD_LEFT);
    }

    protected function buildDetailLine(object $row,string $transactionDateTime): string
    {
         $causeCode = '0000'; 
    $comment = substr($row->comment ?? '', 0, 25); // default comment

    switch ($row->transaction_code) {
        case '0151':
            if (empty($row->cause_code) || $row->cause_code === '0070') {
                $causeCode = '0070';
                $comment = 'Unsellable goods';
            }
            break;
        case '0152': // Örnek flood transaction
            $causeCode = '0071';
            $comment = 'Flood';
            break;
        case '0153': // Örnek theft transaction
            $causeCode = '0072';
            $comment = 'Theft';
            break;
        default:
            $causeCode = '0000';
            break;
    }

        return str_pad('MERCTRAN', 8, ' ', STR_PAD_RIGHT)
            . str_pad($row->store_id_receiver ?? '0', 4, '0', STR_PAD_LEFT)
            . $transactionDateTime
            . str_pad($row->transaction_code ?? '', 4, ' ', STR_PAD_RIGHT)
            . str_pad($row->ean_number ?? '0', 13, '0', STR_PAD_LEFT)
            . '+'
            . str_pad($row->quantity ?? 0, 6, '0', STR_PAD_LEFT)
            . '+'
            . '0000'
            . str_pad($row->bill_of_transport ?? '0', 16, '0', STR_PAD_LEFT)
            . str_replace('-', '', $row->bill_of_transport_date ?? '00000000')
            . str_pad($row->box_ean_number ?? '0', 13, '0', STR_PAD_LEFT)
            . str_repeat('0', 13) // Transfer request EAN
            . str_repeat('0', 9)  // TO number
            . str_repeat('0', 9)  // Return number
            . '0'                 
            . 'TRY'
            . str_pad(number_format((float)$row->retail_price,2,'',''), 15, '0', STR_PAD_LEFT)
            . str_pad($row->cause_code ?? '', 4, ' ', STR_PAD_RIGHT)
            . str_pad($comment, 25, ' ', STR_PAD_RIGHT);

    }

    protected function buildTrailer(string $transactionDateTime, int $recordCount, string $storeId): string
    {
        $totalRecords = $recordCount + 2; // header + detail + trailer
        return str_pad('**FINE**', 8, ' ', STR_PAD_RIGHT)
            . str_pad('MERCTRAN', 8, ' ', STR_PAD_RIGHT)
            . $transactionDateTime
            . str_pad($storeId, 4, '0', STR_PAD_LEFT)
            . str_pad($totalRecords, 7, '0', STR_PAD_LEFT);
    }
}

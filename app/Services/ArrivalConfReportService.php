<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ArrivalConfReportService
{
    protected string $filePath;

    public function __construct() {
        ini_set('max_execution_time', 0);
    }

    public function generateReport()
    {
        $transactionDateTime = date('Ymd'); 

        // isApprove = 1 ve isSend = 0 olan kayıtları çek
        $records = DB::table('zt_packing_header')
            ->where('isApprove', 1)
            ->where('isSend', 0)
            ->orderBy('store_id_receiver')
            ->get();

        // Eğer veri yoksa TXT oluşturma
        if ($records->isEmpty()) {
            return null; // boşsa hiçbir şey yapma
        }

        // Dosya adı
        $this->filePath = 'AR1'.'000029991_' . $transactionDateTime . '.TXT';

        $content = [];

        // Store ID'ye göre gruplama
        $grouped = $records->groupBy('store_id_receiver');

        foreach ($grouped as $storeId => $storeRecords) {
            // Header
            $content[] = $this->buildHeader($transactionDateTime, $storeId);

            // Detaylar
            foreach ($storeRecords as $row) {
                $content[] = $this->buildDetailLine($row,$transactionDateTime);
            }

            // Trailer
            $content[] = $this->buildTrailer($transactionDateTime, count($storeRecords), $storeId);

            // Gruplandıktan sonra bu store_id'nin kayıtlarını isSend = 1 yap
            DB::table('zt_packing_header')
                ->whereIn('id', $storeRecords->pluck('id'))
                ->update(['isSend' => 1]);
        }

        // Dosyaya yaz
        Storage::disk('dataexchange')->put($this->filePath, implode("\n", $content));
        Storage::disk('dataexchange_backupOut')->append($this->filePath,implode("\n", $content));

        return $this->filePath; // oluşturulan dosya ismini döndürüyor
    }

    protected function buildHeader(string $transactionDateTime, string $storeId): string
    {
        return str_pad('**INIT**', 8, ' ', STR_PAD_RIGHT)
            . str_pad('ARRICONF', 8, ' ', STR_PAD_RIGHT)
            . $transactionDateTime
            . str_pad($storeId, 4, '0', STR_PAD_LEFT);
    }

    protected function buildDetailLine(object $row,string $transactionDateTime): string
    {
        return str_pad('ARRICONF', 8, ' ', STR_PAD_RIGHT)
            . str_pad($row->store_id_receiver ?? '0', 4, '0', STR_PAD_LEFT)
            . $transactionDateTime
            . str_pad($row->bill_of_transport ?? '0', 9, '0', STR_PAD_LEFT)
            . str_pad($row->bill_of_transport_date ? date('Ymd', strtotime($row->bill_of_transport_date)) : '00000000', 8, '0', STR_PAD_LEFT)
            . '0000000000'
            . '00000000';
    }

    protected function buildTrailer(string $transactionDateTime, int $recordCount, string $storeId): string
    {
        $totalRecords = $recordCount + 2; // header + detaylar + trailer

        return str_pad('**FINE**', 8, ' ', STR_PAD_RIGHT)
            . str_pad('ARRICONF', 8, ' ', STR_PAD_RIGHT)
            . $transactionDateTime
            . str_pad($storeId, 4, '0', STR_PAD_LEFT)
            . str_pad($totalRecords, 7, '0', STR_PAD_LEFT);
    }
}

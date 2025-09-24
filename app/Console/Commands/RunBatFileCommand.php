<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RunBatFileCommand extends Command
{
    /**
     * Komutun adı (php artisan run:bat)
     */
    protected $signature = 'run:bat';

    /**
     * Açıklama
     */
    protected $description = 'Dromos bat dosyasını çalıştırır';

    public function handle()
    {
        $workingDir = 'C:\\DROMOS\\DromosCLI';
		$batFile = 'C:\\DROMOS\\DromosCLI\\dromoscli.bat';
		$fullCommand = "cd /d \"$workingDir\" && call \"$batFile\"";

        exec($fullCommand, $output, $returnCode);

        if ($returnCode === 0) {
            Log::info('Bat dosyası başarıyla çalıştırıldı', ['output' => $output]);
            $this->info('Bat dosyası başarıyla çalıştırıldı');
        } else {
            Log::error('Bat dosyası çalıştırılırken hata oluştu', [
                'return_code' => $returnCode,
                'output' => $output
            ]);
            $this->error('Bat dosyası çalıştırılırken hata oluştu, loglara bak.');
        }

        return $returnCode;
    }
}
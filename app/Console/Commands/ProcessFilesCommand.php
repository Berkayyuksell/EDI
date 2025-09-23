<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Services\FileProcessorService;

class ProcessFilesCommand extends Command
{
    protected $signature = 'files:process';
    protected $description = 'Yeni dosyaları kontrol et ve işle';

    public function __construct(private FileProcessorService $processor)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        ini_set('memory_limit', '-1');
        $files = Storage::disk('dataexchangeIn')->files();
        foreach ($files as $file) {
            $this->processor->processFile(Storage::disk('dataexchangeIn')->path($file));
            
        }
     // $this->processor->runBatFİle();
        $this->processor->transferFileIn();
        


    }
}



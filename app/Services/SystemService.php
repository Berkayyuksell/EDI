<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SystemService
{

    public function runBat(string $batFileName): string
{
    $path = Storage::disk('DromosFile')->path($batFileName);

    $process = new Process([$path]);
    $process->run();

    if (!$process->isSuccessful()) {
        throw new ProcessFailedException($process);
    }

    return $process->getOutput();
}

    public function cleanDirectory(string $path): void
    {
        if (File::exists($path)) {
            File::cleanDirectory($path);
        }
    }

    /**
     * Storage klasöründe belirtilen dizindeki dosyaları siler
     */
    public function cleanStorageDirectory(string $disk, string $dir): void
    {
        Storage::disk($disk)->delete(Storage::disk($disk)->allFiles($dir));
    }
}

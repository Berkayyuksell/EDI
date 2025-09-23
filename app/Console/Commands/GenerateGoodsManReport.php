<?php

namespace App\Console\Commands;

use App\Services\GoodsManReportService; 
use Illuminate\Console\Command;

class GenerateGoodsManReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'goodsman:generate-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily GoodsMan report and export to TXT file';

    /**
     * Execute the console command.
     */
    public function handle(GoodsManReportService $service)
    {
        $filePath = $service->generateReport();

        if ($filePath) {
            $this->info("GoodsMan report generated successfully: " . $filePath);
        } else {
            $this->info("No records to generate report.");
        }
    }
}

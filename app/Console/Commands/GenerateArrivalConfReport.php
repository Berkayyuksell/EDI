<?php

namespace App\Console\Commands;

use App\Services\ArrivalConfReportService;
use Illuminate\Console\Command;

class GenerateArrivalConfReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'arrivalconf:generate-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Arrival Confirmation report and export to TXT file';

    /**
     * Execute the console command.
     */
    public function handle(ArrivalConfReportService $service)
    {
        $filePath = $service->generateReport();

        if ($filePath) {
            $this->info("Arrival Confirmation report generated successfully: " . $filePath);
        } else {
            $this->info("No records to generate Arrival Confirmation report.");
        }
    }
}

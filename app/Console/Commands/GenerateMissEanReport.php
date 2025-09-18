<?php

namespace App\Console\Commands;
use App\Services\MissingEANReportService;
use Illuminate\Console\Command;

class GenerateMissEanReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
     protected $signature = 'missean:generate-report 
                            {startDate} 
                            {endDate} 
                            {transactionDate} 
                            {storeID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily MissEAN report and export to TXT file';

    /**
     * Execute the console command.
     */
    public function handle(MissingEANReportService $service)
    {
         $startDate = $this->argument('startDate');
        $endDate = $this->argument('endDate');
        $transactionDate = $this->argument('transactionDate');
        $storeID = $this->argument('storeID');
    
        $service->generateReport($startDate, $endDate, $transactionDate, $storeID);

        $this->info("miss ean report generated successfully!");
    }
}

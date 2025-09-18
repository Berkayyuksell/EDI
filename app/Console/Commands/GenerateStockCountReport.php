<?php

namespace App\Console\Commands;
use App\Services\StockCountReportService;

use Illuminate\Console\Command;

class GenerateStockCountReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
      protected $signature = 'stockcount:generate-report 
                            {startDate} 
                            {endDate} 
                            {transactionDate} 
                            {storeID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily sales report and export to TXT file';

    /**
     * Execute the console command.
     */
    public function handle(StockCountReportService $service)
    {
         $startDate = $this->argument('startDate');
        $endDate = $this->argument('endDate');
        $transactionDate = $this->argument('transactionDate');
        $storeID = $this->argument('storeID');

        $service->generateReport($startDate, $endDate, $transactionDate, $storeID);

        $this->info("stock report generated successfully!");
    }
}

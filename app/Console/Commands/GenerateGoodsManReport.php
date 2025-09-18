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
    protected $signature = 'goodsman:generate-report 
                            {startDate} 
                            {endDate} 
                            {transactionDate} 
                            {storeID}';

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
        $startDate = $this->argument('startDate');
        $endDate = $this->argument('endDate');
        $transactionDate = $this->argument('transactionDate');
        $storeID = $this->argument('storeID');

        $service->generateReport($startDate, $endDate, $transactionDate, $storeID);

        $this->info("GoodsMan report generated successfully!");
    }
}

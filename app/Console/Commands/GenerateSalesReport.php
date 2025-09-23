<?php

namespace App\Console\Commands;
use App\Services\SalesReportService;
use Illuminate\Console\Command;
use App\Services\FileProcessorService;
use Illuminate\Support\Facades\Log;


class GenerateSalesReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
     protected $signature = 'sales:generate-report 
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
    public function handle(SalesReportService $service,FileProcessorService $processor)
    {
        ini_set('memory_limit', '-1');
        $startDate = $this->argument('startDate');
        $endDate = $this->argument('endDate');
        $transactionDate = $this->argument('transactionDate');
        $storeID = $this->argument('storeID');

        $service->generateReport($startDate, $endDate, $transactionDate, $storeID);
        
        Log::info('Raporlama basarili');
        $processor->transferFileOut();
        $processor->runBatFile();
           
    }
}

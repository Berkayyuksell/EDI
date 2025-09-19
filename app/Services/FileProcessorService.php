<?php
namespace App\Services;

use App\Services\ExtractItemList;
use App\Services\ExtractCMPS;
use App\Services\ExtractPackingList;
use App\Services\ExtractEanList;
use App\Services\ExtractCustomsDescription;
use App\Services\ExtractGoodsType;

use Illuminate\Support\Facades\Storage;


class FileProcessorService
{
    public function __construct(
        private ExtractItemList $itemService,
        private ExtractCMPS $cmpsService,
        private ExtractPackingList $packingListService,
        private ExtractEanList $eanService,
        private ExtractPriceChanges $priceChangeService,
        private ExtractCustomsDescription $customsDescService,
        private ExtractGoodsType $goodsTypeService,

    ) {}

    public function transferFileIn():void{
            $files = Storage::disk('dataexchangeIn')->files();

        foreach ($files as $file) {

            $contents = Storage::disk('dataexchangeIn')->get($file);

            Storage::disk('dataexchange_backupIn')->put($file, $contents);

            Storage::disk('dataexchangeIn')->delete($file);
        }

    }

    public function transferFileOut():void{
        $files = Storage::disk('dataexchange')->files();

        foreach ($files as $file){

            $contents = Storage::disk('dataexchange')->get($file);

            Storage::disk('dataexchange_backupOut')->put($file,$contents);

            Storage::disk('dataexchange')->delete($file);


        }
    }


    public function runBatFile(): void{
             $batFilePath = 'C:\Users\Mtsoft\Desktop\test.bat';; // Bat dosyasının yolu
    
    // Dosya varlığını kontrol et
    if (!file_exists($batFilePath)) {
        throw new \Exception('Bat dosyası bulunamadı: ' . $batFilePath);
    }
    
    // Bat dosyasını çalıştır
    exec($batFilePath, $output, $returnCode);
    
    // Sonuçları kontrol et
    if ($returnCode === 0) {
        \Log::info('Bat dosyası başarıyla çalıştırıldı', ['output' => $output]);
    } else {
        \Log::error('Bat dosyası çalıştırılırken hata oluştu', [
            'return_code' => $returnCode,
            'output' => $output
        ]);
    }


    }



    public function processFile(string $filePath): void
    {

    
        $fileName = basename($filePath);
        $prefix = substr($fileName, 0, 4);
        $prefixDDT = substr($fileName, 0,3);
        logger()->warning($filePath);
        
        $clientcode = substr($fileName,4,7);

        if(str_starts_with($clientcode,'_0') == true){
        $clientcode = substr($fileName,5,6);
        $clientcode = ltrim($clientcode, '0');
        echo $clientcode;

      }else{
       $clientcode = substr($fileName,4,6);
       $clientcode = ltrim($clientcode, '0');
       echo $clientcode;
      }

      $StoreIDPre = substr($fileName,4,4);
      if(str_starts_with($StoreIDPre,'_')){
        $StoreIDPre  = substr($fileName,5,4);
      }else{
        $StoreIDPre = substr($fileName,4,4);
      }

      $parts = explode("_", $fileName);

      $processName= $parts[0];
      $StoreIdText = isset($parts[1]) ? ltrim($parts[1], "0") : null;
     $numberOfTransmission = isset($parts[2]) 
    ? ltrim(pathinfo($parts[2], PATHINFO_FILENAME), "0") 
    : null;



        if($StoreIdText == '029991'){

        match ($processName) {

             'CDGD' => $this->customsDescService->extractData($filePath,$numberOfTransmission),
             'GDTY' => $this->goodsTypeService->extractData($filePath,$numberOfTransmission),
             'CMPS' => $this->cmpsService->extractData($filePath,$numberOfTransmission),
            default => null,
        };}

         $Stores = [
         'M06' => '5009',
         'M07' => '3957'
        ];
        

        foreach($Stores as $NebimStoreID=>$StoreID){
            if($StoreID == $StoreIdText){
                match ($processName) {
             'ITEM' => $this->itemService->extractData($filePath,$StoreID,$NebimStoreID,$numberOfTransmission),
             'EAN' => $this->eanService->extractData($filePath,$StoreID,$NebimStoreID,$numberOfTransmission),
             'PRC' => $this->priceChangeService->extractData($filePath,$StoreID,$NebimStoreID,$numberOfTransmission),
             default => null,

            };

        }

    }
         if($prefixDDT == 'DDT'){
            $this->packingListService->extractData($filePath);
        }

        




}
}
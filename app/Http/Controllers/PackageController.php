<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\ArrivalConfReportService;



class PackageController extends Controller
{

    public function __construct(private ArrivalConfReportService $arrivalConfService) {
    }

    public function index()
    {

         $package = DB::table('zt_packing_header')->paginate(10);

        // view’e gönder
        return view('products.index', compact('package'));
    }

    public function packing_package($bill_of_transport){
        
        $packageDetail = DB::table('zt_packing_package')
        ->where('bill_of_transport',$bill_of_transport)
        ->paginate(10);

        return view('products.detail', compact('packageDetail'));
    }   

    public function packing_detail($package_grouping_number){
        $packagepDetail = DB::table('zt_packing_detail')
        ->where('package_grouping_number',$package_grouping_number)
        ->paginate(10);

        return view('products.pdetail', compact('packagepDetail'));

    }

    public function ArrivalConf($billOfTransport){
        $result = DB::table('zt_packing_header as a')
        ->join('zt_packing_detail as b','a.bill_of_transport','=','b.bill_of_transport')
        ->where('a.bill_of_transport',$billOfTransport)
        ->select(
            'a.store_id_receiver',
            'a.bill_of_transport',
            'a.bill_of_transport_date' ,
            'b.invoice_number',
            'b.invoice_date'
        )
        ->first();
        
        $this->$arrivalConfService->generateReport($result);




    }




}

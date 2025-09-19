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
          $packagepDetail = DB::table('zt_packing_detail as pd')
        ->leftJoin('zt_eanlist_detail as ed', 'ed.ean_upc_number', '=', 'pd.ean_number')
        ->leftJoin('zt_items_detail as id', 'id.item_number', '=', 'ed.item_number')
        ->where('pd.package_grouping_number', $package_grouping_number)
        ->select(
            'pd.store_id_receiver',                    
            'pd.bill_of_transport',       
            'pd.ean_number',
            'ed.item_number',
            'id.item_description',
            'id.colour',
            'id.retail_price_1'

        )
        ->paginate(10);
        return view('products.pdetail', compact('packagepDetail'));

    }


    public function comingAllProduct(Request $request){
        $query = DB::table('zt_packing_detail as pd')
          ->leftJoin('zt_eanlist_detail as ed', 'ed.ean_upc_number', '=', 'pd.ean_number')
        ->leftJoin('zt_items_detail as id', 'id.item_number', '=', 'ed.item_number')
        ->leftJoin('zt_packing_package as pc','pc.package_grouping_number','=','pd.package_grouping_number')
        ->select(
            'pd.store_id_receiver',                    
            'pd.bill_of_transport',   
            'pd.bill_of_transport_date',  
            'pc.box_ean_number',  
            'pd.ean_number',
            'pd.quantity',
            'ed.item_number',
            'id.item_description',
            'id.colour',
            'id.retail_price_1'
        );

            if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('pd.store_id_receiver', 'like', "%$search%")
              ->orWhere('pd.bill_of_transport', 'like', "%$search%")
              ->orWhere('pd.ean_number', 'like', "%$search%")
              ->orWhere('ed.item_number', 'like', "%$search%")
              ->orWhere('id.item_description', 'like', "%$search%");
        });
    }

    $allProduct = $query->paginate(10)->withQueryString(); // withQueryString, sayfalama ile aramayı korur
    return view('products.allproduct', compact('allProduct'));
        
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

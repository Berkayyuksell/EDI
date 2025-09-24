<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\ArrivalConfReportService;



class PackageController extends Controller
{

    public function __construct(private ArrivalConfReportService $arrivalConfService) {
    }

    public function index()
    {

         $package = DB::table('zt_packing_header')
         ->orderByDesc('bill_of_transport_date')
         ->paginate(20);

        // view’e gönder
        return view('products.index', compact('package'));
    }

    public function packing_package($bill_of_transport){
        
        $packageDetail = DB::table('zt_packing_package')
        ->where('bill_of_transport',$bill_of_transport)
        ->paginate(20);

        return view('products.detail', compact('packageDetail'));
    }   

    public function packing_detail($package_grouping_number){
          $query = DB::table('zt_packing_detail as pd')
        ->leftJoin('zt_eanlist_detail as ed', 'ed.ean_upc_number', '=', 'pd.ean_number')
        ->leftJoin('zt_items_detail as id', 'id.item_number', '=', 'ed.item_number')
        ->leftJoin('zt_packing_package as pc','pc.package_grouping_number','=','pd.package_grouping_number')
        ->where('pd.package_grouping_number', $package_grouping_number)
        ->select(
            'pd.store_id_receiver',                    
            'pd.bill_of_transport',      
            'pd.bill_of_transport_date',
            'pd.ean_number',
            'pc.box_ean_number',
            'ed.item_number',
            'pd.quantity',
            'id.item_description',
            'id.colour',
            'id.retail_price_1'

        );
        

         if ($search = request('search')) {
        $query->where(function($q) use ($search) {
            $q->where('pd.bill_of_transport', 'like', "%{$search}%")
              ->orWhere('pd.ean_number', 'like', "%{$search}%");
        });
    }

        $packagepDetail = $query->paginate(20)->withQueryString();

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






    $allProduct = $query->paginate(20)->withQueryString(); // withQueryString, sayfalama ile aramayı korur
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


    public function approve($id){
        DB::table('zt_packing_header')
        ->where('bill_of_transport',$id)
        ->update(['isApprove' => 1]);


        return redirect()->back()->with('success', 'Paket onaylandı.');


    }

      public function ReportItem(Request $request)
    {
        $request->validate([
            'store_id_receiver' => 'required',
            'bill_of_transport' => 'required',
            'ean_number' => 'required',
            'item_number' => 'required',
            'box_ean_number' => 'required',
            'item_description' => 'required',
            'colour' => 'required',
            'retail_price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'bill_of_transport_date' => 'required|date',
            'transaction_code' => 'required',
            'cause_code' => 'nullable',
            'comment' => 'nullable',
        ]);

        // MS SQL tablosuna insert
        DB::table('package_reports')->insert([
            'store_id_receiver' => $request->store_id_receiver,
            'bill_of_transport' => $request->bill_of_transport,
            'ean_number' => $request->ean_number,
            'item_number' => $request->item_number,
            'box_ean_number' => $request->box_ean_number,
            'item_description' => $request->item_description,
            'colour' => $request->colour,
            'retail_price' => $request->retail_price,
            'quantity' => $request->quantity,
            'bill_of_transport_date' => $request->bill_of_transport_date,
            'transaction_code' => $request->transaction_code,
            'cause_code' => $request->cause_code,
            'comment' => $request->comment,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Ürün bildirimi başarıyla kaydedildi.');

        
    }


     public function GoodsManList(){
        
        $reports = DB::table('package_reports')
        ->paginate(20);

        return view('products.goodsman', compact('reports'));
        
    }   


    public function deleteReportItem($id){
        $deleted = DB::table('package_reports')->where('id', $id)->delete();

         if ($deleted) {
        return redirect()->back()->with('success', 'Rapor başarıyla silindi!');
    } else {
        return redirect()->back()->with('error', 'Rapor bulunamadı veya silinemedi!');
    }

    }





















}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index(Request $request)
{
    $search = $request->input('search'); // formdan gelen arama değeri

    $items = DB::table('zt_items_detail as item')
        ->leftJoin('zt_eanlist_detail as ean','ean.item_number','=','item.item_number')
        ->select(
            'item.item_number',
            'item.item_description',
            'item.colour',
            'ean.ean_upc_number',
            'ean.size_description',
            'ean.sku_number',
            'item.composition',
            'item.function_description',
            'item.season_description',
            'item.item_gender_description',
            'item.retail_price_1',
            'item.category_description',
        )
        ->when($search, function($query, $search) {
            return $query->where(function($q) use ($search) {
                $q->where('item.item_number', 'like', "%{$search}%")
                  ->orWhere('item.item_description', 'like', "%{$search}%")
                  ->orWhere('ean.ean_upc_number', 'like', "%{$search}%");
            });
        })
        ->paginate(20)
        ->appends(['search' => $search]); // sayfalar arası arama kaybolmasın

    return view('item.index', compact('items', 'search'));
}



    public function priceChangeList(){
        $priceChange = DB::table('zt_pricechange_detail')->paginate(20);


        return view('item.pricechange',compact('priceChange'));
    }

}

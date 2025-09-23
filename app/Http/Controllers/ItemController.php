<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index(){
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
        )->paginate(20);

        return view('item.index',compact('items'));







    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index(){
        $items = DB::table('zt_items_detail')
        ->select(
            'item_number',
            'item_description',
            'colour',
        )->paginate(10);


        return view('item.index',compact('items'));


    }
}

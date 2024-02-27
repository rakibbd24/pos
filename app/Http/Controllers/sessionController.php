<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class sessionController extends Controller
{
    function addToCart(Request $request)
    {
        if(session()->has('sale_cart'))
        {
            $sale_cart = [];
            $sale_cart = session()->get('sale_cart');

            $sale_cart[$request->item_id] = [
                'item_id'       => $request->item_id,
                'item_name'     => $request->item_name,
                'item_image'    => $request->item_image,
                'item_price'    => $request->item_price,
            ];
            Session::put('sale_cart', $sale_cart);
        }else{
            $sale_cart = [];

            $sale_cart[$request->item_id] = [
                'item_id'       => $request->item_id,
                'item_name'     => $request->item_name,
                'item_image'    => $request->item_image,
                'item_price'    => $request->item_price,
            ];
            Session::put('sale_cart', $sale_cart);
        }
    }

    public function removeFromCart(Request $request)
    {
        if(session()->has('sale_cart'))
        {
            $sale_cart = session()->get('sale_cart');

            unset($sale_cart[$request->item_id]);

            Session::put('sale_cart', $sale_cart);

            if(empty(session()->get('sale_cart')))
            {
                session()->forget('sale_cart');
            }
            return response()->json(['status' => 'ok']);
        }
    }

    public function updatePriceCart(Request $request)
    {
        if(session()->has('sale_cart'))
        {
            $sale_cart = session()->get('sale_cart');

            $sale_cart[$request->item_id]['item_price'] = $request->item_price;

            Session::put('sale_cart', $sale_cart);
            return response()->json(['status' => 'ok']);
        }
    }
}

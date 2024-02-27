<?php

namespace App\Http\Controllers;

use App\Mail\OnSaleMail;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Account;
use App\Models\PaymentMethod;
use App\Mail\SaleMail;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\PaymentSale;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Unit;
use App\Models\ProductVariant;
use App\Models\product_warehouse;
use App\Models\Warehouse;
use App\Models\User;
use App\Models\UserWarehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Client;
use App\Models\Setting;
use App\Models\PosSetting;
use App\Models\Currency;
use App\Models\Deposit;
use Carbon\Carbon;
use DataTables;
use Stripe;
use Config;
use DB;
use PDF;
use App\utils\helpers;
use Doctrine\DBAL\Driver\IBMDB2\DB2Driver;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Storage;

class PosController extends Controller
{

    protected $currency;
    protected $symbol_placement;

    public function __construct()
    {
        $helpers = new helpers();
        $this->currency = $helpers->Get_Currency();
        $this->symbol_placement = $helpers->get_symbol_placement();

    }


     //--------------------- index  ------------------------\\

     public function index(Request $request)
     {

        $user_auth = auth()->user();

       if ($user_auth->can('pos')){

            $helpers = new helpers();
            $currency = $helpers->Get_Currency();
            $symbol_placement = $helpers->get_symbol_placement();

            $products = product_warehouse::with('warehouse', 'product', 'productVariant')
                                                        ->where('warehouse_id', '1')
                                                        ->where('deleted_at', '=', null)
                                                        ->where(function ($query) use ($request) {
                                                            return $query->where('qte', '>', 0)->orWhere('manage_stock', false);
                                                        })
                                                        ->orderBy('product_id', 'DESC')
                                                        ->paginate(9);
            // $products = Product::latest()->paginate(9);
            $clients = Client::where('deleted_at', '=', null)->get(['id', 'username']);
            $payment_methods = PaymentMethod::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','title']);
            $accounts = Account::where('deleted_at', '=', null)->orderBy('id', 'desc')->get(['id','account_name']);
            return view('sales.pos-new', compact('products', 'clients', 'currency', 'symbol_placement', 'payment_methods', 'accounts'));


        }else{
            return abort('403', __('You are not authorized'));
        }
     }

    //------------ Create New  POS --------------\\

    public function CreatePOS(Request $request)
    {
            try{
                $helpers = new helpers();
                $order = new Sale;

                $order->is_pos = 1;
                $order->date = date('Y-m-d G:i:s', strtotime($request->date));
                $order->Ref = 'SO-' . date("Ymd") . '-'. date("his");
                $order->client_id = $request->client_id;
                $order->warehouse_id = '1';
                $order->tax_rate = '0';
                $order->TaxNet = '0';
                $order->discount = '0';
                $order->discount_type = 'fixed';
                $order->discount_percent_total = '0';
                $order->shipping = '0';
                $order->GrandTotal = $request->GrandTotal;
                $order->notes = NULL;
                $order->statut = 'completed';
                $order->payment_statut = 'unpaid';
                $order->user_id = Auth::user()->id;

                $order->save();
                $sale_id = $order->id;

                foreach ($request->item_id as $key => $value) {

                    $sale_details = new SaleDetail();

                    $sale_details->date               = date('Y-m-d G:i:s', strtotime($request->date));
                    $sale_details->sale_id            = $order->id;
                    $sale_details->sale_unit_id       = '1';
                    $sale_details->quantity           = '1';
                    $sale_details->product_id         = $value;
                    $sale_details->product_variant_id = NULL;
                    $sale_details->total              = $request->unit_price[$key];
                    $sale_details->price              = $request->unit_price[$key];
                    $sale_details->TaxNet             = '0';
                    $sale_details->tax_method         = '1';
                    $sale_details->discount           = '0';
                    $sale_details->discount_method    = '2';
                    $sale_details->imei_number        = NULL;
                    $sale_details->save();

                    $product_warehouse = product_warehouse::where('warehouse_id', 1)
                                                        ->where('product_id', $value)
                                                        ->first();
                    $product_warehouse->qte = $product_warehouse->qte-1;
                    $product_warehouse->save();

                    //CHANGE SOLD STATUS OF EACH ITEM
                    $get_product = Product::where('id', $value)->first();
                    $get_product->sold = '1';
                    $get_product->save();
                }

                if($request->montant > 0){

                    $sale = Sale::findOrFail($order->id);

                    $total_paid = $sale->paid_amount + $request->montant;
                    $due = $sale->GrandTotal - $total_paid;

                    if ($due === 0.0 || $due < 0.0) {
                        $payment_statut = 'paid';
                    } else if ($due != $sale->GrandTotal) {
                        $payment_statut = 'partial';
                    } else if ($due == $sale->GrandTotal) {
                        $payment_statut = 'unpaid';
                    }


                    $product = Product::findOrFail($value);
                    $product_name = $product->name;
                    
                    PaymentSale::create([
                        'sale_id'    => $order->id,
                        'account_id' => $request->account_id?$request->account_id:NULL,
                        'Ref'        => $this->generate_random_code_payment(),
                        'date'       => date('Y-m-d G:i:s', strtotime($request->date)),
                        'payment_method_id'  => $request->payment_method_id,
                        'montant'    => $request->montant,
                        'change'     => 0,
                        'notes'      => NULL,
                        'user_id'    => Auth::user()->id,
                    ]);

                    $account = Account::where('id', $request->account_id)->exists();

                    if ($account) {
                        // Account exists, perform the update
                        $account = Account::find($request['account_id']);
                        $account->update([
                            'initial_balance' => $account->initial_balance + $request->montant,
                        ]);
                    }

                    $sale->update([
                        'paid_amount' => $total_paid,
                        'payment_statut' => $payment_statut,
                    ]);
                    $product = Product::findOrFail($value);
                    $product_name = $product->name;

                    //DEPOSIT
                    Deposit::create([
                        'deposit_ref'            => $product_name,
                        'account_id'             => 3,
                        'deposit_category_id'    => 1,
                        'amount'                 => $total_paid,
                        'payment_method_id'      => 6,
                        'date'                   => date('Y-m-d',strtotime(now())),
                        'description'            => null
                    ]);

                }

                //SEND EMAIL

                $settings = Setting::where('deleted_at', '=', null)->first();
                $sale = Sale::with('client','user')->where('id', $sale_id)->first();
                $sale_details = SaleDetail::with('product')->where('sale_id', $sale->id)->get();

                if(count($sale_details) > 0)
                {
                    foreach($sale_details as $data)
                    {

                        $Html = view('pdf.on_sale_pdf', [
                            'sale' => $sale,
                            'setting' => $settings,
                            'sale_item' => $data
                        ])->render();
                        $pdf = PDF::loadHTML($Html);
                        $content = $pdf->download()->getOriginalContent();
                        Storage::put('public/sale/sale_'.$sale->id.'_item_'.$data->id.'.pdf',$content) ;

                        //SEND MAIL
                        $this->Set_config_mail();

                        $email['subject'] = 'Thank you for your purchase!';
                        $email['company_name'] = $settings->CompanyName;
                        //$email['file'] = url('/public/storage/sale/sale_'.$sale->id.'_item_'.$data->id.'.pdf');
                        $email['file'] = public_path('storage/sale/sale_'.$sale->id.'_item_'.$data->id.'.pdf');
                        $email['client'] = $sale->client->username;
                        $email['invoice_id'] = $sale->Ref;
                        $mail = Mail::to($sale->client->email)->send(new OnSaleMail($email));
                    }
                }
                session()->forget('sale_cart');
                $app_url = url('');
                $url = $app_url.'/sale/sales/'.$sale->id;
                return redirect()->to($url);
            }catch(\Exception $ex){
                dd($ex);
            }
    }

    // Set config mail
    public function Set_config_mail()
    {
        $config = array(
            'driver' => env('MAIL_MAILER'),
            'host' => env('MAIL_HOST'),
            'port' => env('MAIL_PORT'),
            'from' => array('address' => env('MAIL_FROM_ADDRESS'), 'name' =>  env('MAIL_FROM_NAME')),
            'encryption' => env('MAIL_ENCRYPTION'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'sendmail' => '/usr/sbin/sendmail -bs',
            'pretend' => false,
            'stream' => [
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ],
        );
        Config::set('mail', $config);

    }
      // generate_random_code_payment
    public function generate_random_code_payment()
    {
        $gen_code = 'INV/SL-' . date("Ymd") . '-'. substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);

        if (PaymentSale::where('Ref', $gen_code)->exists()) {
            $this->generate_random_code_payment();
        } else {
            return $gen_code;
        }

    }

    //------------ Get Products--------------\\

    public function GetProductsByParametre(request $request)
    {
         // How many items do you want to display.
         $perPage = 8;
         $pageStart = \Request::get('page', 1);
         // Start displaying items from this number;
         $offSet = ($pageStart * $perPage) - $perPage;
         $data = array();

        $product_warehouse_data = product_warehouse::where('warehouse_id', $request->warehouse_id)
            ->with('product', 'product.unitSale')
            ->where('deleted_at', '=', null)
            ->where(function ($query) use ($request) {
                if ($request->stock == '1' && $request->product_service == '1') {
                    return $query->where('qte', '>', 0)->orWhere('manage_stock', false);

                }elseif($request->stock == '1' && $request->product_service == '0') {
                    return $query->where('qte', '>', 0)->orWhere('manage_stock', true);

                }else{
                    return $query->where('manage_stock', true);
                }
            })

        // Filter
        ->where(function ($query) use ($request) {
            return $query->when($request->filled('category_id'), function ($query) use ($request) {
                return $query->whereHas('product', function ($q) use ($request) {
                    $q->where('category_id', '=', $request->category_id);
                });
            });
        })
        ->where(function ($query) use ($request) {
            return $query->when($request->filled('brand_id'), function ($query) use ($request) {
                return $query->whereHas('product', function ($q) use ($request) {
                    $q->where('brand_id', '=', $request->brand_id);
                });
            });
        });

        $totalRows = $product_warehouse_data->count();

        $product_warehouse_data = $product_warehouse_data
            ->orderBy('id','desc')
            ->offset($offSet)
            ->limit(8)
            ->get();

        foreach ($product_warehouse_data as $product_warehouse) {
            if ($product_warehouse->product_variant_id) {
                $productsVariants = ProductVariant::where('product_id', $product_warehouse->product_id)
                    ->where('id', $product_warehouse->product_variant_id)
                    ->where('deleted_at', null)
                    ->first();

                $item['product_variant_id'] = $product_warehouse->product_variant_id;
                $item['Variant'] = $productsVariants->name . '-' . $product_warehouse['product']->name;

                $item['code'] = $productsVariants->code;
                $item['name'] = '['.$productsVariants->name . '] ' . $product_warehouse['product']->name;

                $item['barcode'] = '['.$productsVariants->name . '] ' . $product_warehouse['product']->name;

                $product_price = $productsVariants->price;

            } else if ($product_warehouse->product_variant_id === null) {
                $item['product_variant_id'] = null;
                $item['Variant'] = null;
                $item['code'] = $product_warehouse['product']->code;
                $item['name'] = $product_warehouse['product']->name;
                $item['barcode'] = $product_warehouse['product']->code;

                $product_price =  $product_warehouse['product']->price;
            }

            $item['product_type'] = $product_warehouse['product']->type;
            $item['id']           = $product_warehouse->product_id;
            $item['qty_min']      = $product_warehouse['product']->type != 'is_service'?$product_warehouse['product']->qty_min:'---';
            $item['image']        = $product_warehouse['product']->image;

            //check if product has promotion
            $todaydate = date('Y-m-d');

            if($product_warehouse['product']->is_promo
                && $todaydate >= $product_warehouse['product']->promo_start_date
                && $todaydate <= $product_warehouse['product']->promo_end_date){
                    $price_init = $product_warehouse['product']->promo_price;
                    $item['is_promotion'] = 1;
                    $item['promo_percent'] =  round(100 * ($product_price - $price_init) / $product_price);
            }else{
                $price_init = $product_price;
                $item['is_promotion'] = 0;
            }

            if ($product_warehouse['product']['unitSale'] && $product_warehouse['product']['unitSale']->operator == '/') {
                $item['qte_sale'] = $product_warehouse->qte * $product_warehouse['product']['unitSale']->operator_value;
                $price = $price_init / $product_warehouse['product']['unitSale']->operator_value;

            }elseif ($product_warehouse['product']['unitSale'] && $product_warehouse['product']['unitSale']->operator == '*') {
                $item['qte_sale'] = $product_warehouse->qte / $product_warehouse['product']['unitSale']->operator_value;
                $price = $price_init * $product_warehouse['product']['unitSale']->operator_value;

            }else{
                $item['qte_sale'] = $product_warehouse->qte;
                $price = $price_init;
            }

            $item['unitSale'] = $product_warehouse['product']['unitSale']?$product_warehouse['product']['unitSale']->ShortName:'';
            $item['qte'] = $product_warehouse->qte;

            if ($product_warehouse['product']->TaxNet !== 0.0) {

                //Exclusive
                if ($product_warehouse['product']->tax_method == '1') {
                    $tax_price = $price * $product_warehouse['product']->TaxNet / 100;

                    $item['Net_price'] = $this->render_price_with_symbol_placement(number_format($price + $tax_price, 2, '.', ','));

                    // Inxclusive
                } else {
                    $item['Net_price'] = $this->render_price_with_symbol_placement(number_format($price, 2, '.', ','));
                }
            } else {
                $item['Net_price'] = $this->render_price_with_symbol_placement(number_format($price, 2, '.', ','));
            }

            $data[] = $item;
        }

        return response()->json([
            'products' => $data,
            'totalRows' => $totalRows,
        ]);
    }


    //------------ autocomplete_product_pos -----------------\\

    public function autocomplete_product_pos(request $request, $id)
    {
        $data = [];
        $product_warehouse_data = product_warehouse::with('warehouse', 'product', 'productVariant')
            ->where('warehouse_id', $id)
            ->where('deleted_at', '=', null)
            ->where(function ($query) use ($request) {
                if ($request->stock == '1' && $request->product_service == '1') {
                    return $query->where('qte', '>', 0)->orWhere('manage_stock', false);

                }elseif($request->stock == '1' && $request->product_service == '0') {
                    return $query->where('qte', '>', 0)->orWhere('manage_stock', true);

                }else{
                    return $query->where('manage_stock', true);
                }
            })

        // Filter
        ->where(function ($query) use ($request) {
            return $query->when($request->filled('category_id'), function ($query) use ($request) {
                return $query->whereHas('product', function ($q) use ($request) {
                    $q->where('category_id', '=', $request->category_id);
                });
            });
        })
        ->where(function ($query) use ($request) {
            return $query->when($request->filled('brand_id'), function ($query) use ($request) {
                return $query->whereHas('product', function ($q) use ($request) {
                    $q->where('brand_id', '=', $request->brand_id);
                });
            });
        })->get();

        foreach ($product_warehouse_data as $product_warehouse) {

            if ($product_warehouse->product_variant_id) {
                $item['product_variant_id'] = $product_warehouse->product_variant_id;

                $item['code'] = $product_warehouse['productVariant']->code;
                $item['name'] = '['.$product_warehouse['productVariant']->name . '] ' . $product_warehouse['product']->name;

                $item['Variant'] = '['.$product_warehouse['productVariant']->name . '] ' . $product_warehouse['product']->name;
                $item['barcode'] = '['.$product_warehouse['productVariant']->name . '] ' . $product_warehouse['product']->name;

            } else {
                $item['product_variant_id'] = null;
                $item['Variant'] = null;
                $item['code'] = $product_warehouse['product']->code;
                $item['name'] = $product_warehouse['product']->name;
                $item['barcode'] = $product_warehouse['product']->code;
            }

            $item['id'] = $product_warehouse->product_id;

            $item['qty_min'] = $product_warehouse['product']->type != 'is_service'?$product_warehouse['product']->qty_min:'---';
            $item['Type_barcode'] = $product_warehouse['product']->Type_barcode;
            $item['product_type'] = $product_warehouse['product']->type;

            if ($product_warehouse['product']['unitSale'] && $product_warehouse['product']['unitSale']->operator == '/') {
                $item['qte_sale'] = $product_warehouse->qte * $product_warehouse['product']['unitSale']->operator_value;

            } elseif ($product_warehouse['product']['unitSale'] && $product_warehouse['product']['unitSale']->operator == '*') {
                $item['qte_sale'] = $product_warehouse->qte / $product_warehouse['product']['unitSale']->operator_value;

            }else{
                $item['qte_sale'] = $product_warehouse->qte;
            }

            $item['qte'] = $product_warehouse->qte;
            $item['unitSale'] = $product_warehouse['product']['unitSale']?$product_warehouse['product']['unitSale']->ShortName:'';

            $data[] = $item;
        }

        return response()->json($data);
    }

     //------------- Reference Number Order SALE -----------\\

     public function getNumberOrder()
     {

         $last = DB::table('sales')->latest('id')->first();

         if ($last) {
             $item = $last->Ref;
             $nwMsg = explode("_", $item);
             $inMsg = $nwMsg[1] + 1;
             $code = $nwMsg[0] . '_' . $inMsg;
         } else {
             $code = 'V_1';
         }
         return $code;
     }

     //-------------- Print Invoice ---------------\\

     public function Print_Invoice_POS(Request $request, $id)
     {
        $user_auth = auth()->user();

        if ($user_auth->can('pos')){

            $details = array();

            $sale = Sale::with('details.product.unitSale')
                ->where('deleted_at', '=', null)
                ->findOrFail($id);

            $item['id']                     = $sale->id;
            $item['Ref']                    = $sale->Ref;
            $item['date']                   = Carbon::parse($sale->date)->format('d-m-Y H:i');

            if($sale->discount_type == 'fixed'){
                $item['discount']           = $this->render_price_with_symbol_placement(number_format($sale->discount, 2, '.', ','));
            }else{
                $item['discount']           = $this->render_price_with_symbol_placement(number_format($sale->discount_percent_total, 2, '.', ',')) .'('.$sale->discount .' '.'%)';
            }

            $item['shipping']               = $this->render_price_with_symbol_placement(number_format($sale->shipping, 2, '.', ','));
            $item['taxe']                   = $this->render_price_with_symbol_placement(number_format($sale->TaxNet, 2, '.', ','));
            $item['tax_rate']               = $sale->tax_rate;
            $item['client_name']            = $sale['client']->username;
            $item['warehouse_name']         = $sale['warehouse']->name;
            $item['GrandTotal']             = $this->render_price_with_symbol_placement(number_format($sale->GrandTotal, 2, '.', ','));
            $item['paid_amount']            = $this->render_price_with_symbol_placement(number_format($sale->paid_amount, 2, '.', ','));
            $item['due']                    = $this->render_price_with_symbol_placement(number_format($sale->GrandTotal - $sale->paid_amount, 2, '.', ','));
            foreach ($sale['details'] as $detail) {

                $unit = Unit::where('id', $detail->sale_unit_id)->first();
                if ($detail->product_variant_id) {

                    $productsVariants = ProductVariant::where('product_id', $detail->product_id)
                        ->where('id', $detail->product_variant_id)->first();

                        $data['code'] = $productsVariants->code;
                        $data['name'] = '['.$productsVariants->name . '] ' . $detail['product']['name'];

                    } else {
                        $data['code'] = $detail['product']['code'];
                        $data['name'] = $detail['product']['name'];
                    }

                $data['price'] = $this->render_price_with_symbol_placement(number_format($detail->price, 2, '.', ','));
                $data['total'] = $this->render_price_with_symbol_placement(number_format($detail->total, 2, '.', ','));
                $data['quantity'] = $detail->quantity;
                $data['unit_sale'] = $unit?$unit->ShortName:'';

                $data['is_imei'] = $detail['product']['is_imei'];
                $data['imei_number'] = $detail->imei_number;

                $details[] = $data;
            }

            $payments = PaymentSale::with('sale','payment_method')
                ->where('sale_id', $id)
                ->orderBy('id', 'DESC')
                ->get();

            $payments_details = [];
            foreach ($payments as $payment) {

                $payment_data['Reglement'] = $payment->payment_method->title;
                $payment_data['montant']   = $this->render_price_with_symbol_placement(number_format($payment->montant, 2, '.', ','));

                $payments_details[] = $payment_data;
            }

            $settings = Setting::where('deleted_at', '=', null)->first();
            $pos_settings = PosSetting::where('deleted_at', '=', null)->first();

            return view('sales.invoice_pos',
                    [
                        'payments' => $payments_details,
                        'setting' => $settings,
                        'pos_settings' => $pos_settings,
                        'sale' => $item,
                        'details' => $details,
                    ]
                );

        }
        return abort('403', __('You are not authorized'));

     }



    // render_price_with_symbol_placement

    public function render_price_with_symbol_placement($amount) {

        if ($this->symbol_placement == 'before') {
            return $this->currency . ' ' . $amount;
        } else {
            return $amount . ' ' . $this->currency;
        }
    }



}

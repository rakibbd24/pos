<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Imports\ProductImport;
use App\Models\Account;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\product_warehouse;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\UserWarehouse;
use App\Models\Currency;
use App\Models\Expense;
use App\Models\productProfile;
use DataTables;
use Excel;
use DB;
use Carbon\Carbon;
use App\utils\helpers;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Log;
use JildertMiedema\LaravelPlupload\Facades\Plupload;
use Illuminate\Support\Facades\File;
use ZipArchive;

use function PHPUnit\Framework\fileExists;

class ProductsController extends Controller
{

    protected $currency;
    protected $symbol_placement;

    public function __construct()
    {
        $helpers = new helpers();
        $this->currency = $helpers->Get_Currency();
        $this->symbol_placement = $helpers->get_symbol_placement();

    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
     //------------ GET ALL Product -----------\\

     public function index(Request $request)
     {
        $user_auth = auth()->user();
		if ($user_auth->can('products_view')){

            $categories = Category::where('deleted_at', null)->get(['id', 'name']);
            $brands = Brand::where('deleted_at', null)->get(['id', 'name']);

             //get warehouses assigned to user
             if($user_auth->is_all_warehouses){
                $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
            }else{
                $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
                $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
            }

            return view('products.list_product', compact('categories','brands','warehouses'));

        }
        return abort('403', __('You are not authorized'));

     }

     public function get_product_datatable(Request $request)
     {
        $user_auth = auth()->user();
        if (!$user_auth->can('products_view')){
            return abort('403', __('You are not authorized'));
        }else{

            if($user_auth->is_all_warehouses){
                $array_warehouses_id = Warehouse::where('deleted_at', '=', null)->pluck('id')->toArray();
            }else{
                $array_warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
            }

                $helpers = new helpers();
                $symbol_placement = $helpers->get_symbol_placement();

                // Filter fields With Params to retrieve
                $columns = array(0 => 'name', 1 => 'category_id', 2 => 'brand_id');
                $param = array(0 => 'like', 1 => '=', 2 => '=');

                $columns_order = array(
                    0 => 'id',
                    3 => 'name',
                    4 => 'code',
                );
                $start = $request->input('start');
                $order = 'products.'.$columns_order[$request->input('order.0.column')];
                $dir = $request->input('order.0.dir');

                $product_data = Product::where('deleted_at', '=', null)


                // Multiple Filter
                ->where(function ($query) use ($request) {
                    return $query->when($request->filled('code'), function ($query) use ($request) {
                        return $query->where('products.code', 'LIKE', "%{$request->code}%")
                            ->orWhereHas('variants', function ($query) use ($request) {
                                $query->where('code', 'LIKE', "%{$request->code}%");
                            });
                    });
                });


                //Multiple Filter
                $products_Filtred = $helpers->filter($product_data, $columns, $param, $request)

                 // Search With Multiple Param
                ->where(function ($query) use ($request) {
                    return $query->when($request->filled('search.value'), function ($query) use ($request) {
                        return $query->where('products.name', 'LIKE', "%{$request->input('search.value')}%")
                            ->orWhere('products.code', 'LIKE', "%{$request->input('search.value')}%")
                            ->orWhere(function ($query) use ($request) {
                                return $query->whereHas('category', function ($q) use ($request) {
                                    $q->where('name', 'LIKE', "%{$request->input('search.value')}%");
                                });
                            })
                            ->orWhere(function ($query) use ($request) {
                                return $query->whereHas('brand', function ($q) use ($request) {
                                    $q->where('name', 'LIKE', "%{$request->input('search.value')}%");
                                });
                            });
                    });
                });

                $totalRows = $products_Filtred->count();
                $totalFiltered = $totalRows;

                if($request->input('length') != -1)
                $limit = $request->input('length');
                else
                $limit = $totalRows;

                $products = $products_Filtred
                ->with('unit', 'category', 'brand')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

                $data = array();

                foreach ($products as $product) {
                    $item['id'] = $product->id;
                    $item['category'] = $product['category']->name;
                    $item['brand'] = $product['brand'] ? $product['brand']->name : 'N/D';
                    $item['account_holder'] = $product->account_holder;
                    $item['email'] = $product->email ?? 'N/A';
                    $item['existing_attatchment_id'] = $product->existing_attatchment_id;

                    if($product->type == 'is_single'){

                      $item['type']  = 'Single';
                      $item['name'] = $product->name;
                      $item['code'] = $product->code;
                      $item['cost']  = $this->render_price_with_symbol_placement(number_format($product->cost, 2, '.', ','), $symbol_placement);
                      $item['price'] = $this->render_price_with_symbol_placement(number_format($product->price, 2, '.', ','), $symbol_placement);

                        $product_warehouse_total_qty = product_warehouse::where('product_id', $product->id)
                        ->where(function ($query) use ($array_warehouses_id) {
                            return $query->whereIn('warehouse_id', $array_warehouses_id);
                        })
                        ->where('deleted_at', '=', null)
                        ->sum('qte');

                        $item['quantity'] = $product_warehouse_total_qty .' '.$product['unit']->ShortName;

                    }elseif($product->type == 'is_variant'){

                        $item['type'] = 'Variable';
                        $product_variant_data = ProductVariant::where('product_id', $product->id)
                        ->where('deleted_at', '=', null)
                        ->get();

                        $item['cost'] = '';
                        $item['price'] = '';
                        $item['name'] = '';
                        $item['code'] = '';
                        $item['quantity'] = '';

                        foreach ($product_variant_data as $product_variant) {
                            $item['cost']  .= $this->render_price_with_symbol_placement(number_format($product_variant->cost, 2, '.', ','), $symbol_placement);
                            $item['cost']  .= '<br>';

                            $item['price'] .= $this->render_price_with_symbol_placement(number_format($product_variant->price, 2, '.', ','), $symbol_placement);
                            $item['price'] .= '<br>';

                            $item['name'] .= '['.$product_variant->name . ']' . $product->name;
                            $item['name'] .= '<br>';

                            $item['code'] .= $product_variant->code;
                            $item['code'] .= '<br>';

                            $product_warehouse_total_qty = product_warehouse::where('product_id', $product->id)
                            ->where('product_variant_id', $product_variant->id)
                            ->where(function ($query) use ($array_warehouses_id) {
                                return $query->whereIn('warehouse_id', $array_warehouses_id);
                            })
                            ->where('deleted_at', '=', null)
                            ->sum('qte');

                            $item['quantity'] .= $product_warehouse_total_qty .' '.$product['unit']->ShortName;
                            $item['quantity'] .= '<br>';
                        }



                    }else{
                        $item['type'] = 'Service';
                        $item['name'] = $product->name;
                        $item['code'] = $product->code;
                        $item['cost'] = '----';
                        $item['quantity'] = '----';
                        $item['price'] = $this->render_price_with_symbol_placement(number_format($product->price, 2, '.', ','), $symbol_placement);
                    }


                    if($product->image != 'no_image.png')
                    {
                        $url = $product->image;
                    }else{
                        $url = url("images/products/".$product->image);
                    }
                     //$url = url("images/products/".$product->image);
                     $item['image'] =
                        '<div class="avatar mr-2 avatar-md">
                            <img
                                src="'.$url.'" alt="">
                        </div>';

                    $item['action'] = '<button type="button" class="btn bg-transparent _r_btn border-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <span class="_dot _r_block-dot bg-dark"></span>
                        <span class="_dot _r_block-dot bg-dark"></span>
                        <span class="_dot _r_block-dot bg-dark"></span>
                    </button>';

                    $item['action'] .= '<div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(686px, 50px, 0px);">';

                    if ($user_auth->can('products_edit')){
                        $item['action'] .= '<a class="dropdown-item" href="/products/products/' .$product->id. '/edit" id="' .$product->id. '"><i class="nav-icon i-Edit text-success font-weight-bold m3-2"></i> ' .trans('translate.edit_product').'</a>';
                    }
                    if ($user_auth->can('products_delete')){
                        $item['action'] .= '  <a class="delete dropdown-item cursor-pointer" id="' .$product->id. '"><i class="nav-icon i-Close-Window text-danger font-weight-bold mr-3"></i> ' .trans('translate.delete_product').'</a>';
                    }
                    $item['action'] .= '</div>';


                    $data[] = $item;
                }

                $json_data = array(
                    "draw"            => intval($request->input('draw')),
                    "recordsTotal"    => intval($totalRows),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $data
                );

                echo json_encode($json_data);
            }

     }

     public function soldIndex(Request $request)
     {
        $user_auth = auth()->user();
		if ($user_auth->can('products_view')){

            $categories = Category::where('deleted_at', null)->get(['id', 'name']);
            $brands = Brand::where('deleted_at', null)->get(['id', 'name']);

             //get warehouses assigned to user
             if($user_auth->is_all_warehouses){
                $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
            }else{
                $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
                $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
            }

            return view('products.list_sold_product', compact('categories','brands','warehouses'));

        }
        return abort('403', __('You are not authorized'));

     }

     public function getSoldProductsDatatable(Request $request)
     {
        $user_auth = auth()->user();
        if (!$user_auth->can('products_view')){
            return abort('403', __('You are not authorized'));
        }else{

            if($user_auth->is_all_warehouses){
                $array_warehouses_id = Warehouse::where('deleted_at', '=', null)->pluck('id')->toArray();
            }else{
                $array_warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
            }

                $helpers = new helpers();
                $symbol_placement = $helpers->get_symbol_placement();

                // Filter fields With Params to retrieve
                $columns = array(0 => 'name', 1 => 'category_id', 2 => 'brand_id');
                $param = array(0 => 'like', 1 => '=', 2 => '=');

                $columns_order = array(
                    0 => 'id',
                    3 => 'name',
                    4 => 'code',
                );
                $start = $request->input('start');
                $order = 'products.'.$columns_order[$request->input('order.0.column')];
                $dir = $request->input('order.0.dir');

                $product_data = Product::where('deleted_at', '=', null)->where('sold', '1')


                // Multiple Filter
                ->where(function ($query) use ($request) {
                    return $query->when($request->filled('code'), function ($query) use ($request) {
                        return $query->where('products.code', 'LIKE', "%{$request->code}%")
                            ->orWhereHas('variants', function ($query) use ($request) {
                                $query->where('code', 'LIKE', "%{$request->code}%");
                            });
                    });
                });


                //Multiple Filter
                $products_Filtred = $helpers->filter($product_data, $columns, $param, $request)

                 // Search With Multiple Param
                ->where(function ($query) use ($request) {
                    return $query->when($request->filled('search.value'), function ($query) use ($request) {
                        return $query->where('products.name', 'LIKE', "%{$request->input('search.value')}%")
                            ->orWhere('products.code', 'LIKE', "%{$request->input('search.value')}%")
                            ->orWhere(function ($query) use ($request) {
                                return $query->whereHas('category', function ($q) use ($request) {
                                    $q->where('name', 'LIKE', "%{$request->input('search.value')}%");
                                });
                            })
                            ->orWhere(function ($query) use ($request) {
                                return $query->whereHas('brand', function ($q) use ($request) {
                                    $q->where('name', 'LIKE', "%{$request->input('search.value')}%");
                                });
                            });
                    });
                });

                $totalRows = $products_Filtred->count();
                $totalFiltered = $totalRows;

                if($request->input('length') != -1)
                $limit = $request->input('length');
                else
                $limit = $totalRows;

                $products = $products_Filtred
                ->with('unit', 'category', 'brand')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

                $data = array();

                foreach ($products as $product) {
                    $item['id'] = $product->id;
                    $item['category'] = $product['category']->name;
                    $item['brand'] = $product['brand'] ? $product['brand']->name : 'N/D';
                    $item['account_holder'] = $product->account_holder;
                    $item['email'] = $product->email ?? 'N/A';
                    $item['existing_attatchment_id'] = $product->existing_attatchment_id;

                    if($product->type == 'is_single'){

                      $item['type']  = 'Single';
                      $item['name'] = $product->name;
                      $item['code'] = $product->code;
                      $item['cost']  = $this->render_price_with_symbol_placement(number_format($product->cost, 2, '.', ','), $symbol_placement);
                      $item['price'] = $this->render_price_with_symbol_placement(number_format($product->price, 2, '.', ','), $symbol_placement);

                        $product_warehouse_total_qty = product_warehouse::where('product_id', $product->id)
                        ->where(function ($query) use ($array_warehouses_id) {
                            return $query->whereIn('warehouse_id', $array_warehouses_id);
                        })
                        ->where('deleted_at', '=', null)
                        ->sum('qte');

                        $item['quantity'] = $product_warehouse_total_qty .' '.$product['unit']->ShortName;

                    }elseif($product->type == 'is_variant'){

                        $item['type'] = 'Variable';
                        $product_variant_data = ProductVariant::where('product_id', $product->id)
                        ->where('deleted_at', '=', null)
                        ->get();

                        $item['cost'] = '';
                        $item['price'] = '';
                        $item['name'] = '';
                        $item['code'] = '';
                        $item['quantity'] = '';

                        foreach ($product_variant_data as $product_variant) {
                            $item['cost']  .= $this->render_price_with_symbol_placement(number_format($product_variant->cost, 2, '.', ','), $symbol_placement);
                            $item['cost']  .= '<br>';

                            $item['price'] .= $this->render_price_with_symbol_placement(number_format($product_variant->price, 2, '.', ','), $symbol_placement);
                            $item['price'] .= '<br>';

                            $item['name'] .= '['.$product_variant->name . ']' . $product->name;
                            $item['name'] .= '<br>';

                            $item['code'] .= $product_variant->code;
                            $item['code'] .= '<br>';

                            $product_warehouse_total_qty = product_warehouse::where('product_id', $product->id)
                            ->where('product_variant_id', $product_variant->id)
                            ->where(function ($query) use ($array_warehouses_id) {
                                return $query->whereIn('warehouse_id', $array_warehouses_id);
                            })
                            ->where('deleted_at', '=', null)
                            ->sum('qte');

                            $item['quantity'] .= $product_warehouse_total_qty .' '.$product['unit']->ShortName;
                            $item['quantity'] .= '<br>';
                        }



                    }else{
                        $item['type'] = 'Service';
                        $item['name'] = $product->name;
                        $item['code'] = $product->code;
                        $item['cost'] = '----';
                        $item['quantity'] = '----';
                        $item['price'] = $this->render_price_with_symbol_placement(number_format($product->price, 2, '.', ','), $symbol_placement);
                    }


                    if($product->image != 'no_image.png')
                    {
                        $url = $product->image;
                    }else{
                        $url = url("images/products/".$product->image);
                    }
                     //$url = url("images/products/".$product->image);
                     $item['image'] =
                        '<div class="avatar mr-2 avatar-md">
                            <img
                                src="'.$url.'" alt="">
                        </div>';

                    $item['action'] = '<button type="button" class="btn bg-transparent _r_btn border-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <span class="_dot _r_block-dot bg-dark"></span>
                        <span class="_dot _r_block-dot bg-dark"></span>
                        <span class="_dot _r_block-dot bg-dark"></span>
                    </button>';

                    $item['action'] .= '<div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(686px, 50px, 0px);">';

                    if ($user_auth->can('products_edit')){
                        $item['action'] .= '<a class="dropdown-item" href="/products/products/' .$product->id. '/edit" id="' .$product->id. '"><i class="nav-icon i-Edit text-success font-weight-bold m3-2"></i> ' .trans('translate.edit_product').'</a>';
                    }
                    if ($user_auth->can('products_delete')){
                        $item['action'] .= '  <a class="delete dropdown-item cursor-pointer" id="' .$product->id. '"><i class="nav-icon i-Close-Window text-danger font-weight-bold mr-3"></i> ' .trans('translate.delete_product').'</a>';
                    }
                    $item['action'] .= '</div>';


                    $data[] = $item;
                }

                $json_data = array(
                    "draw"            => intval($request->input('draw')),
                    "recordsTotal"    => intval($totalRows),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $data
                );

                echo json_encode($json_data);
            }

     }

     public function unSoldIndex(Request $request)
     {
        $user_auth = auth()->user();
		if ($user_auth->can('products_view')){

            $categories = Category::where('deleted_at', null)->get(['id', 'name']);
            $brands = Brand::where('deleted_at', null)->get(['id', 'name']);

             //get warehouses assigned to user
             if($user_auth->is_all_warehouses){
                $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
            }else{
                $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
                $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
            }

            return view('products.list_unsold_product', compact('categories','brands','warehouses'));

        }
        return abort('403', __('You are not authorized'));

     }

     public function getUnSoldProductsDatatable(Request $request)
     {
        $user_auth = auth()->user();
        if (!$user_auth->can('products_view')){
            return abort('403', __('You are not authorized'));
        }else{

            if($user_auth->is_all_warehouses){
                $array_warehouses_id = Warehouse::where('deleted_at', '=', null)->pluck('id')->toArray();
            }else{
                $array_warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
            }

                $helpers = new helpers();
                $symbol_placement = $helpers->get_symbol_placement();

                // Filter fields With Params to retrieve
                $columns = array(0 => 'name', 1 => 'category_id', 2 => 'brand_id');
                $param = array(0 => 'like', 1 => '=', 2 => '=');

                $columns_order = array(
                    0 => 'id',
                    3 => 'name',
                    4 => 'code',
                );
                $start = $request->input('start');
                $order = 'products.'.$columns_order[$request->input('order.0.column')];
                $dir = $request->input('order.0.dir');

                $product_data = Product::where('deleted_at', '=', null)->where('sold', '0')


                // Multiple Filter
                ->where(function ($query) use ($request) {
                    return $query->when($request->filled('code'), function ($query) use ($request) {
                        return $query->where('products.code', 'LIKE', "%{$request->code}%")
                            ->orWhereHas('variants', function ($query) use ($request) {
                                $query->where('code', 'LIKE', "%{$request->code}%");
                            });
                    });
                });


                //Multiple Filter
                $products_Filtred = $helpers->filter($product_data, $columns, $param, $request)

                 // Search With Multiple Param
                ->where(function ($query) use ($request) {
                    return $query->when($request->filled('search.value'), function ($query) use ($request) {
                        return $query->where('products.name', 'LIKE', "%{$request->input('search.value')}%")
                            ->orWhere('products.code', 'LIKE', "%{$request->input('search.value')}%")
                            ->orWhere(function ($query) use ($request) {
                                return $query->whereHas('category', function ($q) use ($request) {
                                    $q->where('name', 'LIKE', "%{$request->input('search.value')}%");
                                });
                            })
                            ->orWhere(function ($query) use ($request) {
                                return $query->whereHas('brand', function ($q) use ($request) {
                                    $q->where('name', 'LIKE', "%{$request->input('search.value')}%");
                                });
                            });
                    });
                });

                $totalRows = $products_Filtred->count();
                $totalFiltered = $totalRows;

                if($request->input('length') != -1)
                $limit = $request->input('length');
                else
                $limit = $totalRows;

                $products = $products_Filtred
                ->with('unit', 'category', 'brand')
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

                $data = array();

                foreach ($products as $product) {
                    $item['id'] = $product->id;
                    $item['category'] = $product['category']->name;
                    $item['brand'] = $product['brand'] ? $product['brand']->name : 'N/D';
                    $item['account_holder'] = $product->account_holder;
                    $item['email'] = $product->email ?? 'N/A';
                    $item['existing_attatchment_id'] = $product->existing_attatchment_id;

                    if($product->type == 'is_single'){

                      $item['type']  = 'Single';
                      $item['name'] = $product->name;
                      $item['code'] = $product->code;
                      $item['cost']  = $this->render_price_with_symbol_placement(number_format($product->cost, 2, '.', ','), $symbol_placement);
                      $item['price'] = $this->render_price_with_symbol_placement(number_format($product->price, 2, '.', ','), $symbol_placement);

                        $product_warehouse_total_qty = product_warehouse::where('product_id', $product->id)
                        ->where(function ($query) use ($array_warehouses_id) {
                            return $query->whereIn('warehouse_id', $array_warehouses_id);
                        })
                        ->where('deleted_at', '=', null)
                        ->sum('qte');

                        $item['quantity'] = $product_warehouse_total_qty .' '.$product['unit']->ShortName;

                    }elseif($product->type == 'is_variant'){

                        $item['type'] = 'Variable';
                        $product_variant_data = ProductVariant::where('product_id', $product->id)
                        ->where('deleted_at', '=', null)
                        ->get();

                        $item['cost'] = '';
                        $item['price'] = '';
                        $item['name'] = '';
                        $item['code'] = '';
                        $item['quantity'] = '';

                        foreach ($product_variant_data as $product_variant) {
                            $item['cost']  .= $this->render_price_with_symbol_placement(number_format($product_variant->cost, 2, '.', ','), $symbol_placement);
                            $item['cost']  .= '<br>';

                            $item['price'] .= $this->render_price_with_symbol_placement(number_format($product_variant->price, 2, '.', ','), $symbol_placement);
                            $item['price'] .= '<br>';

                            $item['name'] .= '['.$product_variant->name . ']' . $product->name;
                            $item['name'] .= '<br>';

                            $item['code'] .= $product_variant->code;
                            $item['code'] .= '<br>';

                            $product_warehouse_total_qty = product_warehouse::where('product_id', $product->id)
                            ->where('product_variant_id', $product_variant->id)
                            ->where(function ($query) use ($array_warehouses_id) {
                                return $query->whereIn('warehouse_id', $array_warehouses_id);
                            })
                            ->where('deleted_at', '=', null)
                            ->sum('qte');

                            $item['quantity'] .= $product_warehouse_total_qty .' '.$product['unit']->ShortName;
                            $item['quantity'] .= '<br>';
                        }



                    }else{
                        $item['type'] = 'Service';
                        $item['name'] = $product->name;
                        $item['code'] = $product->code;
                        $item['cost'] = '----';
                        $item['quantity'] = '----';
                        $item['price'] = $this->render_price_with_symbol_placement(number_format($product->price, 2, '.', ','), $symbol_placement);
                    }


                    if($product->image != 'no_image.png')
                    {
                        $url = $product->image;
                    }else{
                        $url = url("images/products/".$product->image);
                    }
                     //$url = url("images/products/".$product->image);
                     $item['image'] =
                        '<div class="avatar mr-2 avatar-md">
                            <img
                                src="'.$url.'" alt="">
                        </div>';

                    $item['action'] = '<button type="button" class="btn bg-transparent _r_btn border-0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <span class="_dot _r_block-dot bg-dark"></span>
                        <span class="_dot _r_block-dot bg-dark"></span>
                        <span class="_dot _r_block-dot bg-dark"></span>
                    </button>';

                    $item['action'] .= '<div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(686px, 50px, 0px);">';

                    if ($user_auth->can('products_edit')){
                        $item['action'] .= '<a class="dropdown-item" href="/products/products/' .$product->id. '/edit" id="' .$product->id. '"><i class="nav-icon i-Edit text-success font-weight-bold m3-2"></i> ' .trans('translate.edit_product').'</a>';
                    }
                    if ($user_auth->can('products_delete')){
                        $item['action'] .= '  <a class="delete dropdown-item cursor-pointer" id="' .$product->id. '"><i class="nav-icon i-Close-Window text-danger font-weight-bold mr-3"></i> ' .trans('translate.delete_product').'</a>';
                    }
                    $item['action'] .= '</div>';


                    $data[] = $item;
                }

                $json_data = array(
                    "draw"            => intval($request->input('draw')),
                    "recordsTotal"    => intval($totalRows),
                    "recordsFiltered" => intval($totalFiltered),
                    "data"            => $data
                );

                echo json_encode($json_data);
            }

     }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $files = File::files(public_path('product_upload'));
        if(count($files) > 0)
        {
            foreach($files as $file) {
                unlink($file);
            }
        }
        $user_auth = auth()->user();
		if ($user_auth->can('products_add')){

            $api = (new AdminApi())->subfolders("products");
            $json = json_encode($api);
            $decoded_data = json_decode($json);

            $data_items = $decoded_data->folders;
            $profiles = productProfile::all();
            return view('products.create_new_product', compact('data_items', 'profiles'));

        }
        return abort('403', __('You are not authorized'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('products_add')){
                \DB::transaction(function () use ($request) {

                    //FIND PROFILE
                    $profile = productProfile::where('name', $request->name)->first();
                    if($profile != null)
                    {
                        $product_name = $profile->name;
                    }else{
                        $product_name = $request->name;
                    }
                    //-- Create New Product
                    $Product = new Product;

                    //-- Field Required
                    $Product->type                   = 'is_single';
                    $Product->code                   = rand(11111111,99999999);
                    $Product->Type_barcode           = "CODE128";
                    $Product->name                   = $request->name;
                    $Product->account_holder         = $request->account_holder;
                    $Product->email                  = $request->email;
                    $Product->email_password         = $request->email_password;
                    $Product->recovery_email         = $request->recovery_email;
                    $Product->account_email          = $request->account_email;
                    $Product->account_password       = $request->account_password;
                    $Product->passcode_pin           = $request->passcode_pin;
                    $Product->number_company         = $request->number_company;
                    $Product->number_email_username  = $request->number_email_username;
                    $Product->number_password        = $request->number_password;
                    $Product->mobile_number          = $request->mobile_number;
                    $Product->proxy_website          = $request->proxy_website;
                    $Product->proxy_ip_host          = $request->proxy_ip_host;
                    $Product->port                   = $request->port;
                    $Product->proxy_username         = $request->proxy_username;
                    $Product->proxy_password         = $request->proxy_password;
                    $Product->note                   = $request->note;
                    $Product->cost                   = $request->cost;
                    $Product->price                  = '0';
                    $Product->category_id            = '1';
                    $Product->brand_id               = '1';
                    $Product->unit_id                = '1';
                    $Product->unit_sale_id           = '1';
                    $Product->unit_purchase_id       = '1';
                    //$Product->save();

                     // Expense Data
                     Expense::create([
                        'expense_ref'            => $request->name,
                        'account_id'             => 1,
                        'expense_category_id'    => 1,
                        'amount'                 => $Product->cost,
                        'payment_method_id'      => 6,
                        'date'                   => date('Y-m-d',strtotime(now())),
                        //'attachment'             => $filename,
                        'description'            => $Product->note,
                    ]);

                    //FOLDER CREATE
                    if($request['attatchement_folder_name'] != NULL)
                    {
                        $result = (new AdminApi())->createFolder('products/'.$request['attatchement_folder_name']);
                        $json =  json_encode($result);
                        $data = json_decode($json);

                        $json =  json_encode($result);
                        $data = json_decode($json);

                        $existing_attatchment_id = $data->name;
                    }else{
                        $existing_attatchment_id = $request->existing_attatchment_id;
                    }

                    if($profile != null)
                    {
                        $image = $request->file('image');
                        $result = (new UploadApi())->upload($profile->image, [
                            'folder' => 'products/'.$existing_attatchment_id.'/',
                            'resource_type' => 'image']);
                        $json =  json_encode($result);
                        $data = json_decode($json);
                        $data_profile_image = $data->secure_url;
                    }else{
                        if ($request->hasFile('image')) {

                            $image = $request->file('image');
                            $result = (new UploadApi())->upload($request->file('image')->getRealPath(), [
                                'folder' => 'products/'.$existing_attatchment_id.'/',
                                'resource_type' => 'image']);
                            $json =  json_encode($result);
                            $data = json_decode($json);
                            $data_profile_image = $data->secure_url;
                        } else {
                            $filename = 'no_image.png';
                            $data_profile_image = $filename;
                        }
                    }

                    $get_existing_attatchment_id = Product::where('existing_attatchment_id', $existing_attatchment_id)
                                                            ->orderBy('id', 'ASC')
                                                            ->first();
                                                            //$existing_attatchment_id
                    if($get_existing_attatchment_id != null)
                    {
                        $Product->existing_attatchment_id = $existing_attatchment_id;
                        $Product->image = $data_profile_image;
                        $Product->public_id = $get_existing_attatchment_id->public_id;
                        $Product->secure_url = $get_existing_attatchment_id->secure_url;
                        $Product->save();
                    }else{
                        //UPLOAD PROFILE IMAGE
                        //ZIP FILE UPLOAD IN CLOUDENERY
                        $zip = new ZipArchive;
                        $zipFileName = rand(111111,999999).".zip";
                        $files = File::files(public_path('product_upload'));

                        if(count($files) > 0)
                        {
                            if ($zip->open(public_path('product_upload/zip/'.$zipFileName), ZipArchive::CREATE) === TRUE) {
                                //FILES ARRAY
                                $filesToZip = array();
                                foreach($files as $file) {
                                    array_push($filesToZip, $file->getRealPath());
                                }

                                //FILE ADDING TO ZIP
                                foreach ($filesToZip as $file) {
                                    $zip->addFile($file, basename($file));
                                }
                                $zip->close();
                            }
                            //DELETING FILES
                            foreach($files as $file) {
                                unlink($file);
                            }
                            //UPLOADING ZIP FILE
                            $zipfile = public_path('product_upload/zip/'.$zipFileName);
                            $result = (new UploadApi())->upload($zipfile, [
                                'folder' => 'products/'.$existing_attatchment_id.'/',
                                'resource_type' => 'raw']);
                            $zip_json =  json_encode($result);
                            $zip_data = json_decode($zip_json);

                            $Product->existing_attatchment_id = $existing_attatchment_id;
                            $Product->image = $data_profile_image;
                            $Product->public_id = $zip_data->public_id;
                            $Product->secure_url = $zip_data->secure_url;
                            $Product->save();
                            if(fileExists($zipfile))
                            {
                                unlink($zipfile);
                            }
                        }else{
                            $Product->existing_attatchment_id = $existing_attatchment_id;
                            $Product->image = $data_profile_image;
                            $Product->save();
                        }

                    }

                    $account = Account::findOrFail(1);
                    $account->update([
                        'initial_balance' => $account->initial_balance - $Product->cost,
                    ]);

                    //--Store Product Warehouse

                    $warehouse = new product_warehouse();

                    $warehouse->product_id   = $Product->id;
                    $warehouse->warehouse_id = '1';
                    $warehouse->qte          = '1';
                    $warehouse->manage_stock = '1';
                    $warehouse->save();

                }, 10);

                return redirect()->route('products.index')->with('successMessage', 'Product successfully created!');
        }
        return abort('403', __('You are not authorized'));

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */

    public function show($id)
    {
        //
    }

    public function show_product_data($id , $variant_id)
    {

        $Product_data = Product::with('unit')
            ->where('id', $id)
            ->where('deleted_at', '=', null)
            ->first();

        $data = [];
        $item['id']           = $Product_data['id'];
        $item['image']        = $Product_data['image'];
        $item['product_type'] = $Product_data['type'];
        $item['Type_barcode'] = $Product_data['Type_barcode'];
        $item['qty_min']      = $Product_data['qty_min'];

        $item['unit_id'] = $Product_data['unit']?$Product_data['unit']->id:'';
        $item['unit']    = $Product_data['unit']?$Product_data['unit']->ShortName:'';

        $item['purchase_unit_id'] = $Product_data['unitPurchase']?$Product_data['unitPurchase']->id:'';
        $item['unitPurchase']     = $Product_data['unitPurchase']?$Product_data['unitPurchase']->ShortName:'';

        $item['sale_unit_id'] = $Product_data['unitSale']?$Product_data['unitSale']->id:'';
        $item['unitSale']     = $Product_data['unitSale']?$Product_data['unitSale']->ShortName:'';

        $item['tax_method']  = $Product_data['tax_method'];
        $item['tax_percent'] = $Product_data['TaxNet'];
        $item['is_imei']     = $Product_data['is_imei'];

        //product single
        if($Product_data['type'] == 'is_single'){
            $product_price = $Product_data['price'];
            $product_cost  = $Product_data['cost'];

            $item['code'] = $Product_data['code'];
            $item['name'] = $Product_data['name'];

        //product is_variant
        }elseif($Product_data['type'] == 'is_variant'){

            $product_variant_data = ProductVariant::where('product_id', $id)
            ->where('id', $variant_id)->first();

            $product_price = $product_variant_data['price'];
            $product_cost  = $product_variant_data['cost'];
            $item['code'] = $product_variant_data['code'];
            $item['name'] = '['.$product_variant_data['name'].']'.$Product_data['name'];

         //product is_service
        }else{

            $product_price = $Product_data['price'];
            $product_cost  = 0;

            $item['code'] = $Product_data['code'];
            $item['name'] = $Product_data['name'];
        }

          //check if product has promotion
          $todaydate = date('Y-m-d');

          if($Product_data['is_promo']
              && $todaydate >= $Product_data['promo_start_date']
              && $todaydate <= $Product_data['promo_end_date']){
                  $price_init = $Product_data['promo_price'];
                  $item['is_promotion'] = 1;
                  $item['promo_percent'] =  100 * ($product_price - $price_init) / $product_price;
          }else{
              $price_init = $product_price;
              $item['is_promotion'] = 0;
          }

        //check if product has Unit sale
        if ($Product_data['unitSale']) {

            if ($Product_data['unitSale']->operator == '/') {
                $price = $price_init / $Product_data['unitSale']->operator_value;

            } else {
                $price = $price_init * $Product_data['unitSale']->operator_value;
            }

        }else{
            $price = $price_init;
        }

        //check if product has Unit Purchase

        if ($Product_data['unitPurchase']) {

            if ($Product_data['unitPurchase']->operator == '/') {
                $cost = $product_cost / $Product_data['unitPurchase']->operator_value;
            } else {
                $cost = $product_cost * $Product_data['unitPurchase']->operator_value;
            }

        }else{
            $cost = 0;
        }

        $item['Unit_cost'] = $cost;
        $item['fix_cost'] = $product_cost;
        $item['Unit_price'] = $price;
        $item['fix_price'] = $price_init;

        if ($Product_data->TaxNet !== 0.0) {
            //Exclusive
            if ($Product_data['tax_method'] == '1') {
                $tax_price = $price * $Product_data['TaxNet'] / 100;
                $tax_cost = $cost * $Product_data['TaxNet'] / 100;

                $item['Total_cost'] = $cost + $tax_cost;
                $item['Total_price'] = $price + $tax_price;
                $item['Net_cost'] = $cost;
                $item['Net_price'] = $price;
                $item['tax_price'] = $tax_price;
                $item['tax_cost'] = $tax_cost;

                // Inxclusive
            } else {
                $item['Total_cost'] = $cost;
                $item['Total_price'] = $price;
                $item['Net_cost'] = $cost / (($Product_data['TaxNet'] / 100) + 1);
                $item['Net_price'] = $price / (($Product_data['TaxNet'] / 100) + 1);
                $item['tax_cost'] = $item['Total_cost'] - $item['Net_cost'];
                $item['tax_price'] = $item['Total_price'] - $item['Net_price'];
            }
        } else {
            $item['Total_cost'] = $cost;
            $item['Total_price'] = $price;
            $item['Net_cost'] = $cost;
            $item['Net_price'] = $price;
            $item['tax_price'] = 0;
            $item['tax_cost'] = 0;
        }

        $data[] = $item;

        return response()->json($data[0]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $files = File::files(public_path('product_upload'));
        if(count($files) > 0)
        {
            foreach($files as $file) {
                unlink($file);
            }
        }

        $user_auth = auth()->user();
		if ($user_auth->can('products_edit')){

            $product = Product::where('deleted_at', '=', null)->findOrFail($id);

            $api = (new AdminApi())->subfolders("products");
            $json = json_encode($api);
            $decoded_data = json_decode($json);

            $data_items = $decoded_data->folders;
            $profiles = productProfile::all();
            return view('products.edit_new_product',compact('product', 'data_items', 'profiles'));

        }
        return abort('403', __('You are not authorized'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {

        $user_auth = auth()->user();
		if ($user_auth->can('products_edit')){
            $Product = Product::where('id', $id)
                            ->where('deleted_at', '=', null)
                            ->first();

            try {
                \DB::transaction(function () use ($request, $id) {

                    //FIND PROFILE
                    $profile = productProfile::where('name', $request->name)->first();
                    if($profile != null)
                    {
                        $product_name = $profile->name;
                    }else{
                        $product_name = $request->name;
                    }

                    $Product = Product::where('id', $id)
                        ->where('deleted_at', '=', null)
                        ->first();

                    $old_existing_attatchment_id = $Product->existing_attatchment_id;

                    $Product->name                   = $request->name;
                    $Product->account_holder         = $request->account_holder;
                    $Product->email                  = $request->email;
                    $Product->email_password         = $request->email_password;
                    $Product->recovery_email         = $request->recovery_email;
                    $Product->account_email          = $request->account_email;
                    $Product->account_password       = $request->account_password;
                    $Product->passcode_pin           = $request->passcode_pin;
                    $Product->number_company         = $request->number_company;
                    $Product->number_email_username  = $request->number_email_username;
                    $Product->number_password        = $request->number_password;
                    $Product->mobile_number          = $request->mobile_number;
                    $Product->proxy_website          = $request->proxy_website;
                    $Product->proxy_ip_host          = $request->proxy_ip_host;
                    $Product->port                   = $request->port;
                    $Product->proxy_username         = $request->proxy_username;
                    $Product->proxy_password         = $request->proxy_password;
                    $Product->note                   = $request->note;
                    $Product->cost                   = $request->cost;


                    $Product->existing_attatchment_id = $request->existing_attatchment_id;
                    $Product->save();

                    if($profile != null)
                    {
                        if($profile->name != $Product->name)
                        {
                            $result = (new UploadApi())->upload($profile->image, [
                                'folder' => 'products/'.$Product->existing_attatchment_id.'/',
                                'resource_type' => 'image']);
                            $json =  json_encode($result);
                            $data = json_decode($json);
                            $Product->image = $data->secure_url;
                            $Product->save();
                        }
                    }

                    $get_existing_attatchment_id = Product::where('existing_attatchment_id', $old_existing_attatchment_id)
                                                             ->orderBy('id', 'ASC')
                                                             ->first();
                    if($get_existing_attatchment_id != null)
                    {
                        $Product->public_id = $get_existing_attatchment_id->public_id;
                        $Product->secure_url = $get_existing_attatchment_id->secure_url;
                        $Product->save();
                    }
                }, 10);

                return redirect()->route('products.edit', $Product->id)->with('successMessage', 'Product successfully created!');

            } catch (ValidationException $e) {
                dd($e);
            }

        }
        return abort('403', __('You are not authorized'));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('products_delete')){

            \DB::transaction(function () use ($id) {

                $Product = Product::findOrFail($id);

                Expense::create([
                    'expense_ref'            => 'Product_id-'.$Product->id,
                    'account_id'             => 1,
                    'expense_category_id'    => 1,
                    'amount'                 => $Product->cost,
                    'payment_method_id'      => 6,
                    'date'                   => date('Y-m-d',strtotime(now())),
                    //'attachment'             => $filename,
                    'description'            => $Product->note,
                ]);

                $account = Account::findOrFail(1);
                $account->update([
                    'initial_balance' => $account->initial_balance + $Product->cost,
                ]);
                $Product->deleted_at = Carbon::now();
                $Product->save();

                $path = public_path() . '/images/products';
                $pr_image = $path . '/' . $Product->image;
                if (file_exists($pr_image)) {
                    if ($Product->image != 'no_image.png') {
                        @unlink($pr_image);
                    }
                }

                product_warehouse::where('product_id', $id)->update([
                    'deleted_at' => Carbon::now(),
                ]);

                ProductVariant::where('product_id', $id)->update([
                    'deleted_at' => Carbon::now(),
                ]);

            }, 10);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));
    }

    //-------------- Delete by selection  ---------------\\

    public function delete_by_selection(Request $request)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('products_delete')){

            \DB::transaction(function () use ($request) {
                $selectedIds = $request->selectedIds;
                foreach ($selectedIds as $product_id) {

                    $Product = Product::findOrFail($product_id);
                    $Product->deleted_at = Carbon::now();
                    $Product->save();

                    $path = public_path() . '/images/products';
                    $pr_image = $path . '/' . $Product->image;
                    if (file_exists($pr_image)) {
                        if ($Product->image != 'no_image.png') {
                            @unlink($pr_image);
                        }
                    }

                    product_warehouse::where('product_id', $product_id)->update([
                        'deleted_at' => Carbon::now(),
                    ]);

                    ProductVariant::where('product_id', $product_id)->update([
                        'deleted_at' => Carbon::now(),
                    ]);
                }

            }, 10);

            return response()->json(['success' => true]);

        }
        return abort('403', __('You are not authorized'));

    }


    //------------ Get products By Warehouse -----------------\\

    public function Products_by_Warehouse(request $request, $id)
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

            })->get();

        foreach ($product_warehouse_data as $product_warehouse) {

            if ($product_warehouse->product_variant_id) {

                $item['product_variant_id'] = $product_warehouse->product_variant_id;
                $item['code'] = $product_warehouse['productVariant']->code;
                $item['Variant'] = '['.$product_warehouse['productVariant']->name . ']' . $product_warehouse['product']->name;
                $item['name'] = '['.$product_warehouse['productVariant']->name . ']' . $product_warehouse['product']->name;
                $item['barcode'] = $product_warehouse['productVariant']->code;


                $product_price = $product_warehouse['productVariant']->price;

            } else {
                $item['product_variant_id'] = null;
                $item['Variant'] = null;
                $item['code'] = $product_warehouse['product']->code;
                $item['name'] = $product_warehouse['product']->name;
                $item['barcode'] = $product_warehouse['product']->code;

                $product_price =  $product_warehouse['product']->price;
            }

            $item['id'] = $product_warehouse->product_id;
            $item['product_type'] = $product_warehouse['product']->type;
            $item['qty_min'] = $product_warehouse['product']->qty_min;
            $item['Type_barcode'] = $product_warehouse['product']->Type_barcode;
            $item['image'] = $product_warehouse['product']->image;

            if($product_warehouse['product']['unitSale']){

                if($product_warehouse['product']['unitSale']->operator == '/') {
                    $item['qte_sale'] = $product_warehouse->qte * $product_warehouse['product']['unitSale']->operator_value;
                    $price = $product_price / $product_warehouse['product']['unitSale']->operator_value;

                }else{
                    $item['qte_sale'] = $product_warehouse->qte / $product_warehouse['product']['unitSale']->operator_value;
                    $price = $product_price * $product_warehouse['product']['unitSale']->operator_value;
                }

            }else{
                $item['qte_sale'] = $product_warehouse->qte;
                $price = $product_price;
            }

            if($product_warehouse['product']['unitPurchase']) {

                if($product_warehouse['product']['unitPurchase']->operator == '/') {
                    $item['qte_purchase'] = round($product_warehouse->qte * $product_warehouse['product']['unitPurchase']->operator_value, 5);

                }else{
                    $item['qte_purchase'] = round($product_warehouse->qte / $product_warehouse['product']['unitPurchase']->operator_value, 5);
                }

            }else{
                $item['qte_purchase'] = $product_warehouse->qte;
            }

            $item['manage_stock'] = $product_warehouse->manage_stock;
            $item['qte'] = $product_warehouse->qte;
            $item['unitSale'] = $product_warehouse['product']['unitSale']?$product_warehouse['product']['unitSale']->ShortName:'';
            $item['unitPurchase'] = $product_warehouse['product']['unitPurchase']?$product_warehouse['product']['unitPurchase']->ShortName:'';

            if ($product_warehouse['product']->TaxNet !== 0.0) {
                //Exclusive
                if ($product_warehouse['product']->tax_method == '1') {
                    $tax_price = $price * $product_warehouse['product']->TaxNet / 100;
                    $item['Net_price'] = $price + $tax_price;
                    // Inxclusive
                } else {
                    $item['Net_price'] = $price;
                }
            } else {
                $item['Net_price'] = $price;
            }

            $data[] = $item;
        }

        return response()->json($data);
    }


      //--------------  Product Quantity Alerts ---------------\\

      public function Products_Alert(request $request)
      {

          $product_warehouse_data = product_warehouse::with('warehouse', 'product', 'productVariant')
              ->join('products', 'product_warehouse.product_id', '=', 'products.id')
              ->whereRaw('qte <= stock_alert')
              ->where(function ($query) use ($request) {
                  return $query->when($request->filled('warehouse'), function ($query) use ($request) {
                      return $query->where('warehouse_id', $request->warehouse);
                  });
              })->where('product_warehouse.deleted_at', null)->get();

          $data = [];

          if ($product_warehouse_data->isNotEmpty()) {

              foreach ($product_warehouse_data as $product_warehouse) {
                  if ($product_warehouse->qte <= $product_warehouse['product']->stock_alert) {
                      if ($product_warehouse->product_variant_id) {
                          $item['code'] = $product_warehouse['productVariant']->name . '-' . $product_warehouse['product']->code;
                      } else {
                          $item['code'] = $product_warehouse['product']->code;
                      }
                      $item['quantity'] = $product_warehouse->qte;
                      $item['name'] = $product_warehouse['product']->name;
                      $item['warehouse'] = $product_warehouse['warehouse']->name;
                      $item['stock_alert'] = $product_warehouse['product']->stock_alert;
                      $data[] = $item;
                  }
              }
          }

          $perPage = $request->limit; // How many items do you want to display.
          $pageStart = \Request::get('page', 1);
          // Start displaying items from this number;
          $offSet = ($pageStart * $perPage) - $perPage;
          $collection = collect($data);
          // Get only the items you need using array_slice
          $data_collection = $collection->slice($offSet, $perPage)->values();

          $products = new LengthAwarePaginator($data_collection, count($data), $perPage, Paginator::resolveCurrentPage(), array('path' => Paginator::resolveCurrentPath()));

           //get warehouses assigned to user
           $user_auth = auth()->user();
           if($user_auth->is_all_warehouses){
               $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
           }else{
               $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
               $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
           }

          return response()->json([
              'products' => $products,
              'warehouses' => $warehouses,
          ]);
      }


    //---------------- Show Elements Barcode ---------------\\

    public function Get_element_barcode(Request $request)
    {

         //get warehouses assigned to user
         $user_auth = auth()->user();
         if($user_auth->is_all_warehouses){
             $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
         }else{
             $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
             $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
         }

        return response()->json(['warehouses' => $warehouses]);

    }

    public function import_products_page()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('products_add')){

            return view('products.import_products');
        }
        return abort('403', __('You are not authorized'));
    }



    // import Products
    public function import_products(Request $request)
    {
        $user_auth = auth()->user();
		if ($user_auth->can('products_add')){

            //Set maximum php execution time
            ini_set('max_execution_time', 0);

            $request->validate([
                'products' => 'required|mimes:xls,xlsx',
            ]);

            $product_array = Excel::toArray(new ProductImport, $request->file('products'));

            $warehouses = Warehouse::where('deleted_at', null)->pluck('id')->toArray();

            $products = [];

            $code_array = [];
            foreach ($product_array[0] as $key => $value) {

                $code_array[] = $value['code'];

                //--Product name
                if($value['name'] != ''){
                    $row['name'] = $value['name'];
                }else{
                    return back()->with('error','Nom du produit n\'existe pas!');
                }

                 //--Product code
                 if($value['code'] != ''){
                    if (Product::where('code', $value['code'])->where('deleted_at', '=', null)->exists()) {
                        return back()->with('error','Code du produit'.' "'.$value['name'].'" '.'duplicate!');
                    }else{
                        $row['code'] = $value['code'];
                    }
                }else{
                    return back()->with('error','Code du produit'.' "'.$value['name'].'" '.'n\'existe pas!');
                }

                //--Product price
                if($value['price'] != '' && is_numeric($value['price'])){
                    $row['price'] = $value['price'];
                }else{
                    return back()->with('error','Price du produit'.' "'.$value['name'].'" '.'Incorrect or Null!');
                }

                //--Product cost
                if($value['cost'] != '' && is_numeric($value['cost'])){
                    $row['cost'] = $value['cost'];
                }else{
                     return back()->with('error','Cost du produit'.' "'.$value['name'].'" '.'Incorrect or Null!');
                }

                //--Product category_id
                $category = Category::where(['name' => $value['category']])->first();
                if($category){
                    $row['category_id'] = $category->id;
                }else{
                    return back()->with('error','Catégorie du produit'.' "'.$value['name'].'" '.'n\'existe pas!');
                }

                //--Product unit_id
                $unit = Unit::where(['ShortName' => $value['unit']])->orWhere(['name' => $value['unit']])->first();
                if($unit){
                    $row['unit_id'] = $unit->id;
                    $row['unit_sale_id'] = $unit->id;
                    $row['unit_purchase_id'] = $unit->id;
                }else{
                    return back()->with('error','Unit du produit'.' "'.$value['name'].'" '.'n\'existe pas!');
                }

                //--Product brand_id
                if ($value['brand'] != '') {
                    $brand = Brand::where(['name' => $value['brand']])->first();
                    if($brand){
                        $row['brand_id'] = $brand->id;
                    }else{
                        return back()->with('error','Brand du produit'.' "'.$value['name'].'" '.'n\'existe pas!');
                    }
                } else {
                    $row['brand_id'] = NULL;
                }

                //--Product qty_min
                if ($value['qty_min_sale'] != '' && is_numeric($value['qty_min_sale'])) {
                    $row['qty_min'] = $value['qty_min_sale'];
                } else {
                    $row['qty_min'] = 0;
                }

                //--Product stock_alert
                if ($value['stock_alert'] != '' && is_numeric($value['stock_alert'])) {
                    $row['stock_alert'] = $value['stock_alert'];
                } else {
                    $row['stock_alert'] = 0;
                }

                //--Product Note
                if ($value['note'] != '') {
                    $row['note'] = $value['note'];
                } else {
                    $row['note'] = NULL;
                }

                $products[]= $row;
            }

             $duplicate = false;

            if(count($product_array[0]) != count(array_unique($code_array))){
                $duplicate = true;
                return back()->with('error','le code produit est dupliqué');
            }

            foreach ($products as $key => $product_value) {

                $Product = new Product;

                $Product->name = $product_value['name'];
                $Product->qty_min = $product_value['qty_min'];
                $Product->code = $product_value['code'];
                $Product->price = $product_value['price'];
                $Product->cost = $product_value['cost'];
                $Product->category_id = $product_value['category_id'];
                $Product->brand_id = $product_value['brand_id'];
                $Product->note = $product_value['note'];
                $Product->unit_id =$product_value['unit_id'];
                $Product->unit_sale_id = $product_value['unit_sale_id'];
                $Product->unit_purchase_id = $product_value['unit_purchase_id'];
                $Product->stock_alert = $product_value['stock_alert'];

                //default value
                $Product->type = 'is_single';
                $Product->Type_barcode = 'CODE128';
                $Product->image = 'no_image.png';
                $Product->TaxNet = 0;
                $Product->tax_method = 1;
                $Product->is_variant = 0;
                $Product->is_imei = 0;
                $Product->not_selling = 0;
                $Product->is_active = 1;
                $Product->save();

                if ($warehouses) {
                    foreach ($warehouses as $warehouse) {
                        $product_warehouse[] = [
                            'product_id' => $Product->id,
                            'warehouse_id' => $warehouse,
                        ];
                    }
                }
            }

            if ($warehouses) {
                product_warehouse::insert($product_warehouse);
            }

            return redirect()->back()->with('success','Products Imported successfully!');

        }
        return abort('403', __('You are not authorized'));

    }

    // Generate_random_code
    public function generate_random_code()
    {
        $gen_code = substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);

       if (Product::where('code', $gen_code)->exists()) {
           $this->generate_random_code();
       } else {
           return $gen_code;
       }

    }


    public function print_labels()
    {
        $user_auth = auth()->user();
		if ($user_auth->can('print_labels')){

            //get warehouses assigned to user
            if($user_auth->is_all_warehouses){
                $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
            }else{
                $warehouses_id = UserWarehouse::where('user_id', $user_auth->id)->pluck('warehouse_id')->toArray();
                $warehouses = Warehouse::where('deleted_at', '=', null)->whereIn('id', $warehouses_id)->get(['id', 'name']);
            }

            return view('products.print_labels', compact('warehouses'));

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

    public function fileUpload(Request $request){
        return Plupload::receive('file', function ($file)
        {
            $file->move(public_path() . '/product_upload/', $file->getClientOriginalName());

            return 'ready';
        });
    }

    public function getAjaxProduct(Request $request)
    {
        $product = Product::where('id', $request->product_id)->first();
        return response()->json([
            'product' => $product
        ]);
    }

}

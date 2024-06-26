<?php

use App\Http\Controllers\productProfileController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\sessionController;
use App\Mail\CustomEmail;
use App\Mail\OnSaleMail;
use App\Models\Account;
use App\Models\Client;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\product_warehouse;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Setting;
use App\utils\helpers;
use Cloudinary\Api\Admin\AdminApi;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use ZanySoft\Zip\Facades\Zip as FacadesZip;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/storage_link', function (){ Artisan::call('storage:link'); });
$installed = Storage::disk('public')->exists('installed');

if ($installed === true) {

    Route::get('zip', function(){

        $products = product_warehouse::where('qte', '0')->get();
        foreach($products as $data)
        {
            $getP = Product::where('id', $data->product_id)->first();
            $getP->sold = '1';
            $getP->save();
        }
        // $result = (new AdminApi())->assetByAssetId("300ff9fd67c9e8f814c5839950f9992c");
        // $json = json_encode($result);
        // $data = json_decode($json);
        // dd($data);


        // $zip = new ZipArchive;
        // $zipFileName = rand(111111,999999).".zip";
        // $files = File::files(public_path('product_upload'));

        // if ($zip->open(public_path('product_upload/zip/'.$zipFileName), ZipArchive::CREATE) === TRUE) {

        //     if(count($files) > 0)
        //     {
        //         //FILES ARRAY
        //         $filesToZip = array();
        //         foreach($files as $file) {
        //             array_push($filesToZip, $file->getRealPath());
        //             // unlink($file);
        //         }

        //         //FILE ADDING TO ZIP
        //         foreach ($filesToZip as $file) {
        //             $zip->addFile($file, basename($file));
        //         }
        //     }
        //     $zip->close();
        //     //return response()->download(public_path($zipFileName))->deleteFileAfterSend(true);
        // }
        // $zipfile = public_path('product_upload/zip/'.$zipFileName);
        // $result = (new UploadApi())->upload($zipfile, [
        // 'folder' => 'zip/',
        // 'resource_type' => 'raw']);
        // $json =  json_encode($result);
        // $data = json_decode($json);
        // dd($data);

//         $public_ids = ['zip/iskidclxtzb6n8vxk3nx.zip'];
// $result = (new AdminApi())->deleteAssets($public_ids,
//     ["resource_type" => "raw", "type" => "upload"]);
// echo json_encode($result, JSON_PRETTY_PRINT);
    });
    // Route::get('pdf', function(){
    //     //return view('pdf.on_sale_pdf');
    //     $settings = Setting::where('deleted_at', '=', null)->first();
    //     $sale = Sale::with('client')->where('id', '1')->first();
    //     $sale_details = SaleDetail::where('sale_id', $sale->id)->get();

    //     if(count($sale_details) > 0)
    //     {
    //         foreach($sale_details as $data)
    //         {
    //             $Html = view('pdf.on_sale_pdf', [
    //                 'sale' => $sale,
    //                 'setting' => $settings
    //             ])->render();
    //             $pdf = PDF::loadHTML($Html);
    //             $content = $pdf->download()->getOriginalContent();
    //             Storage::put('public/sale/sale_'.$sale->id.'_item_'.$data->id.'.pdf',$content) ;

    //             //SEND MAIL
    //             $config = array(
    //                 'driver' => env('MAIL_MAILER'),
    //                 'host' => env('MAIL_HOST'),
    //                 'port' => env('MAIL_PORT'),
    //                 'from' => array('address' => env('MAIL_FROM_ADDRESS'), 'name' =>  env('MAIL_FROM_NAME')),
    //                 'encryption' => env('MAIL_ENCRYPTION'),
    //                 'username' => env('MAIL_USERNAME'),
    //                 'password' => env('MAIL_PASSWORD'),
    //                 'sendmail' => '/usr/sbin/sendmail -bs',
    //                 'pretend' => false,
    //                 'stream' => [
    //                     'ssl' => [
    //                         'allow_self_signed' => true,
    //                         'verify_peer' => false,
    //                         'verify_peer_name' => false,
    //                     ],
    //                 ],
    //             );
    //             Config::set('mail', $config);

    //             //$this->Set_config_mail();

    //             $email['subject'] = 'Test From One';
    //             $email['body'] = 'Hello Body';
    //             $email['company_name'] = 'Posly';
    //             $email['file'] = url('/public/storage/sale/sale_'.$sale->id.'_item_'.$data->id.'.pdf');
    //             $email['client'] = $sale->client->username;
    //             $email['invoice_id'] = $sale->Ref;
    //             $mail = Mail::to('sarwardnj@gmail.com')->send(new OnSaleMail($email));
    //         }
    //     }
    // });

    //Route::get('c-api', function(){
        // $api = (new AdminApi())->subfolders("products");
        // $json = json_encode($api);
        // $data = json_decode($json);
        // dd( $data->folders);
        // $result = (new AdminApi())->createFolder('datas3');
        // $json =  json_encode($result);
        // $data = json_decode($json);

        // dd($data->path);

            // // Your input string
            // $inputString = "https://res.cloudinary.com/dbgmtft6w/image/upload/v1702474736/products/6/hktlwp1z81pkbqa3yfkk.jpg";

            // // Find the position of the last special character
            // $lastSpecialCharPosition = strrpos($inputString, "/");

            // // Check if a special character was found
            // if ($lastSpecialCharPosition !== false) {
            //     // Get all characters after the last special character
            //     $result = substr($inputString, $lastSpecialCharPosition + 1);

            //     // Output the result
            //     echo "Original String: $inputString<br>";
            //     echo "Characters after the last special character: $result";
            // } else {
            //     // No special character found
            //     echo "No special character found in the string.";
            // }

        //     $files = File::files(public_path('product_upload'));

        //     if(count($files) > 0)
        //     {
        //         foreach($files as $file) {
        //         $ext = pathinfo($file, PATHINFO_EXTENSION);
        //         if(($ext == 'jpg') || ($ext == 'JPG') || ($ext == 'jpeg') || ($ext == 'JPEG') || ($ext == 'png') || ($ext == 'PNG'))
        //         {
        //             $result = (new UploadApi())->upload($file->getRealPath(), [
        //                 'folder' => 'test',
        //                 'resource_type' => 'image']);
        //         }

        //         if(($ext == 'pdf') || ($ext == 'PDF'))
        //         {
        //             $result = (new UploadApi())->upload($file->getRealPath(), [
        //                 'folder' => 'test',
        //                 'resource_type' => 'raw']);
        //         }
        //         unlink($file);
        //     }
        //    }
            // $result = (new UploadApi())->upload('https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_92x30dp.png', [
            //     'folder' => 'products/12',
            //     'resource_type' => 'image']);
            // $json =  json_encode($result);
            // $data = json_decode($json);
            // dd($data->secure_url);
            // dd($data['public_id']);
            // dd($data['format']);
            // dd($data['format']);
            // resource_type
            // url
            // secure_url
            // folder

        //         $result = (new AdminApi())->assetByAssetId("74b80095cc78c3c490ccd603d694ef17");
        //   echo json_encode($result, JSON_PRETTY_PRINT);
    //});
    Auth::routes(['register' => false]);

    Route::get('file', function(){
        $files = File::files(public_path('product_upload'));
        foreach($files as $file) {
            $file_path_info = pathinfo($file);

            dd($file_path_info);
        }
    });
    Route::get('/products/files/view/{code}', [ProductsController::class, 'fileView'])->name('product.file.view');

    Route::middleware(['XSS'])->group(function () {

        Route::get('/', "HomeController@RedirectToLogin");
        Route::get('switch/language/{lang}', 'LocalController@languageSwitch')->name('language.switch');

        //------------------------------- dashboard Admin--------------------------\\

        Route::group(['middleware'=>'auth','Is_Admin' , 'Is_Active'],function(){
            Route::get('dashboard/admin', "DashboardController@dashboard_admin")->name('dashboard');

            //------------------------------------------------------------------\\

            Route::get('/update_database', 'UpdateController@viewStep1');

            Route::get('/update_database/finish', function () {

                return view('update.finishedUpdate');
            });

            Route::post('/update_database/lastStep', [
                'as' => 'update_lastStep', 'uses' => 'UpdateController@lastStep',
            ]);

        });

        Route::middleware(['auth', 'Is_Active'])->group(function () {
            Route::get('session-add-to-cart', [sessionController::class, 'addToCart'])->name('session.add.to.cart');
            Route::get('session-remove-from-cart', [sessionController::class, 'removeFromCart'])->name('session.remove.from.cart');
            Route::get('session-update-price-cart', [sessionController::class, 'updatePriceCart'])->name('session.update.price.cart');
            Route::get('get-ajax-product', [ProductsController::class, 'getAjaxProduct'])->name('get.ajax.product');
            Route::post("file_uploader", [ProductsController::class, 'fileUpload'])->name('file-upload');
            Route::get('dashboard/employee', "DashboardController@dashboard_employee")->name('dashboard_employee');

            // Route::prefix('inventory')->group(function() {


                Route::get('dashboard_filter/{start_date}/{end_date}', "DashboardController@dashboard_filter");

                //-------------------------------  Reports ------------------------\\
                Route::prefix('reports')->group(function() {

                    // Route::get('report_profit', 'ReportController@report_profit')->name('report_profit');
                    // Route::get('report_profit/{start_date}/{end_date}/{warehouse}', 'ReportController@report_profit_filter')->name('report_profit_filter');

                    Route::get('report_profit', 'ReportController@report_profit_new')->name('report_profit');
                    Route::post('report_profit_post', 'ReportController@report_profit_new_post')->name('report_profit.post');

                    Route::get('report_stock', 'ReportController@report_stock_page')->name('report_stock');
                    Route::post('get_report_stock_datatable', 'ReportController@get_report_stock_datatable')->name('get_report_stock_datatable');

                    Route::get('report_product', 'ReportController@report_product')->name('report_product');
                    Route::post('get_report_product_datatable', 'ReportController@get_report_product_datatable')->name('get_report_product_datatable');

                    Route::get('report_clients', 'ReportController@report_clients')->name('report_clients');
                    Route::post('get_report_clients_datatable', 'ReportController@get_report_clients_datatable')->name('get_report_clients_datatable');

                    Route::get('report_providers', 'ReportController@report_providers')->name('report_providers');
                    Route::post('get_report_providers_datatable', 'ReportController@get_report_providers_datatable')->name('get_report_providers_datatable');

                    Route::get('sale_report', 'ReportController@sale_report')->name('sale_report');
                    Route::post('get_report_sales_datatable', 'ReportController@get_report_sales_datatable')->name('get_report_sales_datatable');
                    Route::get('report_monthly_sale', 'ReportController@report_monthly_sale')->name('report_monthly_sale');

                    Route::get('purchase_report', 'ReportController@purchase_report')->name('purchase_report');
                    Route::post('get_report_Purchases_datatable', 'ReportController@get_report_Purchases_datatable')->name('get_report_Purchases_datatable');
                    Route::get('report_monthly_purchase', 'ReportController@report_monthly_purchase')->name('report_monthly_purchase');

                    Route::get('payment_sale', 'ReportController@payment_sale_report')->name('payment_sale');
                    Route::post('get_payment_sale_reports_datatable', 'ReportController@get_payment_sale_reports_datatable')->name('get_payment_sale_reports_datatable');


                    Route::get('payment_purchase', 'ReportController@payment_purchase_report')->name('payment_purchase');
                    Route::post('get_payment_purchase_report_datatable', 'ReportController@get_payment_purchase_report_datatable')->name('get_payment_purchase_report_datatable');


                    Route::get('payment_sale_return', 'ReportController@payment_sale_return_report')->name('payment_sale_return_report');

                    Route::get('payment_purchase_return', 'ReportController@payment_purchase_return_report')->name('payment_purchase_return_report');

                    Route::get('reports_quantity_alerts', 'ReportController@reports_quantity_alerts')->name('reports_quantity_alerts');
                });

                //------------------------------- products--------------------------\\
                Route::get('delete-item-file', [ProductsController::class, 'fileDelete']);
                Route::prefix('products')->group(function() {
                    Route::resource('products', 'ProductsController')->except('update');
                    Route::get('sold', [ProductsController::class, 'soldIndex']);
                    Route::post('get_sold_product_datatable', [ProductsController::class, 'getSoldProductsDatatable'])->name('sold_products_datatable');
                    Route::get('unsold', [ProductsController::class, 'unSoldIndex']);
                    Route::post('get_unsold_product_datatable', [ProductsController::class, 'getUnSoldProductsDatatable'])->name('unsold_products_datatable');

                    Route::post('products/{id}/update-new', [ProductsController::class, 'update'])->name('products.update.new');
                    Route::post('get_product_datatable', 'ProductsController@get_product_datatable')->name('products_datatable');
                    Route::get('show_product_data/{id}/{variant_id}', 'ProductsController@show_product_data');

                    Route::get('products_by_Warehouse/{id}', 'ProductsController@Products_by_Warehouse');
                    Route::get('print_labels', 'ProductsController@print_labels')->name('print_labels');
                    Route::get('import_products', 'ProductsController@import_products_page');
                    Route::post('import_products', 'ProductsController@import_products');
                    Route::get('product-import', [ProductsController::class, 'importProduct'])->name('product.import');
                    Route::post('product-import-store', [ProductsController::class, 'importProductPost'])->name('product.import.store');



                    //------------------------------- product profiles--------------------------\\
                    Route::get('product-profiles', [productProfileController::class, 'index'])->name('product.profile.index');
                    Route::post('product-profile/store', [productProfileController::class, 'store'])->name('product.profile.store');

                    //------------------------------- categories--------------------------\\
                    Route::resource('categories', 'CategoriesController');

                    //------------------------------- brands--------------------------\\
                    Route::resource('brands', 'BrandsController');

                    //------------------------------- units--------------------------\\
                    Route::resource('units', 'UnitsController');
                    Route::get('Get_Units_SubBase', 'UnitsController@Get_Units_SubBase');
                    Route::get('Get_sales_units', 'UnitsController@Get_sales_units');

                    //------------------------------- warehouses--------------------------\\
                    Route::resource('warehouses', 'WarehousesController');
                });

                //------------------------------- adjustments--------------------------\\
                Route::resource('adjustment/adjustments', 'AdjustmentsController');


                //------------------------------- quotations --------------------------\\
                Route::prefix('quotation')->group(function() {
                    Route::resource('quotations', 'QuotationsController');
                    Route::post('get_quotations_datatable', 'QuotationsController@get_quotations_datatable')->name('get_quotations_datatable');
                });

                Route::post('quotations/sendQuote/email', 'QuotationsController@SendEmail');
                Route::get('quotations/generate_sale/{id}', 'QuotationsController@generate_sale');

                //------------------------------- purchases --------------------------\\
                Route::prefix('purchase')->group(function() {
                    Route::resource('purchases', 'PurchasesController');
                    Route::post('get_purchases_datatable', 'PurchasesController@get_purchases_datatable')->name('get_purchases_datatable');

                    Route::get('purchases/payments/{id}', 'PurchasesController@Get_Payments');
                    Route::post('purchases/send/email', 'PurchasesController@Send_Email');
                    Route::get('get_Products_by_purchase/{id}', 'PurchasesController@get_Products_by_purchase');
                });

                //------------------------------- Sales --------------------------\\
                Route::prefix('sale')->group(function() {
                    Route::resource('sales', 'SalesController');
                    Route::post('get_sales_datatable', 'SalesController@get_sales_datatable')->name('sales_datatable');

                    Route::post('sales/send/email', 'SalesController@Send_Email');
                    Route::get('sales/payments/{id}', 'SalesController@Payments_Sale');
                    Route::get('get_Products_by_sale/{id}', 'SalesController@get_Products_by_sale');
                    Route::get('sale_attatchment_download/{sale_id}/{item_id}', 'SalesController@sale_attatchment_download')->name('sale.attatchment.download');
                });

                //---------------------- POS (point of sales) ----------------------\\
                //------------------------------------------------------------------\\
                Route::get('pos', 'PosController@index');
                Route::post('pos/create_pos', 'PosController@CreatePOS')->name('pos.create');
                Route::get('pos/get_products_pos', 'PosController@GetProductsByParametre');
                Route::get('pos/data_create_pos', 'PosController@GetELementPos');
                Route::get('pos/autocomplete_product_pos/{id}', 'PosController@autocomplete_product_pos');
                Route::get('invoice_pos/{id}', 'PosController@Print_Invoice_POS');

                //------------------------------- transfers --------------------------\\
                Route::resource('transfer/transfers', 'TransfersController');

                //------------------------------- Sales Return --------------------------\\
                Route::prefix('sales-return')->group(function() {
                    Route::resource('returns_sale', 'SalesReturnController');
                    Route::get('returns/sale/payment/{id}', 'SalesReturnController@Payment_Returns');

                    Route::get('add_returns_sale/{id}', 'SalesReturnController@create_sell_return')->name('create_sell_return');
                    Route::get('edit_returns_sale/{id}/{sale_id}', 'SalesReturnController@edit_sell_return')->name('edit_sell_return');
                });

                //------------------------------- Purchases Return --------------------------\\
                Route::prefix('purchase-return')->group(function() {
                    Route::resource('returns_purchase', 'PurchasesReturnController');
                    Route::post('returns/purchase/send/email', 'PurchasesReturnController@Send_Email');
                    Route::get('returns/purchase/payment/{id}', 'PurchasesReturnController@Payment_Returns');

                    Route::get('add_returns_purchase/{id}', 'PurchasesReturnController@create_purchase_return')->name('create_purchase_return');
                    Route::get('edit_returns_purchase/{id}/{purchase_id}', 'PurchasesReturnController@edit_purchase_return')->name('edit_purchase_return');
                });

                //------------------------------- payment purchase --------------------------\\

                Route::resource('payment/purchase', 'PaymentPurchasesController');
                Route::get('payment/purchase/get_data_create/{id}', 'PaymentPurchasesController@get_data_create');
                Route::post('payment/purchase/send/email', 'PaymentPurchasesController@SendEmail');

                //------------------------------- payment sale --------------------------\\

                Route::resource('payment/sale', 'PaymentSalesController');
                Route::get('payment/sale/get_data_create/{id}', 'PaymentSalesController@get_data_create');
                Route::post('payment/sale/send/email', 'PaymentSalesController@SendEmail');

                //------------------------------- Payment Sale Returns --------------------------\\

                Route::resource('payment_returns_sale', 'PaymentSaleReturnsController');
                Route::get('payment/returns_sale/get_data_create/{id}', 'PaymentSaleReturnsController@get_data_create');
                Route::post('payment/returns_sale/send/email', 'PaymentSaleReturnsController@SendEmail');

                //------------------------------- Payments Purchase Returns --------------------------\\

                Route::resource('payment_returns_purchase', 'PaymentPurchaseReturnsController');
                Route::get('payment/returns_purchase/get_data_create/{id}', 'PaymentPurchaseReturnsController@get_data_create');
                Route::post('payment/returns_purchase/send/email', 'PaymentPurchaseReturnsController@SendEmail');

                //------------------------------- suppliers --------------------------\\

                 Route::resource('people/suppliers', 'SuppliersController');
                 Route::post('get_suppliers_datatable', 'SuppliersController@get_suppliers_datatable')->name('get_suppliers_datatable');

                 Route::post("suppliers/delete/by_selection", "SuppliersController@delete_by_selection");
                 Route::get('get_provider_debt_total/{id}', 'SuppliersController@get_provider_debt_total');
                 Route::post('providers_pay_due', 'SuppliersController@providers_pay_due');

                 Route::get('get_provider_debt_return_total/{id}', 'SuppliersController@get_provider_debt_return_total');
                 Route::post('providers_pay_return_due', 'SuppliersController@providers_pay_return_due');

                 //------------------------------- clients --------------------------\\

                 Route::resource('people/clients', 'ClientController');
                 Route::post('get_clients_datatable', 'ClientController@get_clients_datatable')->name('clients_datatable');

                 Route::get('get_client_plafond/{id}', 'ClientController@get_client_plafond');
                 Route::get('get_client_debt_total/{id}', 'ClientController@get_client_debt_total');
                 Route::get('get_client_debt_return_total/{id}', 'ClientController@get_client_debt_return_total');

                 Route::post('clients_pay_due', 'ClientController@clients_pay_due');
                 Route::post('clients_pay_return_due', 'ClientController@clients_pay_return_due');

                 Route::get('import_clients', 'ClientController@import_clients_page')->name('import_clients');
                 Route::post('import_clients', 'ClientController@import_clients');

                 //------------------------------- users & permissions --------------------------\\
                Route::prefix('user-management')->group(function() {
                    Route::resource('users', 'UserController');
                    Route::post('assignRole', 'UserController@assignRole');

                    Route::resource('permissions', 'PermissionsController');
                });
                //-------------------------------  --------------------------\\

                //------------------------------- Profile --------------------------\\
                //----------------------------------------------------------------\\
                Route::put('updateProfile/{id}', 'ProfileController@updateProfile');
                Route::resource('profile', 'ProfileController');

                //-------------------------------  Print & PDF ------------------------\\

                Route::get('Sale_PDF/{id}', 'SalesController@Sale_PDF');
                Route::get('Quote_PDF/{id}', 'QuotationsController@Quotation_pdf');
                Route::get('Purchase_PDF/{id}', 'PurchasesController@Purchase_pdf');
                Route::get('Payment_Purchase_PDF/{id}', 'PaymentPurchasesController@Payment_purchase_pdf');
                Route::get('payment_Sale_PDF/{id}', 'PaymentSalesController@payment_sale');
                Route::get('return_sale_pdf/{id}', 'SalesReturnController@Return_pdf');
                Route::get('return_purchase_pdf/{id}', 'PurchasesReturnController@Return_pdf');
                Route::get('payment_return_sale_pdf/{id}', 'PaymentSaleReturnsController@payment_return');
                Route::get('payment_return_purchase_pdf/{id}', 'PaymentPurchaseReturnsController@payment_return');

                //------------------------------- SEND SMS --------------------------\\
                Route::post('sales_send_sms', 'SalesController@Send_SMS');
                Route::post('purchase_send_sms', 'PurchasesController@Send_SMS');
                Route::post('quotation_send_sms', 'QuotationsController@Send_SMS');
                Route::post('purchase_payment_send_sms', 'PaymentPurchasesController@Send_SMS');
                Route::post('sell_payment_send_sms', 'PaymentSalesController@Send_SMS');
                Route::post('sell_return_payment_send_sms', 'PaymentSaleReturnsController@Send_SMS');

                //------------------------------- Settings --------------------------\\
                //----------------------------------------------------------------\\

                Route::prefix('settings')->group(function () {
                    Route::resource('system_settings', 'SettingController');
                    Route::resource('currency', 'CurrencyController');
                    Route::resource('backup', 'BackupController');
                    Route::resource('email_settings', 'EmailSettingController');
                    Route::get('pos_settings', 'SettingController@get_pos_Settings');
                    Route::put('pos_settings/{id}', 'SettingController@update_pos_settings');

                     //------------------------------- SMS Settings ------------------------\\


                     Route::get('sms_settings', 'Sms_SettingsController@get_sms_config');
                     Route::put('update_Default_SMS', 'Sms_SettingsController@update_Default_SMS');
                     Route::post('update_twilio_config', 'Sms_SettingsController@update_twilio_config');
                     Route::post('update_nexmo_config', 'Sms_SettingsController@update_nexmo_config');
                     Route::post('update_infobip_config', 'Sms_SettingsController@update_infobip_config');

                     // notifications_template
                     Route::get('sms_template', 'Notifications_Template@get_sms_template');
                     Route::put('update_sms_body', 'Notifications_Template@update_sms_body');

                     Route::get('emails_template', 'Notifications_Template@get_emails_template');
                     Route::put('update_custom_email', 'Notifications_Template@update_custom_email');

                    // update_backup_settings
                     Route::post('update_backup_settings', 'SettingController@update_backup_settings');

                });

                Route::get('GenerateBackup', 'BackupController@GenerateBackup');

                //------------------------------- clear_cache --------------------------\\

                Route::get("clear_cache", "SettingController@Clear_Cache");



            //------------------------------- Accounting -----------------------\\
            //----------------------------------------------------------------\\
            Route::prefix('accounting')->group(function () {
                Route::resource('account', 'AccountController');
                Route::resource('deposit', 'DepositController');
                Route::resource('expense', 'ExpenseController');
                Route::resource('expense_category', 'ExpenseCategoryController');
                Route::resource('deposit_category', 'DepositCategoryController');
                Route::resource('payment_methods', 'PaymentMethodController');

                Route::post("account/delete/by_selection", "AccountController@delete_by_selection");
                Route::post("deposit/delete/by_selection", "DepositController@delete_by_selection");
                Route::post("expense/delete/by_selection", "ExpenseController@delete_by_selection");
                Route::post("expense_category/delete/by_selection", "ExpenseCategoryController@delete_by_selection");
                Route::post("deposit_category/delete/by_selection", "DepositCategoryController@delete_by_selection");
                Route::post("payment_methods/delete/by_selection", "PaymentMethodController@delete_by_selection");
            });

        });


          //------------------------------- url_invoice_sms--------------------------\\
          Route::get('sell_url/{id}', 'SalesController@Sale_PDF');
          Route::get('purchase_url/{id}', 'PurchasesController@Purchase_pdf');
          Route::get('quotation_url/{id}', 'QuotationsController@Quotation_pdf');


    });

        } else {

            Route::get('/{vue?}',
            function () {
                    return redirect('/setup');
            })->where('vue', '^(?!setup).*$');


            Route::get('/setup', [
                'uses' => 'SetupController@viewCheck',
            ])->name('setup');

            Route::get('/setup/step-1', [
                'uses' => 'SetupController@viewStep1',
            ]);

            Route::post('/setup/step-2', [
                'as' => 'setupStep1', 'uses' => 'SetupController@setupStep1',
            ]);

            Route::post('/setup/testDB', [
                'as' => 'testDB', 'uses' => 'TestDbController@testDB',
            ]);

            Route::get('/setup/step-2', [
                'uses' => 'SetupController@viewStep2',
            ]);

            Route::get('/setup/step-3', [
                'uses' => 'SetupController@viewStep3',
            ]);

            Route::get('/setup/finish', function () {

                return view('setup.finishedSetup');
            });

            Route::get('/setup/getNewAppKey', [
                'as' => 'getNewAppKey', 'uses' => 'SetupController@getNewAppKey',
            ]);

            Route::get('/setup/getPassport', [
                'as' => 'getPassport', 'uses' => 'SetupController@getPassport',
            ]);

            Route::get('/setup/getMegrate', [
                'as' => 'getMegrate', 'uses' => 'SetupController@getMegrate',
            ]);

            Route::post('/setup/step-3', [
                'as' => 'setupStep2', 'uses' => 'SetupController@setupStep2',
            ]);

            Route::post('/setup/step-4', [
                'as' => 'setupStep3', 'uses' => 'SetupController@setupStep3',
            ]);

            Route::post('/setup/step-5', [
                'as' => 'setupStep4', 'uses' => 'SetupController@setupStep4',
            ]);

            Route::post('/setup/lastStep', [
                'as' => 'lastStep', 'uses' => 'SetupController@lastStep',
            ]);

            Route::get('setup/lastStep', function () {
                return redirect('/setup', 301);
            });

    }





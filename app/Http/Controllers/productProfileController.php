<?php

namespace App\Http\Controllers;

use App\Models\productProfile;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class productProfileController extends Controller
{
    public function index()
    {
        $user_auth = auth()->user();
        if ($user_auth->can('products_view')){
            $product_profiles = productProfile::all();
            return view('product_profiles.list_product_profile', compact('product_profiles'));

        }
        return abort('403', __('You are not authorized'));
    }

    public function store(Request $request)
    {
        //CREATE FOLDER
        if(!file_exists(public_path('storage/products/profile')))
        {
            //CREATEING UPLOAD FILE IF NOT EXISTS
            Storage::disk('public')->makeDirectory('products/profile');
        }

        //UPLOAD IMAGE
        //GENERATING FILE NAME
        $uploadedFile = $request->file('image');
        $fileType = strtolower($uploadedFile->getClientOriginalExtension());
        $rand_1 = rand(1000000000,9999999999);
        $rand_2 = rand(1000000000,9999999999);
        $rand_3 = rand(1000000000,9999999999);
        $finalRand = $rand_1.$rand_2.$rand_3;
        $finalRand = str_shuffle($finalRand);
        $fileName = str_shuffle(substr($finalRand, -10)).'.'.$fileType;

        $request->file('image')->move(
            public_path('storage/products/profile'), $fileName
        );

        $data_profile_image = url('storage/products/profile/'.$fileName);

        $profile = new productProfile();
        $profile->name = $request->name;
        $profile->image = $data_profile_image;
        $profile->save();

        return redirect()->route('product.profile.index')->with('successMessage', 'Product profile successfully created!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\productProfile;
use Cloudinary\Api\Upload\UploadApi;
use Illuminate\Http\Request;

class productProfileController extends Controller
{
    public function index()
    {
        $user_auth = auth()->user();
        if ($user_auth->can('products_view')){
            $product_profiles = productProfile::paginate(10);
            return view('product_profiles.list_product_profile', compact('product_profiles'));

        }
        return abort('403', __('You are not authorized'));
    }

    public function store(Request $request)
    {
        $result = (new UploadApi())->upload($request->file('image')->getRealPath(), [
            'folder' => 'products/profile/',
            'resource_type' => 'image']);
        $json =  json_encode($result);
        $data = json_decode($json);

        $profile = new productProfile();
        $profile->name = $request->name;
        $profile->image = $data->secure_url;
        $profile->save();

        return redirect()->route('product.profile.index')->with('successMessage', 'Product profile successfully created!');
    }
}

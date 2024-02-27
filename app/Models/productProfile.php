<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class productProfile extends Model
{
    use HasFactory;
    protected $table = 'product_profiles';
    protected $fillable = [
        'id','name', 'image'
    ];
}

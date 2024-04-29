<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable=
    [
        'file_name',
        'file_type',
        'fileable_id',
        'fileable_type',
        'delete_status'
    ];
    /**
     * Get the owning fileable model.
     */
    public function fileable()
    {
        return $this->morphTo();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandModal extends Model
{
    use HasFactory;

    protected $table = 'brand_modals';
    protected $fillable = ['brand_id','brand_modal'];
}

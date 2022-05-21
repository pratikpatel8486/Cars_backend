<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandVariant extends Model
{
    use HasFactory;

    protected $table = 'brand_variants';
    protected $fillable = ['brand_id','model_id','brand_variant'];
}

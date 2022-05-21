<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;
    protected $table = 'cars';
    protected $fillable = ['brand','modal','variant','make_year','reg_year','fuel_type','ownership','kms','rto','transmission','insurance','insurance_date','color','price','body_type'];  
}

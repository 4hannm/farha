<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscribeBenefit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
      'name',
      'subscribe_package_id',  
    ];
}


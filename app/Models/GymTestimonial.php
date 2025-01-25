<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GymTestimonial extends Model
{
    use HasFactory,SoftDeletes;
    
    protected $fillable = [
      'name',  
      'occopation',  
      'message',  
      'photo',  
      'gym_id',  
    ];

    public function gym(): BelongsTo
    {
        return $this->belongsTo(gym::class, 'gym_id');
    }
}

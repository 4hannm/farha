<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscribeTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $filable=[
        'booking_trx_id',
        'name',
        'phone',
        'email',
        'proof',
        'total_amount',
        'duration',
        'is_paid',
        'starter_at',
        'ended_at',
        'subscribe_package_id',
    ];

    protected $cast=[
        'starter_at' =>'date',
        'ended_at' =>'date'
    ];

    public function subscibePackage(): BelongsTo
    {
        return $this->belongsTo(SubscribePackage::class, 'subscribe_package_id');
    }

    #untuk generate transaksi id

    public static function generateUniqueTrxId()
    {
        $prefix = 'FITBWA';
        do{
            $randomString = $prefix . mt_rand(1000,9999);
        } while (self::where('booking_trx_id', $randomString)->exists());

        return $randomString;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTracking extends Model
{
    use HasFactory;

    protected $table = 'order_tracking';
    protected $fillable = [
        'order_id',
        'tracking_id',
        'type'
    ];

    public function Order()
    {
        return $this->belongsTo(Order::class);
    }
}

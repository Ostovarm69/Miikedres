<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMapping extends Model
{
    use HasFactory;

    protected $table = 'products_mapping';

    protected $fillable = [
        'target_product_id',
        'source_product_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'target_product_id');
    }
}

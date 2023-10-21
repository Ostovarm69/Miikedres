<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSizesGuide extends Model
{
    use HasFactory;

    protected $table = 'product_sizes_guide';

    protected $fillable = [
        'product_id',
        'sizes'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

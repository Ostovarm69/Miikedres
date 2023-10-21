<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryMapping extends Model
{
    use HasFactory;

    protected $table = 'categories_mapping';

    protected $fillable = [
        'target_category_id',
        'source_category_id',
    ];
}

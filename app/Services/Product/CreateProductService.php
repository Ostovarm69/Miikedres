<?php

namespace App\Services\Product;

use App\Models\Product;
use Morilog\Jalali\Jalalian;

class CreateProductService
{

    public function create(array $attributes)
    {
        $product = Product::updateOrCreate([
            'slug'               => $attributes['barcode'],
        ],[
            'title'              => $attributes['title'],
            'title_en'           => $attributes['title_en'],
            'category_id'        => $attributes['category_id'],
            'spec_type_id'       => $attributes['spec_type_id'],
            'size_type_id'       => $attributes['size_type_id'],
            'weight'             => rand(100, 300),
            'unit'               => $attributes['unit'],
            'price_type'         => "multiple-price",
            'type'               => 'physical',
            'description'        => '',
            'short_description'  => '',
            'special'            => false,
            'meta_title'         => $attributes['title'],
            'image_alt'          => $attributes['title'],
            'meta_description'   => $attributes['title'],
            'published'          => 1,
            'publish_date'       => null,
            'currency_id'        => null,
            'rounding_amount'    => 'default',
            'rounding_type'      => 'default',
            'lang'               => 'fa',
        ]);

        return $product;
    }
}

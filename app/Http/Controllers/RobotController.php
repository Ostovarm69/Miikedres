<?php

namespace App\Http\Controllers;

use App\Models\AttributeGroup;
use App\Models\Category;
use App\Models\CategoryMapping;
use App\Models\Label;
use App\Models\Product;
use App\Models\ProductMapping;
use App\Models\ProductSizesGuide;
use App\Models\Specification;
use App\Models\SpecificationGroup;
use App\Models\SpecType;
use App\Services\Havinmode\CategoryService;
use App\Services\Havinmode\ProductService;
use App\Services\Product\CreateProductService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Morilog\Jalali\Jalalian;

class RobotController extends Controller
{
    //
    public function categoriesMapping(CategoryService $categoryService)
    {
        $this->authorize('robot.category-mapping');

        $localCategories = $this->getLocalCategories();
        $localCategoriesPluck = collect($localCategories)->pluck('title', 'id')->toArray();
        $mappedCategories = CategoryMapping::all()->pluck('target_category_id', 'source_category_id')->toArray();

        $havinmodeCategories = $this->getHavinmodeCategories($categoryService);

        return view('back.robot.categories-mapping',
            compact(
                'havinmodeCategories',
                'localCategories',
                'localCategoriesPluck',
                'mappedCategories'
            ));
    }

    public function categoriesMappingStore(Request $request)
    {
        $this->authorize('robot.category-mapping');

        $request->validate([
            'source_category_id' => 'required|array',
            'target_category_id' => 'required|array',
        ]);

        collect($request->source_category_id)->each(function ($category, $index) use ($request) {
            if ($request->target_category_id[$index] !== null) {
                CategoryMapping::updateOrCreate([
                    'source_category_id' => $category
                ], [
                    'target_category_id' => $request->target_category_id[$index]
                ]);
            }
        });

        return redirect()->route('admin.robot.categories-mapping');
    }

    public function setting()
    {
        $this->authorize('robot.update-setting');

        $currentPages = Cache::get('havinmod_current_page', 0);
        $count = 0;
        if ($currentPages){
            $pages = Cache::get('havinmod_pages', 0);

            $count = ($pages - $currentPages) * 50;
        }

        $periodOption = option('robot-update-period', 30);

        $captions = json_decode(
            option('robot-captions', ''),
            true
        );

        $localCategories = $this->getLocalCategories();
        $localCategories = collect($localCategories)->pluck('title', 'id')->toArray();

        return view('back.robot.setting',
            compact(
                'periodOption',
                'count',
                'localCategories',
                'captions'
            ));
    }

    public function storeCaptions(Request $request)
    {
        $this->authorize('robot.update-setting');

        $captions = $request->except('_token');
        option_update('robot-captions', json_encode($captions));

        return redirect()->route('admin.robot.setting')->with([
            'captionsMessage' => "اطلاعات کپشن ها با موفقیت بروزرسانی شد."
        ]);
    }

    public function liveUpdate()
    {
        $this->authorize('robot.update-setting');

        $cache = Cache::has('product-index');
        if ($cache){
            Cache::forget('product-index');
        }

        return redirect()->route('admin.robot.setting');
    }

    public function updatePeriod(Request $request)
    {
        $this->authorize('robot.update-setting');

        $request->validate([
            'period' => 'int|min:5'
        ]);
        option_update('robot-update-period', $request->period);

        return redirect()->route('admin.robot.setting')->with([
            'periodUpdateMessage' => "فاصله زمانی بروزرسانی به {$request->period} دقیقه بروزرسانی شد."
        ]);
    }

    public function updateSingleProduct(Request $request, ProductService $productService, CreateProductService $createProductService)
    {
        $this->authorize('robot.update-setting');
        $products = Cache::remember('product_temp_cache_list', 600, function () use ($productService) {
            return $productService->index();
        });
        $singleProduct = collect($products['data']['products']['data'])->where('barcode', $request->code)->first();
        $productDetails = $productService->getDetails($singleProduct['id']);

        $mappedCategories = CategoryMapping::all()
            ->pluck('target_category_id', 'source_category_id')
            ->toArray();

        $systemCategories = Category::all();

        $item = $productDetails['data']['product'];

        $textCrawl = explode(PHP_EOL, strip_tags($productDetails['data']['product']['description']));
        $specifications = [];
        $i = 0;
        foreach ($textCrawl as $itemText){
            $itemText = trim($itemText, ' \t\n\r\0\x0B &nbsp;');
            if ($itemText !== ''){
                $textExploded = explode(':', $itemText);
                if (trim($textExploded[0], ' \t\n\r\0\x0B &nbsp;') !== 'کدمحصول' && isset($textExploded[1])){
                    $specifications[$i]['name'] = trim($textExploded[0], ' \t\n\r\0\x0B &nbsp;');
                    $specifications[$i]['value'] = trim($textExploded[1], ' \t\n\r\0\x0B &nbsp;');
                    $i++;
                }
            }
        }

        $sizes = collect($productDetails['data']['product']['size_charts'])
            ->map(function ($sizeItem) {
                return [
                    'title' => $sizeItem['title'],
                    'chart' => $sizeItem['chart'],
                ];
            })->toArray();

        $sourceCategory = collect($productDetails['data']['product']['categories']);
        if ($sourceCategory->count() === 1){
            $category = $sourceCategory->first();
        } else {
            $category = $sourceCategory->where('parent_id', '<>', null)->first();
            if (!$category){
                $category = $sourceCategory->sortByDesc('priority')
                    ->first();
            }
        }

        if (isset($mappedCategories[$category['id']])){
            $mainCategory = $systemCategories->where('id', $mappedCategories[$category['id']])->first();

            $item['barcode'] = empty($item['barcode']) ? Str::slug($item['title']) : $item['barcode'];

            $create = $createProductService->create([
                'title' => trim($item['title'], ' \t\n\r\0\x0B &nbsp;'),
                'title_en' => null,
                'category_id' => $mappedCategories[$category['id']],
                'barcode' => $item['barcode'],
                'size_type_id' => null,
                'unit' => 'تعداد',
                'spec_type_id' => $this->spec_type($mainCategory->title, $specifications)
            ]);
            $create->categories()->sync([$create->category_id]);
            $this->updateProductLabels($create, $mainCategory->title);
            $tags = addTags($item['title']);
            $create->tags()->sync($tags);
            if (!empty($sizes)){
                ProductSizesGuide::updateOrCreate([
                    'product_id' => $create->id,
                ], [
                    'sizes' => json_encode($sizes)
                ]);
            }

            $pricesArray = $this->getPriceAttributes($productDetails['data']['product']['varieties']);
            $this->updateProductPrices($create, $pricesArray);

            $tmpImages = collect($productDetails['data']['product']['images'])->map(function ($imageUrl, $key) use ($create) {
                if ($key == 0) {
                    $this->setMainProductImage($create, $imageUrl['url']);
                }

                return $this->storeImage($imageUrl['url']);
            });
            if ($tmpImages->isNotEmpty()) {
                $tmpImages = implode(',', $tmpImages->toArray());
                $this->updateProductImages($create, $tmpImages);
            }

            $specificationsGroup = [
                [
                    'name' => 'اطلاعات محصول',
                    'specifications' => $specifications
                ]
            ];
            $this->updateProductSpecifications($create, $specificationsGroup);

            ProductMapping::updateOrCreate([
                'target_product_id' => $create->id,
            ], [
                'source_product_id'=> $item['id'],
            ]);
        }

        return redirect()->route('admin.robot.setting')->with([
            'singleUpdateMessage' => "اطلاعات محصول به کد {$request->code} با موفقیت بروزرسانی شد."
        ]);
    }

    public function updateSingleProductById(Request $request, ProductService $productService, CreateProductService $createProductService)
    {
        $this->authorize('robot.update-setting');

        $productDetails = $productService->getDetails($request->code);

        $mappedCategories = CategoryMapping::all()
            ->pluck('target_category_id', 'source_category_id')
            ->toArray();

        $systemCategories = Category::all();

        $item = $productDetails['data']['product'];

        $textCrawl = explode(PHP_EOL, strip_tags($productDetails['data']['product']['description']));
        $specifications = [];
        $i = 0;
        foreach ($textCrawl as $itemText){
            $itemText = trim($itemText, ' \t\n\r\0\x0B &nbsp;');
            if ($itemText !== ''){
                $textExploded = explode(':', $itemText);
                if (trim($textExploded[0], ' \t\n\r\0\x0B &nbsp;') !== 'کدمحصول' && isset($textExploded[1])){
                    $specifications[$i]['name'] = trim($textExploded[0], ' \t\n\r\0\x0B &nbsp;');
                    $specifications[$i]['value'] = trim($textExploded[1], ' \t\n\r\0\x0B &nbsp;');
                    $i++;
                }
            }
        }

        $sizes = collect($productDetails['data']['product']['size_charts'])
            ->map(function ($sizeItem) {
                return [
                    'title' => $sizeItem['title'],
                    'chart' => $sizeItem['chart'],
                ];
            })->toArray();

        $sourceCategory = collect($productDetails['data']['product']['categories']);
        if ($sourceCategory->count() === 1){
            $category = $sourceCategory->first();
        } else {
            $category = $sourceCategory->where('parent_id', '<>', null)->first();
            if (!$category){
                $category = $sourceCategory->sortByDesc('priority')
                    ->first();
            }
        }

        if (isset($mappedCategories[$category['id']])){
            $mainCategory = $systemCategories->where('id', $mappedCategories[$category['id']])->first();

            $item['barcode'] = empty($item['barcode']) ? Str::slug($item['title']) : $item['barcode'];

            $create = $createProductService->create([
                'title' => trim($item['title'], ' \t\n\r\0\x0B &nbsp;'),
                'title_en' => null,
                'category_id' => $mappedCategories[$category['id']],
                'barcode' => $item['barcode'],
                'size_type_id' => null,
                'unit' => 'تعداد',
                'spec_type_id' => $this->spec_type($mainCategory->title, $specifications)
            ]);
            $create->categories()->sync([$create->category_id]);
            $this->updateProductLabels($create, $mainCategory->title);
            $tags = addTags($item['title']);
            $create->tags()->sync($tags);
            if (!empty($sizes)){
                ProductSizesGuide::updateOrCreate([
                    'product_id' => $create->id,
                ], [
                    'sizes' => json_encode($sizes)
                ]);
            }

            $pricesArray = $this->getPriceAttributes($productDetails['data']['product']['varieties']);
            $this->updateProductPrices($create, $pricesArray);

            $tmpImages = collect($productDetails['data']['product']['images'])->map(function ($imageUrl, $key) use ($create) {
                if ($key == 0) {
                    $this->setMainProductImage($create, $imageUrl['url']);
                }

                return $this->storeImage($imageUrl['url']);
            });
            if ($tmpImages->isNotEmpty()) {
                $tmpImages = implode(',', $tmpImages->toArray());
                $this->updateProductImages($create, $tmpImages);
            }

            $specificationsGroup = [
                [
                    'name' => 'اطلاعات محصول',
                    'specifications' => $specifications
                ]
            ];
            $this->updateProductSpecifications($create, $specificationsGroup);

            ProductMapping::updateOrCreate([
                'target_product_id' => $create->id,
            ], [
                'source_product_id'=> $item['id'],
            ]);
        }

        return redirect()->route('admin.robot.setting')->with([
            'singleUpdateMessage' => "اطلاعات محصول به کد {$request->code} با موفقیت بروزرسانی شد."
        ]);
    }


    private function getPriceAttributes($varieties)
    {
        $attributeGroups = AttributeGroup::detectLang()->orderBy('ordering')->get();
        $colorAttributes = $attributeGroups->where('id', 1)->first();
        $sizeAttributes = $attributeGroups->where('id', 3)->first();
        $typeAttributes = $attributeGroups->where('id', 4)->first();
        return collect($varieties)->map(function ($item) use ($colorAttributes, $sizeAttributes, $typeAttributes) {

            $colorId = null;
            if (!empty($item['color']['name'])){
                $colorId = ($colorAttributes->get_attributes()->firstOrCreate([
                    'name' => $item['color']['name'],
                    'value' => $item['color']['code']
                ]))->id;
            }

            $sizeId = null;
            $tarhId = null;
            if (!empty($item['attributes'])){
                if ($item['attributes'][0]['name'] === 'tarh'){
                    $tarhId = ($typeAttributes->get_attributes()->firstOrCreate([
                        'name' => $item['attributes'][0]['pivot']['value']
                    ]))->id;
                }
                if ($item['attributes'][0]['name'] === 'size'){
                    $sizeId = ($sizeAttributes->get_attributes()->firstOrCreate([
                        'name' => $item['attributes'][0]['pivot']['value']
                    ]))->id;
                }
            }

            return [
                'attributes' => [
                    $colorId,
                    $sizeId,
                    $tarhId
                ],
                'price' => $this->getPriceCalculate($item['final_price']['amount']),
                'discount' => null,
                'cart_max' => null,
                'cart_min' => null,
                'stock' => $item['quantity'],
            ];
        })->toArray();
    }

    private function getPriceCalculate($price)
    {
        $secondPart = substr($price, 1);
        $firstPart = substr($price, 0, 1);

        return (int)(($firstPart * 2) . $secondPart);
    }

    private function updateProductLabels(Product $product, $labels)
    {
        $label_ids = [];

        $labels = explode(',', $labels);

        foreach ($labels as $item) {
            $label = Label::firstOrCreate([
                'title' => trim($item, ' \t\n\r\0\x0B&nbsp;'),
                'lang' => 'fa',
            ]);

            $label_ids[] = $label->id;
        }

        $product->labels()->sync($label_ids);
    }

    private function updateProductPrices(Product $product, $pricesArray)
    {
        if ($product->isDownload()) {
            return;
        }

        $prices_id = [];

        foreach ($pricesArray as $price) {

            $time = null;
            if (isset($price['discount_expire']) && $price['discount_expire']) {
                $time = Carbon::instance(Jalalian::fromFormat('Y-m-d H:i:s', $price['discount_expire'])->toCarbon())->toDateTimeString() ?? null;
            }
            $attributes = array_filter($price['attributes'] ?? []);

            $update_price = false;

            foreach ($product->prices()->withTrashed()->get() as $product_price) {
                $product_price_attributes = $product_price->get_attributes()->get()->pluck('id')->toArray();

                sort($attributes);
                sort($product_price_attributes);

                if ($attributes == $product_price_attributes) {
                    $update_price = $product_price;
                    break 1;
                }
            }

            if ($update_price) {

                $update_price->createChange(
                    $price["price"],
                    $price["discount"]
                );

                $update_price->update([
                    "price"              => $price["price"],
                    "discount"           => $price["discount"],
                    "discount_price"     => get_discount_price($price["price"], $price["discount"], $product),
                    "regular_price"      => $price["price"],
                    "stock"              => $price["stock"],
                    "cart_max"           => $price["cart_max"],
                    "cart_min"           => $price["cart_min"],
                    "discount_expire_at" => $price["discount"] ?  $time : null,
                    "deleted_at"         => null,
                ]);

                $update_price->get_attributes()->sync($attributes);

                $prices_id[] = $update_price->id;
            } else {

                $insert_price = $product->prices()->create(
                    [
                        "price"              => $price["price"],
                        "discount"           => $price["discount"],
                        "discount_price"     => get_discount_price($price["price"], $price["discount"], $product),
                        "regular_price"      => $price["price"],
                        "stock"              => $price["stock"],
                        "cart_max"           => $price["cart_max"],
                        "cart_min"           => $price["cart_min"],
                        "discount_expire_at" => $price["discount"] ? $time : null,
                    ]
                );

                foreach ($attributes as $attribute) {
                    $insert_price->get_attributes()->attach([$attribute]);
                }

                $insert_price->createChange($price["price"], $price["discount"]);

                $insert_price->createChange(
                    $price["price"],
                    $price["discount"],
                    $price["stock"]
                );

                $prices_id[] = $insert_price->id;
            }
        }

        $product->prices()->whereNotIn('id', $prices_id)->delete();

        DB::table('cart_product')
            ->where('product_id', $product->id)
            ->whereNotNull('price_id')
            ->whereNotIn('price_id', $prices_id)
            ->delete();
    }

    private function storeImage(string  $imageUrl, $imageName = null, $path = 'tmp')
    {
        $contents = file_get_contents($imageUrl);

        if ($imageName === null){
            $currentDate = Carbon::now()->toDateString();

            $name = substr($imageUrl, strrpos($imageUrl, '/') + 1);
            $extension = pathinfo($name, \PATHINFO_EXTENSION);
            $imageName = 'img' . '-' . $currentDate . '-' . uniqid() . '.' . $extension;
        }

        Storage::put("{$path}/". $imageName, $contents);

        return $imageName;
    }

    private function updateProductImages(Product $product, $tmpImages)
    {

        $product_images = $product->gallery()->pluck('image')->toArray();
        $images         = explode(',', $tmpImages);
        $deleted_images = array_diff($product_images, $images);

        foreach ($deleted_images as $del_img) {
            $del_img = $product->gallery()->where('image', $del_img)->first();

            if (!$del_img) {
                continue;
            }

            if (Storage::disk('public')->exists($del_img->image)) {
                Storage::disk('public')->delete($del_img->image);
            }

            $del_img->delete();
        }

        $ordering = 1;

        foreach ($images as $image) {

            if (Storage::exists('tmp/' . $image)) {

                Storage::move('tmp/' . $image, 'products/' . $image);

                $product->gallery()->create([
                    'image' => '/uploads/products/' . $image,
                    'ordering' => $ordering++,
                ]);
            } else {
                $product->gallery()->where('image', $image)->update([
                    'ordering' => $ordering++,
                ]);
            }
        }
    }

    private function setMainProductImage(Product $product, $imageUrl)
    {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $name = substr($imageUrl, strrpos($imageUrl, '/') + 1);
        $extension = pathinfo($name, \PATHINFO_EXTENSION);

        $name = uniqid() . '_' . $product->id . '.' . $extension;
        $this->storeImage($imageUrl, $name, 'products');

        $product->image = '/uploads/products/' . $name;
        $product->save();
    }

    private function spec_type($spec_type, $specification_group)
    {

        $spec_type = SpecType::firstOrCreate([
            'name' => $spec_type,
            'lang' => 'fa'
        ]);

        $group_ordering = 0;

        foreach ($specification_group as $group) {

            if (!isset($group['specifications'])) {
                continue;
            }

            $spec_group = SpecificationGroup::firstOrCreate([
                'name' => $group['name'],
                'lang' => app()->getLocale()
            ]);

            $specification_ordering = 0;

            foreach ($group['specifications'] as $specification) {
                $spec = Specification::firstOrCreate([
                    'name' => $specification['name']
                ]);

                if (!$spec_type->specifications()->where('specification_id', $spec->id)->where('specification_group_id', $spec_group->id)->first()) {
                    $spec_type->specifications()->attach([
                        $spec->id => [
                            'specification_group_id' => $spec_group->id,
                            'group_ordering'         => $group_ordering,
                            'specification_ordering' => $specification_ordering++,
                        ]
                    ]);
                }
            }

            $group_ordering++;
        }

        return $spec_type->id;
    }

    private function updateProductSpecifications(Product $product, $specificationGroup)
    {
        $product->specifications()->detach();
        $group_ordering = 0;

        if ($specificationGroup) {
            foreach ($specificationGroup as $group) {

                if (!isset($group['specifications'])) {
                    continue;
                }

                $spec_group = SpecificationGroup::firstOrCreate([
                    'name' => $group['name'],
                ]);

                $specification_ordering = 0;

                foreach ($group['specifications'] as $specification) {
                    $spec = Specification::firstOrCreate([
                        'name' => $specification['name']
                    ]);

                    $product->specifications()->attach([
                        $spec->id => [
                            'specification_group_id' => $spec_group->id,
                            'group_ordering'         => $group_ordering,
                            'specification_ordering' => $specification_ordering++,
                            'value'                  => $specification['value'],
                            'special'                => isset($specification['special']) ? true : false
                        ]
                    ]);
                }

                $group_ordering++;
            }
        }
    }

    /**
     * @param CategoryService $categoryService
     * @return array
     */
    private function getHavinmodeCategories(CategoryService $categoryService): array
    {
        $categories = $categoryService->get();
        $categoriesArray = [];
        collect($categories)->each(function ($category) use (&$categoriesArray) {
            $categoriesArray[] = [
                'id' => $category['id'],
                'title' => $category['title'],
                'parent' => null
            ];
            if (!empty($category['children'])) {
                collect($category['children'])->each(function ($subItem) use ($category, &$categoriesArray) {
                    $categoriesArray[] = [
                        'id' => $subItem['id'],
                        'title' => $subItem['title'],
                        'parent' => $category['title']
                    ];
                });
            }
        });

        return $categoriesArray;
    }

    private function getLocalCategories()
    {
        $categories = Category::all();
        $categoriesArray = [];
        $categories->each(function ($category) use (&$categoriesArray) {
            $categoriesArray[] = [
                'id' => $category->id,
                'title' => $category->title,
                'parent' => $category->category_id
            ];
        });

        return $categoriesArray;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductMapping;
use App\Services\Havinmode\ContentService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;

class GetProductContents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:content';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'this command update products contents';
    /**
     * @var ContentService
     */
    private $contetService;
    /**
     * @var Crawler
     */
    private $crawler;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ContentService $contentService, Crawler $crawler)
    {
        parent::__construct();
        $this->contetService = $contentService;
        $this->crawler = $crawler;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $products = ProductMapping::whereNull('content_updated_at')
            ->limit(10)
            ->get()
            ->sortByDesc('id');

        $products->each(function (ProductMapping $product) {
            $singleProduct = Product::find($product->target_product_id);
            if ($singleProduct){
                $slug = $singleProduct->slug;
                $html = $this->contetService->get($slug);
                $this->crawler->clear();
                $this->crawler->addContent($html);
                $accordions = $this->crawler->filter('div.accordion');
                $accordions->each(function (Crawler $accordion) use ($singleProduct) {

                    $title = $accordion->filter('button.accordion-button')->text();
                    $images = $accordion->filter('div.accordion-body .feature .card img');

                    $images->each(function (Crawler $image, $i) use ($singleProduct, $title) {
                        $src = $image->attr('src');

                        $slugTitle = $this->makeSlug($singleProduct->title . ' ' . $title);
                        $j = $i + 1;
                        $filename = "{$slugTitle}-{$j}.{$this->getFileExtension($src)}";

                        $this->storeImage(
                            $src,
                            $filename,
                            "product-contents/{$singleProduct->slug}/$title"
                        );

                    });
                });
                $product->content_updated_at = Carbon::now();
                $product->save();
            }
        });
    }

    private function storeImage(string $imageUrl, $imageName = null, $path = 'tmp')
    {
        try {
            $contents = file_get_contents($imageUrl);

            if ($imageName === null) {
                $currentDate = Carbon::now()->toDateString();

                $name = substr($imageUrl, strrpos($imageUrl, '/') + 1);
                $extension = pathinfo($name, \PATHINFO_EXTENSION);
                $imageName = 'img' . '-' . $currentDate . '-' . uniqid() . '.' . strtolower($extension);
            }

            $store = Storage::put("{$path}/" . $imageName, $contents);

            return $imageName;
        } catch (Exception $exception){

        }
    }

    private function getFileExtension($file)
    {
        $name = substr($file, strrpos($file, '/') + 1);

        return strtolower(pathinfo($name, \PATHINFO_EXTENSION));
    }

    private function makeSlug($string, $separator = '-')
    {
        $string = trim($string);
        $string = mb_strtolower($string, 'UTF-8');
        $string = preg_replace("/[^a-z0-9_\-\sءاآؤئبپتثجچحخدذرزژسشصضطظعغفقكکگلمنوهی]/u", '', $string);
        $string = preg_replace("/[\s\-_]+/", ' ', $string);
        $string = preg_replace("/[\s_]/", $separator, $string);

        return $string;
    }
}

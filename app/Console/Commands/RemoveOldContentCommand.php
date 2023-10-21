<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class RemoveOldContentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:remove-old-contents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will remove old product contents media';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $products = Product::orderBy('id', 'DESC')->take(99999999)->skip(10)
            ->get();

        $products->each(function ($product){
            if ($product->slug && $product->source) {
                $this->deleteDirectory(
                    __DIR__ . '/../../../public/uploads/product-contents/' . $product->slug
                );

                echo $product->slug . PHP_EOL;
            }
        });
    }

    private function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return rmdir($dir);
    }
}

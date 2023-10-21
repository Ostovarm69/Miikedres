<?php

namespace App\Providers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Filter;
use App\Observers\ProductObserver;
use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use App\Models\Slider;
use App\Models\User;
use App\Observers\BannerObserver;
use App\Observers\CategoryObserver;
use App\Observers\PostObserver;
use App\Observers\SliderObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use RachidLaasri\LaravelInstaller\Helpers\EnvironmentManager;
use Illuminate\Support\Facades\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        $this->loadTheme();

        if (application_installed()) {

            if (!$this->app->runningInConsole()) {
                $this->viewComposer();
                $this->configs();
            }
        } else if (!application_installed() && !$this->app->runningInConsole()) {
            if (!file_exists(base_path('.env'))) {
                copy(base_path('.env.example'), base_path('.env'));
            }

            $this->app->bind(EnvironmentManager::class, MyEnvironmentManager::class);

            if (request()->segment(1) != 'install') {
                return redirect('install')->send();
            }
        }

        $this->observers();
        
    }

 


    private function viewComposer()
    {
        // SHARE WITH SPECIFIC VIEW

        view()->composer('*', function ($view) {
            $current_local = local_info();

            $view->with('current_local', $current_local);
        });

        view()->composer(['back.partials.notifications', 'back.partials.sidebar'], function ($view) {

            $notifications = auth()->user()->unreadNotifications;

            $view->with('notifications', $notifications);
        });

        view()->composer(['back.products.partials.filters', 'back.products.partials.index-filters'], function ($view) {

            $categories = Category::detectLang()->where('type', 'productcat')->orderBy('ordering')->get();

            $view->with('categories', $categories);
        });

        view()->composer(['back.products.categories.edit'], function ($view) {

            $filters = Filter::latest()->get();

            $view->with('filters', $filters);
        });

        view()->composer(['back.menus.index', 'back.sliders.create', 'back.sliders.edit', 'back.banners.create', 'back.banners.edit', 'back.links.create', 'back.links.edit'], function ($view) {

            $pages = Page::detectLang()->pluck('slug');

            $view->with('pages', $pages);
        });
    }

    private function configs()
    {
        //
    }

    private function observers()
    {
        Product::observe(ProductObserver::class);
        Slider::observe(SliderObserver::class);
        Banner::observe(BannerObserver::class);
        Post::observe(PostObserver::class);
        Category::observe(CategoryObserver::class);
    }

    public static function loadTheme()
    {
        // register theme service provider

        $theme = get_current_theme();

        if ($theme && class_exists($theme['service_provider'])) {
            app()->register($theme['service_provider']);
        }
    }
}


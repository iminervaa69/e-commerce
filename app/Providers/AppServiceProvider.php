<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Services\CartService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CartService::class, function ($app) {
            return new CartService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('twMerge', function ($expression) {
            return "<?php echo \App\Helpers\TWHelper::merge($expression); ?>";
        });
    }
}

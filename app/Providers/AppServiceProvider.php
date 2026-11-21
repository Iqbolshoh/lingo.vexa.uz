<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (!$this->app->bound('view')) {
            $this->app->singleton('view', function () {
                return new class {
                    public function make($view, $data = [], $mergeData = []) {
                        return new class($view, $data) {
                            private $view;
                            private $data;
                            public function __construct($view, $data) { $this->view = $view; $this->data = $data; }
                            public function render() { return "View: " . htmlspecialchars((string)$this->view); }
                            public function __toString() { return $this->render(); }
                        };
                    }
                    public function exists($view) { return false; }
                    public function share($key, $value = null) {}
                    public function composer($views, $callback) {}
                    public function creator($views, $callback) {}
                };
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

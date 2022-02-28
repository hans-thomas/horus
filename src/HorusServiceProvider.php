<?php

    namespace Hans\Horus;

    use Illuminate\Support\ServiceProvider;
    use Hans\Horus\Contracts\HorusContract;

    class HorusServiceProvider extends ServiceProvider {
        /**
         * Register any application services.
         *
         * @return void
         */
        public function register() {
            $this->app->singleton( HorusSeeder::class, function() {
                return new HorusSeeder();
            } );
            $this->app->singleton( HorusContract::class, function() {
                return new HorusService();
            } );
        }

        /**
         * Bootstrap any application services.
         *
         * @return void
         */
        public function boot() {
            $this->publishes( [
                __DIR__ . '/../config/config.php' => config_path( 'horus.php' ),
            ], 'horus-config' );
            $this->mergeConfigFrom( __DIR__ . '/../config/config.php', 'horus' );
            $this->loadMigrationsFrom( __DIR__ . '/../migrations' );
        }

    }

<?php

	namespace Hans\Horus;

	use Hans\Horus\Services\HorusService;
	use Hans\Horus\Services\SeederService;
	use Illuminate\Support\ServiceProvider;

	class HorusServiceProvider extends ServiceProvider {

		/**
		 * Register any application services.
		 *
		 * @return void
		 */
		public function register(): void {
			$this->app->singleton( 'seeder-service', SeederService::class );
			$this->app->singleton( 'horus-service', HorusService::class );
		}

		/**
		 * Bootstrap any application services.
		 *
		 * @return void
		 */
		public function boot(): void {
			$this->mergeConfigFrom( __DIR__ . '/../config/config.php', 'horus' );

			if ( $this->app->runningInConsole() ) {
				$this->publishes(
					[
						__DIR__ . '/../config/config.php' => config_path( 'horus.php' ),
					],
					'horus-config'
				);
				$this->loadMigrationsFrom( __DIR__ . '/../migrations' );
			}
		}

	}

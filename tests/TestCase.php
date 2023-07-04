<?php

	namespace Hans\Horus\Tests;


	use Hans\Horus\HorusServiceProvider;
	use Illuminate\Foundation\Application;
	use Illuminate\Foundation\Testing\RefreshDatabase;
	use Illuminate\Routing\Router;
	use Orchestra\Testbench\TestCase as BaseTestCase;
	use Spatie\Permission\PermissionServiceProvider;

	class TestCase extends BaseTestCase {
		use RefreshDatabase;

		protected string $separator;
		protected string $default_guard;

		/**
		 * Setup the test environment.
		 *
		 * @return void
		 */
		protected function setUp(): void {
			parent::setUp();

			$this->separator     = horus_config( 'separator' );
			$this->default_guard = config( 'auth.defaults.guard' );
		}

		/**
		 * Get application timezone.
		 *
		 * @param Application $app
		 *
		 * @return string|null
		 */
		protected function getApplicationTimezone( $app ): ?string {
			return 'UTC';
		}

		/**
		 * Get package providers.
		 *
		 * @param Application $app
		 *
		 * @return array
		 */
		protected function getPackageProviders( $app ): array {
			return [
				HorusServiceProvider::class,
				PermissionServiceProvider::class
			];
		}

		/**
		 * Override application aliases.
		 *
		 * @param Application $app
		 *
		 * @return array
		 */
		protected function getPackageAliases( $app ): array {
			return [//	'Acme' => 'Acme\Facade',
			];
		}

		/**
		 * Define environment setup.
		 *
		 * @param Application $app
		 *
		 * @return void
		 */
		protected function defineEnvironment( $app ): void {
			// Setup default database to use sqlite :memory:
			$app[ 'config' ]->set( 'database.default', 'testbench' );
			$app[ 'config' ]->set( 'database.connections.testbench', [
				'driver'   => 'sqlite',
				'database' => ':memory:',
				'prefix'   => '',
			] );
		}

		/**
		 * Define routes setup.
		 *
		 * @param Router $router
		 *
		 * @return void
		 */
		protected function defineRoutes( $router ): void {
			// Define routes.
		}

		/**
		 * Define database migrations.
		 *
		 * @return void
		 */
		protected function defineDatabaseMigrations(): void {
			$this->loadLaravelMigrations();
			$this->loadMigrationsFrom( __DIR__ . '/Core/migrations' );
		}

	}
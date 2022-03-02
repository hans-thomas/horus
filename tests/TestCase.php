<?php

	namespace Hans\Horus\Tests;

	use AreasEnum;
	use Hans\Horus\Contracts\HorusContract;
	use Hans\Horus\Facades\HorusSeeder as Horus;
	use Hans\Horus\HorusServiceProvider;
	use Hans\Horus\Models\Role;
	use Hans\Sphinx\SphinxServiceProvider;
	use Illuminate\Foundation\Application;
	use Illuminate\Foundation\Testing\RefreshDatabase;
	use Illuminate\Routing\Router;
	use Orchestra\Testbench\TestCase as BaseTestCase;
	use RolesEnum;

	class TestCase extends BaseTestCase {
		use RefreshDatabase;

		public HorusContract $horus;

		/**
		 * Setup the test environment.
		 */
		protected function setUp(): void {
			// Code before application created.

			parent::setUp();

			// Code after application created.
			$this->horus = app( HorusContract::class );
			$this->seedHorus();
		}

		private function seedHorus(): void {
			Horus::createPermissions( [
				"User" => 'admin'
			], AreasEnum::ADMIN );
			Horus::createPermissions( [
				"User" => 'user'
			], AreasEnum::USER );

			Horus::createRoles( [ RolesEnum::DEFAULT_ADMINS ], AreasEnum::ADMIN );

			Horus::createRoles( [ RolesEnum::DEFAULT_USERS ], AreasEnum::USER );

			Horus::assignPermissionsToRole( Role::findByName( RolesEnum::DEFAULT_ADMINS, AreasEnum::ADMIN ), [
				"User" => [
					'view',
					'update'
				]
			], AreasEnum::ADMIN );

			Horus::assignPermissionsToRole( Role::findByName( RolesEnum::DEFAULT_USERS, AreasEnum::USER ), [
				"User" => [ 'view' ],
			], AreasEnum::USER );


			Horus::createSuperPermissions( [
				"User" => '*',
			], AreasEnum::ADMIN );

			Horus::assignSuperPermissionsToRole( Role::findByName( RolesEnum::DEFAULT_ADMINS, AreasEnum::ADMIN ), [
				"User"
			] );
		}

		/**
		 * Get application timezone.
		 *
		 * @param Application $app
		 *
		 * @return string|null
		 */
		protected function getApplicationTimezone( $app ) {
			return 'UTC';
		}

		/**
		 * Get package providers.
		 *
		 * @param Application $app
		 *
		 * @return array
		 */
		protected function getPackageProviders( $app ) {
			return [
				HorusServiceProvider::class,
				SphinxServiceProvider::class
			];
		}

		/**
		 * Override application aliases.
		 *
		 * @param Application $app
		 *
		 * @return array
		 */
		protected function getPackageAliases( $app ) {
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
		protected function defineEnvironment( $app ) {
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
		protected function defineRoutes( $router ) {
			// Define routes.
		}

		/**
		 * Define database migrations.
		 *
		 * @return void
		 */
		protected function defineDatabaseMigrations() {
			$this->loadLaravelMigrations();
		}

		/**
		 * Get base path.
		 *
		 * @return string
		 */
		protected function getBasePath() {
			return __DIR__ . '/skeleton/laravel-9.x';
		}
	}
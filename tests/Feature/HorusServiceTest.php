<?php

	namespace Hans\Horus\Tests\Feature;

	use Hans\Horus\Facades\Horus;
	use Hans\Horus\Tests\TestCase;
	use Spatie\Permission\Models\Role;

	class HorusServiceTest extends TestCase {

		private string $default_guard;

		/**
		 * Setup the test environment.
		 *
		 * @return void
		 */
		protected function setUp(): void {
			parent::setUp();
			$this->default_guard = config( 'auth.defaults.guard' );
		}


		/**
		 * @test
		 *
		 * @return void
		 */
		public function createRoles(): void {
			$roles = [ 'admin', [ 'name' => 'user', 'guard_name' => 'customers' ] ];

			self::assertTrue(
				Horus::createRoles( $roles )
			);

			self::assertInstanceOf(
				Role::class,
				Role::findByName( 'admin', $this->default_guard )
			);
			self::assertEquals(
				[
					'name'       => 'admin',
					'guard_name' => $this->default_guard
				],
				Role::findByName( 'admin', $this->default_guard )->only( 'name', 'guard_name' )
			);
		}

	}
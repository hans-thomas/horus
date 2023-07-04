<?php

	namespace Hans\Horus\Tests\Feature;

	use Hans\Horus\Facades\Horus;
	use Hans\Horus\Tests\Instances\Models\Article;
	use Hans\Horus\Tests\Instances\Models\Category;
	use Hans\Horus\Tests\Instances\Models\Comment;
	use Hans\Horus\Tests\Instances\Models\Post;
	use Hans\Horus\Tests\Instances\Models\Tag;
	use Hans\Horus\Tests\Instances\Policies\PostPolicy;
	use Hans\Horus\Tests\TestCase;
	use Spatie\Permission\Models\Permission;
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

		/**
		 * @test
		 *
		 * @return void
		 */
		public function createRolesWithDifferentGuard(): void {
			$roles = [ 'user' ];

			self::assertTrue(
				Horus::createRoles( $roles, 'customer' )
			);

			self::assertInstanceOf(
				Role::class,
				Role::findByName( 'user', 'customer' )
			);
			self::assertEquals(
				[
					'name'       => 'user',
					'guard_name' => 'customer'
				],
				Role::findByName( 'user', 'customer' )->only( 'name', 'guard_name' )
			);
		}

		/**
		 * @test
		 *
		 * @return void
		 */
		public function createPermissions(): void {
			$permissions = [
				Post::class, // supposed to create all basic permissions
				Category::class => [ 'viewPosts', 'viewPosts' ], // supposed to create only viewPosts permission
				Comment::class  => [ '*', 'viewPost' ], // supposed to create basics + viewPost permissions
				Article::class  => 'viewComments', // supposed to create only viewComments permission
				Tag::class      => '*', // same as first one
			];

			self::assertTrue(
				Horus::createPermissions( $permissions )
			);

			self::assertEquals(
				[
					[
						"name"       => "tests.instances.models.post.viewAny",
						"guard_name" => "web"
					],
					[
						"name"       => "tests.instances.models.post.view",
						"guard_name" => "web"
					],
					[
						"name"       => "tests.instances.models.post.create",
						"guard_name" => "web"
					],
					[
						"name"       => "tests.instances.models.post.update",
						"guard_name" => "web"
					],
					[
						"name"       => "tests.instances.models.post.delete",
						"guard_name" => "web"
					],
					[
						"name"       => "tests.instances.models.post.restore",
						"guard_name" => "web"
					],
					[
						"name"       => "tests.instances.models.post.forceDelete",
						"guard_name" => "web"
					]
				],
				Permission::query()
				          ->where( 'name', 'LIKE', 'tests.instances.models.post%' )
				          ->get()
				          ->map(
					          fn( $value ) => [ 'name' => $value->name, 'guard_name' => $value->guard_name ]
				          )
				          ->toArray()
			);

			self::assertEquals(
				[
					[
						"name"       => "tests.instances.models.category.viewPosts",
						"guard_name" => "web"
					]
				],
				Permission::query()
				          ->where( 'name', 'LIKE', 'tests.instances.models.category%' )
				          ->get()
				          ->map(
					          fn( $value ) => [ 'name' => $value->name, 'guard_name' => $value->guard_name ]
				          )
				          ->toArray()
			);

			self::assertEquals(
				[
					[
						"name"       => "tests.instances.models.comment.viewAny",
						"guard_name" => "web"
					],
					[
						"name"       => "tests.instances.models.comment.view",
						"guard_name" => "web"
					],
					[
						"name"       => "tests.instances.models.comment.create",
						"guard_name" => "web"
					],
					[
						"name"       => "tests.instances.models.comment.update",
						"guard_name" => "web"
					],
					[
						"name"       => "tests.instances.models.comment.delete",
						"guard_name" => "web"
					],
					[
						"name"       => "tests.instances.models.comment.restore",
						"guard_name" => "web"
					],
					[
						"name"       => "tests.instances.models.comment.forceDelete",
						"guard_name" => "web"
					],
					[
						"name"       => "tests.instances.models.comment.viewPost",
						"guard_name" => "web"
					]
				],
				Permission::query()
				          ->where( 'name', 'LIKE', 'tests.instances.models.comment%' )
				          ->get()
				          ->map(
					          fn( $value ) => [ 'name' => $value->name, 'guard_name' => $value->guard_name ]
				          )
				          ->toArray()
			);

			self::assertEquals(
				[
					[
						"name"       => "tests.instances.models.article.viewComments",
						"guard_name" => "web"
					]
				],
				Permission::query()
				          ->where( 'name', 'LIKE', 'tests.instances.models.article%' )
				          ->get()
				          ->map(
					          fn( $value ) => [ 'name' => $value->name, 'guard_name' => $value->guard_name ]
				          )
				          ->toArray()
			);

			self::assertEquals(
				[
					[
						"name"       => "tests.instances.models.tag.viewAny",
						"guard_name" => "web"
					],
					[
						"name"       => "tests.instances.models.tag.view",
						"guard_name" => "web"
					],
					[
						"name"       => "tests.instances.models.tag.create",
						"guard_name" => "web"
					],
					[
						"name"       => "tests.instances.models.tag.update",
						"guard_name" => "web"
					],
					[
						"name"       => "tests.instances.models.tag.delete",
						"guard_name" => "web"
					],
					[
						"name"       => "tests.instances.models.tag.restore",
						"guard_name" => "web"
					],
					[
						"name"       => "tests.instances.models.tag.forceDelete",
						"guard_name" => "web"
					]
				],
				Permission::query()
				          ->where( 'name', 'LIKE', 'tests.instances.models.tag%' )
				          ->get()
				          ->map(
					          fn( $value ) => [ 'name' => $value->name, 'guard_name' => $value->guard_name ]
				          )
				          ->toArray()
			);
		}

		/**
		 * @test
		 *
		 * @return void
		 */
		public function createPermissionsWithDifferentGuard(): void {
			$permissions = [
				Post::class,
				Category::class => [ 'viewPosts', 'viewPosts' ],
				Comment::class  => [ '*', 'viewPost' ],
				Article::class  => 'viewComments',
				Tag::class      => '*',
			];

			self::assertTrue(
				Horus::createPermissions( $permissions, 'sphinx' )
			);

			self::assertEquals(
				[
					[
						"name"       => "tests.instances.models.post.viewAny",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.post.view",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.post.create",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.post.update",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.post.delete",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.post.restore",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.post.forceDelete",
						"guard_name" => "sphinx"
					]
				],
				Permission::query()
				          ->where( 'name', 'LIKE', 'tests.instances.models.post%' )
				          ->get()
				          ->map(
					          fn( $value ) => [ 'name' => $value->name, 'guard_name' => $value->guard_name ]
				          )
				          ->toArray()
			);

			self::assertEquals(
				[
					[
						"name"       => "tests.instances.models.category.viewPosts",
						"guard_name" => "sphinx"
					]
				],
				Permission::query()
				          ->where( 'name', 'LIKE', 'tests.instances.models.category%' )
				          ->get()
				          ->map(
					          fn( $value ) => [ 'name' => $value->name, 'guard_name' => $value->guard_name ]
				          )
				          ->toArray()
			);

			self::assertEquals(
				[
					[
						"name"       => "tests.instances.models.comment.viewAny",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.comment.view",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.comment.create",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.comment.update",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.comment.delete",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.comment.restore",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.comment.forceDelete",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.comment.viewPost",
						"guard_name" => "sphinx"
					]
				],
				Permission::query()
				          ->where( 'name', 'LIKE', 'tests.instances.models.comment%' )
				          ->get()
				          ->map(
					          fn( $value ) => [ 'name' => $value->name, 'guard_name' => $value->guard_name ]
				          )
				          ->toArray()
			);

			self::assertEquals(
				[
					[
						"name"       => "tests.instances.models.article.viewComments",
						"guard_name" => "sphinx"
					]
				],
				Permission::query()
				          ->where( 'name', 'LIKE', 'tests.instances.models.article%' )
				          ->get()
				          ->map(
					          fn( $value ) => [ 'name' => $value->name, 'guard_name' => $value->guard_name ]
				          )
				          ->toArray()
			);

			self::assertEquals(
				[
					[
						"name"       => "tests.instances.models.tag.viewAny",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.tag.view",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.tag.create",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.tag.update",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.tag.delete",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.tag.restore",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.tag.forceDelete",
						"guard_name" => "sphinx"
					]
				],
				Permission::query()
				          ->where( 'name', 'LIKE', 'tests.instances.models.tag%' )
				          ->get()
				          ->map(
					          fn( $value ) => [ 'name' => $value->name, 'guard_name' => $value->guard_name ]
				          )
				          ->toArray()
			);
		}

		/**
		 * @test
		 *
		 * @return void
		 */
		public function createSuperPermissions(): void {
			$permissions = [
				Post::class,
			];

			self::assertTrue(
				Horus::createSuperPermissions( $permissions )
			);

			self::assertEquals(
				[
					[
						"name"       => "tests.instances.models.post.*",
						"guard_name" => "web"
					]
				],
				Permission::query()
				          ->where( 'name', 'LIKE', 'tests.instances.models.post%' )
				          ->get()
				          ->map(
					          fn( $value ) => [ 'name' => $value->name, 'guard_name' => $value->guard_name ]
				          )
				          ->toArray()
			);
		}

		/**
		 * @test
		 *
		 * @return void
		 */
		public function createSuperPermissionsWithDifferentGuard(): void {
			$permissions = [
				Post::class,
			];

			self::assertTrue(
				Horus::createSuperPermissions( $permissions, 'sphinx' )
			);

			self::assertEquals(
				[
					[
						"name"       => "tests.instances.models.post.*",
						"guard_name" => "sphinx"
					]
				],
				Permission::query()
				          ->where( 'name', 'LIKE', 'tests.instances.models.post%' )
				          ->get()
				          ->map(
					          fn( $value ) => [ 'name' => $value->name, 'guard_name' => $value->guard_name ]
				          )
				          ->toArray()
			);
		}

		/**
		 * @test
		 *
		 * @return void
		 */
		public function assignPermissionsToRole(): void {
			$roles = [ 'admin', 'reporter' ];
			self::assertTrue(
				Horus::createRoles( $roles )
			);

			$permissions = [
				Post::class,
				Category::class => [ 'viewPosts' ]
			];
			self::assertTrue(
				Horus::createPermissions( $permissions )
			);

			self::assertTrue(
				Horus::assignPermissionsToRole(
					'admin',
					[
						Post::class => [ 'viewAny', 'update' ]
					]
				)
			);

			self::assertCount(
				2,
				Role::findByName( 'admin' )->getPermissionNames()->toArray()
			);
			self::assertContains(
				'tests.instances.models.post.viewAny',
				Role::findByName( 'admin' )->getPermissionNames()->toArray()
			);
			self::assertContains(
				'tests.instances.models.post.update',
				Role::findByName( 'admin' )->getPermissionNames()->toArray()
			);

			self::assertNotContains(
				'tests.instances.models.post.delete',
				Role::findByName( 'admin' )->getPermissionNames()->toArray()
			);
			self::assertNotContains(
				'tests.instances.models.category.viewPosts',
				Role::findByName( 'admin' )->getPermissionNames()->toArray()
			);
		}

		/**
		 * @test
		 *
		 * @return void
		 */
		public function assignSuperPermissionsToRole(): void {
			$roles = [ 'admin', 'reporter' ];
			self::assertTrue(
				Horus::createRoles( $roles )
			);

			$permissions = [
				Post::class,
				Category::class // tests.instances.models.category.*
			];

			self::assertTrue(
				Horus::createSuperPermissions( $permissions )
			);

			self::assertTrue(
				Horus::assignSuperPermissionsToRole(
					Role::findByName( 'admin' ),
					[
						Category::class
					]
				)
			);

			self::assertContains(
				'tests.instances.models.category.*',
				Role::findByName( 'admin' )->getPermissionNames()->toArray()
			);
		}

		/**
		 * @test
		 *
		 * @return void
		 */
		public function createPermissionsUsingPolicy(): void {
			$policy = PostPolicy::class;
			$model  = Post::class;

			self::assertTrue(
				Horus::createPermissionsUsingPolicy(
					$policy,
					$model,
				)
			);

			self::assertEquals(
				[
					[
						"name"       => "tests.instances.models.post.viewAny",
						"guard_name" => $this->default_guard
					],
					[
						"name"       => "tests.instances.models.post.view",
						"guard_name" => $this->default_guard
					],
					[
						"name"       => "tests.instances.models.post.create",
						"guard_name" => $this->default_guard
					],
					[
						"name"       => "tests.instances.models.post.update",
						"guard_name" => $this->default_guard
					],
					[
						"name"       => "tests.instances.models.post.delete",
						"guard_name" => $this->default_guard
					],
					[
						"name"       => "tests.instances.models.post.restore",
						"guard_name" => $this->default_guard
					],
					[
						"name"       => "tests.instances.models.post.forceDelete",
						"guard_name" => $this->default_guard
					],
					[
						"name"       => "tests.instances.models.post.viewComments",
						"guard_name" => $this->default_guard
					],
					[
						"name"       => "tests.instances.models.post.updateComments",
						"guard_name" => $this->default_guard
					]
				],
				Permission::query()
				          ->where( 'name', 'LIKE', 'tests.instances.models.post%' )
				          ->get()
				          ->map(
					          fn( $value ) => [ 'name' => $value->name, 'guard_name' => $value->guard_name ]
				          )
				          ->toArray()
			);

		}

		/**
		 * @test
		 *
		 * @return void
		 */
		public function createPermissionsUsingPolicyWithDifferentGuardAndIgnoredMethods(): void {
			$policy = PostPolicy::class;
			$model  = Post::class;

			self::assertTrue(
				Horus::createPermissionsUsingPolicy(
					$policy,
					$model,
					'sphinx',
					[ 'viewComments' ]
				)
			);

			self::assertEquals(
				[
					[
						"name"       => "tests.instances.models.post.viewAny",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.post.view",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.post.create",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.post.update",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.post.delete",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.post.restore",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.post.forceDelete",
						"guard_name" => "sphinx"
					],
					[
						"name"       => "tests.instances.models.post.updateComments",
						"guard_name" => "sphinx"
					]
				],
				Permission::query()
				          ->where( 'name', 'LIKE', 'tests.instances.models.post%' )
				          ->get()
				          ->map(
					          fn( $value ) => [ 'name' => $value->name, 'guard_name' => $value->guard_name ]
				          )
				          ->toArray()
			);

			$this->assertDatabaseMissing(
				( new Permission )->getTable(),
				[
					'name' => 'tests.instances.models.post.viewComments'
				]
			);

		}

	}
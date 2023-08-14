<?php

namespace Hans\Horus\Tests\Feature;

use Hans\Horus\Exceptions\HorusException;
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

class HorusServiceTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function createRoles(): void
    {
        $roles = ['admin', ['name' => 'user', 'guard_name' => 'customers']];

        self::assertTrue(
            Horus::createRoles($roles)
        );

        self::assertInstanceOf(
            Role::class,
            Role::findByName('admin', $this->default_guard)
        );
        self::assertEquals(
            [
                'name'       => 'admin',
                'guard_name' => $this->default_guard,
            ],
            Role::findByName('admin', $this->default_guard)->only('name', 'guard_name')
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function createRolesWithDifferentGuard(): void
    {
        $roles = ['user'];

        self::assertTrue(
            Horus::createRoles($roles, 'customer')
        );

        self::assertInstanceOf(
            Role::class,
            Role::findByName('user', 'customer')
        );
        self::assertEquals(
            [
                'name'       => 'user',
                'guard_name' => 'customer',
            ],
            Role::findByName('user', 'customer')->only('name', 'guard_name')
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function createPermissions(): void
    {
        $permissions = [
            Post::class, // supposed to create all basic permissions
            Category::class => ['viewPosts', 'viewPosts'], // supposed to create only viewPosts permission
            Comment::class  => ['*', 'viewPost'], // supposed to create basics + viewPost permissions
            Article::class  => 'viewComments', // supposed to create only viewComments permission
            Tag::class      => '*', // same as first one
        ];

        self::assertTrue(
            Horus::createPermissions($permissions)
        );

        self::assertEquals(
            [
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}viewAny",
                    'guard_name' => 'web',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}view",
                    'guard_name' => 'web',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}create",
                    'guard_name' => 'web',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}update",
                    'guard_name' => 'web',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}delete",
                    'guard_name' => 'web',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}restore",
                    'guard_name' => 'web',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}forceDelete",
                    'guard_name' => 'web',
                ],
            ],
            Permission::query()
                      ->where(
                          'name',
                          'LIKE',
                          "tests{$this->separator}instances{$this->separator}models{$this->separator}post%"
                      )
                      ->get()
                      ->map(
                          fn ($value) => ['name' => $value->name, 'guard_name' => $value->guard_name]
                      )
                      ->toArray()
        );

        self::assertEquals(
            [
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}category{$this->separator}viewPosts",
                    'guard_name' => 'web',
                ],
            ],
            Permission::query()
                      ->where(
                          'name',
                          'LIKE',
                          "tests{$this->separator}instances{$this->separator}models{$this->separator}category%"
                      )
                      ->get()
                      ->map(
                          fn ($value) => ['name' => $value->name, 'guard_name' => $value->guard_name]
                      )
                      ->toArray()
        );

        self::assertEquals(
            [
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}comment{$this->separator}viewAny",
                    'guard_name' => 'web',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}comment{$this->separator}view",
                    'guard_name' => 'web',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}comment{$this->separator}create",
                    'guard_name' => 'web',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}comment{$this->separator}update",
                    'guard_name' => 'web',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}comment{$this->separator}delete",
                    'guard_name' => 'web',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}comment{$this->separator}restore",
                    'guard_name' => 'web',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}comment{$this->separator}forceDelete",
                    'guard_name' => 'web',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}comment{$this->separator}viewPost",
                    'guard_name' => 'web',
                ],
            ],
            Permission::query()
                      ->where(
                          'name',
                          'LIKE',
                          "tests{$this->separator}instances{$this->separator}models{$this->separator}comment%"
                      )
                      ->get()
                      ->map(
                          fn ($value) => ['name' => $value->name, 'guard_name' => $value->guard_name]
                      )
                      ->toArray()
        );

        self::assertEquals(
            [
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}article{$this->separator}viewComments",
                    'guard_name' => 'web',
                ],
            ],
            Permission::query()
                      ->where(
                          'name',
                          'LIKE',
                          "tests{$this->separator}instances{$this->separator}models{$this->separator}article%"
                      )
                      ->get()
                      ->map(
                          fn ($value) => ['name' => $value->name, 'guard_name' => $value->guard_name]
                      )
                      ->toArray()
        );

        self::assertEquals(
            [
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}tag{$this->separator}viewAny",
                    'guard_name' => 'web',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}tag{$this->separator}view",
                    'guard_name' => 'web',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}tag{$this->separator}create",
                    'guard_name' => 'web',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}tag{$this->separator}update",
                    'guard_name' => 'web',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}tag{$this->separator}delete",
                    'guard_name' => 'web',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}tag{$this->separator}restore",
                    'guard_name' => 'web',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}tag{$this->separator}forceDelete",
                    'guard_name' => 'web',
                ],
            ],
            Permission::query()
                      ->where(
                          'name',
                          'LIKE',
                          "tests{$this->separator}instances{$this->separator}models{$this->separator}tag%"
                      )
                      ->get()
                      ->map(
                          fn ($value) => ['name' => $value->name, 'guard_name' => $value->guard_name]
                      )
                      ->toArray()
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function createPermissionsWithInvalidModel(): void
    {
        $permissions = [
            '\Hans\Horus\Tests\Instances\NotExistModels\Post',
        ];

        $this->expectException(HorusException::class);
        $this->expectExceptionMessage('Class is not a valid model! [ \Hans\Horus\Tests\Instances\NotExistModels\Post ].');

        Horus::createPermissions($permissions);
    }

    /**
     * @test
     *
     * @return void
     */
    public function createPermissionsWithDifferentGuard(): void
    {
        $permissions = [
            Post::class,
            Category::class => ['viewPosts', 'viewPosts'],
            Comment::class  => ['*', 'viewPost'],
            Article::class  => 'viewComments',
            Tag::class      => '*',
        ];

        self::assertTrue(
            Horus::createPermissions($permissions, 'sphinx')
        );

        self::assertEquals(
            [
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}viewAny",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}view",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}create",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}update",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}delete",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}restore",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}forceDelete",
                    'guard_name' => 'sphinx',
                ],
            ],
            Permission::query()
                      ->where(
                          'name',
                          'LIKE',
                          "tests{$this->separator}instances{$this->separator}models{$this->separator}post%"
                      )
                      ->get()
                      ->map(
                          fn ($value) => ['name' => $value->name, 'guard_name' => $value->guard_name]
                      )
                      ->toArray()
        );

        self::assertEquals(
            [
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}category{$this->separator}viewPosts",
                    'guard_name' => 'sphinx',
                ],
            ],
            Permission::query()
                      ->where(
                          'name',
                          'LIKE',
                          "tests{$this->separator}instances{$this->separator}models{$this->separator}category%"
                      )
                      ->get()
                      ->map(
                          fn ($value) => ['name' => $value->name, 'guard_name' => $value->guard_name]
                      )
                      ->toArray()
        );

        self::assertEquals(
            [
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}comment{$this->separator}viewAny",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}comment{$this->separator}view",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}comment{$this->separator}create",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}comment{$this->separator}update",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}comment{$this->separator}delete",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}comment{$this->separator}restore",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}comment{$this->separator}forceDelete",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}comment{$this->separator}viewPost",
                    'guard_name' => 'sphinx',
                ],
            ],
            Permission::query()
                      ->where(
                          'name',
                          'LIKE',
                          "tests{$this->separator}instances{$this->separator}models{$this->separator}comment%"
                      )
                      ->get()
                      ->map(
                          fn ($value) => ['name' => $value->name, 'guard_name' => $value->guard_name]
                      )
                      ->toArray()
        );

        self::assertEquals(
            [
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}article{$this->separator}viewComments",
                    'guard_name' => 'sphinx',
                ],
            ],
            Permission::query()
                      ->where(
                          'name',
                          'LIKE',
                          "tests{$this->separator}instances{$this->separator}models{$this->separator}article%"
                      )
                      ->get()
                      ->map(
                          fn ($value) => ['name' => $value->name, 'guard_name' => $value->guard_name]
                      )
                      ->toArray()
        );

        self::assertEquals(
            [
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}tag{$this->separator}viewAny",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}tag{$this->separator}view",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}tag{$this->separator}create",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}tag{$this->separator}update",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}tag{$this->separator}delete",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}tag{$this->separator}restore",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}tag{$this->separator}forceDelete",
                    'guard_name' => 'sphinx',
                ],
            ],
            Permission::query()
                      ->where(
                          'name',
                          'LIKE',
                          "tests{$this->separator}instances{$this->separator}models{$this->separator}tag%"
                      )
                      ->get()
                      ->map(
                          fn ($value) => ['name' => $value->name, 'guard_name' => $value->guard_name]
                      )
                      ->toArray()
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function createSuperPermissions(): void
    {
        $permissions = [
            Post::class,
        ];

        self::assertTrue(
            Horus::createSuperPermissions($permissions)
        );

        self::assertEquals(
            [
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}*",
                    'guard_name' => 'web',
                ],
            ],
            Permission::query()
                      ->where(
                          'name',
                          'LIKE',
                          "tests{$this->separator}instances{$this->separator}models{$this->separator}post%"
                      )
                      ->get()
                      ->map(
                          fn ($value) => ['name' => $value->name, 'guard_name' => $value->guard_name]
                      )
                      ->toArray()
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function createSuperPermissionsWithDifferentGuard(): void
    {
        $permissions = [
            Post::class,
        ];

        self::assertTrue(
            Horus::createSuperPermissions($permissions, 'sphinx')
        );

        self::assertEquals(
            [
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}*",
                    'guard_name' => 'sphinx',
                ],
            ],
            Permission::query()
                      ->where(
                          'name',
                          'LIKE',
                          "tests{$this->separator}instances{$this->separator}models{$this->separator}post%"
                      )
                      ->get()
                      ->map(
                          fn ($value) => ['name' => $value->name, 'guard_name' => $value->guard_name]
                      )
                      ->toArray()
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function assignPermissionsToRole(): void
    {
        $roles = ['admin', 'reporter','reviewer','writers'];
        self::assertTrue(
            Horus::createRoles($roles)
        );

        $permissions = [
	        Tag::class,
	        Post::class => ['*','viewTags'],
	        Comment::class  => ['*', 'viewPost'],
        ];
        self::assertTrue(
            Horus::createPermissions($permissions)
        );

        self::assertTrue(
            Horus::assignPermissionsToRole(
                'admin',
                [
	                Tag::class,
	                Post::class => ['viewAny', 'update','viewTags'],
	                Comment::class => ['*', 'viewPost'],
                ]
            )
        );

        self::assertCount(
            18,
            $adminPermissionsNames=Role::findByName('admin')->getPermissionNames()->toArray()
        );

		// tags permissions
        self::assertContains(
            "tests{$this->separator}instances{$this->separator}models{$this->separator}tag{$this->separator}viewAny",
            $adminPermissionsNames
        );
        self::assertContains(
            "tests{$this->separator}instances{$this->separator}models{$this->separator}tag{$this->separator}update",
            $adminPermissionsNames
        );
        self::assertContains(
            "tests{$this->separator}instances{$this->separator}models{$this->separator}tag{$this->separator}delete",
            $adminPermissionsNames
        );
        self::assertContains(
            "tests{$this->separator}instances{$this->separator}models{$this->separator}tag{$this->separator}restore",
            $adminPermissionsNames
        );
        self::assertContains(
            "tests{$this->separator}instances{$this->separator}models{$this->separator}tag{$this->separator}forceDelete",
            $adminPermissionsNames
        );

		// posts permissions
	    self::assertContains(
		    "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}viewAny",
		    $adminPermissionsNames
	    );
	    self::assertContains(
		    "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}update",
		    $adminPermissionsNames
	    );
	    self::assertContains(
		    "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}viewTags",
		    $adminPermissionsNames
	    );

	    self::assertNotContains(
		    "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}delete",
		    $adminPermissionsNames
	    );
	    self::assertNotContains(
		    "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}restore",
		    $adminPermissionsNames
	    );
	    self::assertNotContains(
		    "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}forceDelete",
		    $adminPermissionsNames
	    );

	    // tags permissions
	    self::assertContains(
		    "tests{$this->separator}instances{$this->separator}models{$this->separator}comment{$this->separator}viewAny",
		    $adminPermissionsNames
	    );
	    self::assertContains(
		    "tests{$this->separator}instances{$this->separator}models{$this->separator}comment{$this->separator}update",
		    $adminPermissionsNames
	    );
	    self::assertContains(
		    "tests{$this->separator}instances{$this->separator}models{$this->separator}comment{$this->separator}delete",
		    $adminPermissionsNames
	    );
	    self::assertContains(
		    "tests{$this->separator}instances{$this->separator}models{$this->separator}comment{$this->separator}restore",
		    $adminPermissionsNames
	    );
	    self::assertContains(
		    "tests{$this->separator}instances{$this->separator}models{$this->separator}comment{$this->separator}forceDelete",
		    $adminPermissionsNames
	    );
	    self::assertContains(
		    "tests{$this->separator}instances{$this->separator}models{$this->separator}comment{$this->separator}viewPost",
		    $adminPermissionsNames
	    );
    }

    /**
     * @test
     *
     * @return void
     */
    public function assignSuperPermissionsToRole(): void
    {
        $roles = ['admin', 'reporter'];
        self::assertTrue(
            Horus::createRoles($roles)
        );

        $permissions = [
            Post::class,
            Category::class,
        ];

        self::assertTrue(
            Horus::createSuperPermissions($permissions)
        );

        self::assertTrue(
            Horus::assignSuperPermissionsToRole(
                Role::findByName('admin'),
                [
                    Category::class,
                ]
            )
        );

        self::assertContains(
            "tests{$this->separator}instances{$this->separator}models{$this->separator}category{$this->separator}*",
            Role::findByName('admin')->getPermissionNames()->toArray()
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function createPermissionsUsingPolicy(): void
    {
        $policy = PostPolicy::class;
        $model = Post::class;

        self::assertTrue(
            Horus::createPermissionsUsingPolicy(
                $policy,
                $model,
            )
        );

        self::assertEquals(
            [
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}viewAny",
                    'guard_name' => $this->default_guard,
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}view",
                    'guard_name' => $this->default_guard,
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}create",
                    'guard_name' => $this->default_guard,
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}update",
                    'guard_name' => $this->default_guard,
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}delete",
                    'guard_name' => $this->default_guard,
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}restore",
                    'guard_name' => $this->default_guard,
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}forceDelete",
                    'guard_name' => $this->default_guard,
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}viewComments",
                    'guard_name' => $this->default_guard,
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}updateComments",
                    'guard_name' => $this->default_guard,
                ],
            ],
            Permission::query()
                      ->where(
                          'name',
                          'LIKE',
                          "tests{$this->separator}instances{$this->separator}models{$this->separator}post%"
                      )
                      ->get()
                      ->map(
                          fn ($value) => ['name' => $value->name, 'guard_name' => $value->guard_name]
                      )
                      ->toArray()
        );
    }

    /**
     * @test
     *
     * @return void
     */
    public function createPermissionsUsingPolicyWithDifferentGuardAndIgnoredMethods(): void
    {
        $policy = PostPolicy::class;
        $model = Post::class;

        self::assertTrue(
            Horus::createPermissionsUsingPolicy(
                $policy,
                $model,
                'sphinx',
                ['viewComments']
            )
        );

        self::assertEquals(
            [
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}viewAny",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}view",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}create",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}update",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}delete",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}restore",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}forceDelete",
                    'guard_name' => 'sphinx',
                ],
                [
                    'name'       => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}updateComments",
                    'guard_name' => 'sphinx',
                ],
            ],
            Permission::query()
                      ->where(
                          'name',
                          'LIKE',
                          "tests{$this->separator}instances{$this->separator}models{$this->separator}post%"
                      )
                      ->get()
                      ->map(
                          fn ($value) => ['name' => $value->name, 'guard_name' => $value->guard_name]
                      )
                      ->toArray()
        );

        $this->assertDatabaseMissing(
            ( new Permission() )->getTable(),
            [
                'name' => "tests{$this->separator}instances{$this->separator}models{$this->separator}post{$this->separator}viewComments",
            ]
        );
    }
}

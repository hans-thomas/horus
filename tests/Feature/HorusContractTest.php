<?php

	namespace Hans\Horus\Tests\Feature;

    use Hans\Horus\Tests\TestCase;
    use Illuminate\Database\Eloquent\Collection;
    use Hans\Horus\Models\Permission;
    use Hans\Horus\Models\Role;

    class HorusContractTest extends TestCase {

        /**
         * @test
         *
         *
         * @return void
         */
        public function findRoleMethod() {
            $role = Role::firstOrFail();

            $this->assertInstanceOf( Role::class, $foundRole = $this->horus->findRole( $role ) );
            $this->assertEquals( $role->id, $foundRole->id );

            $this->assertInstanceOf( Role::class, $foundRole = $this->horus->findRole( $role->id ) );
            $this->assertEquals( $role->id, $foundRole->id );

            $this->assertInstanceOf( Role::class, $foundRole = $this->horus->findRole( $role->name ) );
            $this->assertEquals( $role->id, $foundRole->id );
        }

        /**
         * @test
         *
         *
         * @return void
         */
        public function findAllRoles() {
            $this->assertInstanceOf( Collection::class, $findAll = $this->horus->findAllRoles() );

            $this->assertEquals( Role::all(), $findAll );
        }

        /**
         * @test
         *
         *
         * @return void
         */
        public function findAnyRoles() {
            $roles = Role::take( 2 )->get();

            $this->assertEquals( collect( $roles ),
                $this->horus->findAnyRoles( $roles->first(), $roles->last()->id, 999 ) );
        }

        /**
         * @test
         *
         *
         * @return void
         */
        public function findPermission() {
            $permission = Permission::take( 1 )->first();

            $this->assertInstanceOf( Permission::class, $this->horus->findPermission( $permission ) );
            $this->assertInstanceOf( Permission::class, $this->horus->findPermission( $permission->id ) );
            $this->assertInstanceOf( Permission::class, $this->horus->findPermission( $permission->name ) );

            $this->assertEquals( $permission, $this->horus->findPermission( $permission ) );
        }


        /**
         * @test
         *
         *
         * @return void
         */
        public function findAllPermissions() {
            $this->assertInstanceOf( Collection::class, $findAll = $this->horus->findAllPermissions() );

            $this->assertEquals( Permission::all(), $findAll );
        }

        /**
         * @test
         *
         *
         * @return void
         */
        public function findAnyPermissions() {
            $permissions = Permission::take( 2 )->get();

            $this->assertEquals( collect( $permissions ),
                $this->horus->findAnyPermissions( $permissions->first(), $permissions->last()->id, 999 ) );
        }

    }

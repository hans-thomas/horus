<?php

	namespace Hans\Horus\Tests\Unit;

    use AreasEnum;
    use Hans\Horus\Exceptions\HorusException;
    use Hans\Horus\Models\Role;
    use Hans\Horus\Tests\TestCase;

    class RoleModelTest extends TestCase {
        private Role $normalRole;

        /**
         * Setup the test environment.
         *
         * @return void
         */
        protected function setUp(): void {
            parent::setUp();
            $this->normalRole = Role::firstWhere( [
                'area' => AreasEnum::USER
            ] );
        }

        /**
         * @test
         *
         *
         * @return void
         * @throws HorusException
         */
        public function findByName() {
            $role = Role::findByName( $this->normalRole->name, AreasEnum::USER );
            $this->assertInstanceOf( Role::class, $role );
        }

        /**
         * @test
         *
         *
         * @return void
         */
        public function findByNameInWrongArea() {
            $this->expectException( HorusException::class );
            $this->expectExceptionMessage( 'The ' . $this->normalRole->name . ' role not found in the ' . AreasEnum::ADMIN . ' area!' );
            Role::findByName( $this->normalRole->name, AreasEnum::ADMIN );
        }

        /**
         * @test
         *
         *
         * @return void
         */
        public function findByNameNonExistedRole() {
            $name = 'non-exists';
            $this->expectException( HorusException::class );
            $this->expectExceptionMessage( 'The ' . $name . ' role not found in the all areas!' );
            Role::findByName( $name );
        }


        /**
         * @test
         *
         *
         * @return void
         * @throws HorusException
         */
        public function findById() {
            $role = Role::findById( $this->normalRole->id, AreasEnum::USER );
            $this->assertInstanceOf( Role::class, $role );
        }

        /**
         * @test
         *
         *
         * @return void
         */
        public function findByIdInWrongArea() {
            $this->expectException( HorusException::class );
            $this->expectExceptionMessage( 'The ' . $this->normalRole->id . ' role not found in the ' . AreasEnum::ADMIN . ' area!' );
            Role::findById( $this->normalRole->id, AreasEnum::ADMIN );
        }

        /**
         * @test
         *
         *
         * @return void
         */
        public function findByIdNonExistedRole() {
            $id = 267;
            $this->expectException( HorusException::class );
            $this->expectExceptionMessage( 'The ' . $id . ' role not found in the all areas!' );
            Role::findById( $id );
        }


        /**
         * @test
         *
         *
         * @return void
         * @throws HorusException
         */
        public function findOrCreate() {
            $role = [
                'name' => 'new role',
                'area' => AreasEnum::USER
            ];

            $createdRole = Role::findOrCreate( ...$role );
            $this->assertTrue( $createdRole->wasRecentlyCreated );

            Role::findById( $createdRole->id, $createdRole->area );
        }


        /**
         * @test
         *
         *
         * @return void
         */
        public function getVersion() {
            $this->assertIsInt( $this->normalRole->getVersion() );
            $this->assertEquals( $this->normalRole->version, $this->normalRole->getVersion() );
        }

        /**
         * @test
         *
         *
         * @return void
         */
        public function increaseVersion() {
            $this->assertEquals( 1, $this->normalRole->getVersion() );
            $this->normalRole->update( [ 'name' => 'new name' ] );
            $this->assertEquals( 2, $this->normalRole->getVersion() );
        }

    }

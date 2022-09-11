<?php


	namespace Hans\Horus;


	use Hans\Horus\Models\Permission;
	use Hans\Horus\Models\Role;
	use Hans\Horus\Traits\Utilities;
	use Illuminate\Support\Facades\DB;
	use Throwable;

	class HorusSeeder {
		use Utilities;

		/**
		 * create roles
		 *
		 * @param array  $data
		 * @param string $area
		 *
		 * @throws Throwable
		 */
		public function createRoles( array $data, string $area ): void {
			foreach ( $data as $datum ) {
				$payload[] = [
					'name' => $datum,
					'area' => $area
				];
			}
			DB::beginTransaction();
			try {
				foreach ( $payload ?? [] as $item ) {
					if ( Role::query()->limit( 1 )->where( 'name', $item[ 'name' ] )->exists() ) {
						continue;
					}
					Role::create( $item );
				}
			} catch ( Throwable $e ) {
				DB::rollBack();
				throw $e;
			}
			DB::commit();
		}

		/**
		 * create permissions for registered models
		 *
		 * @param array  $data
		 * @param string $area
		 *
		 * @throws Throwable
		 */
		public function createPermissions( array $data, string $area ): void {
			$permissionCollection = [];
			foreach ( $data as $key => $datum ) {
				// get model normalized name
				$model = $this->getModel( $key, $datum );
				// create permissions
				$permissionCollection = array_merge( $permissionCollection,
					$this->generatePermissions( $model, $datum, $area ) );
			}

			DB::beginTransaction();
			try {
				foreach ( $permissionCollection as $item ) {
					if ( Permission::query()->limit( 1 )->where( 'name', $item[ 'name' ] )->exists() ) {
						continue;
					}
					Permission::create( $item );
				}
			} catch ( Throwable $e ) {
				DB::rollBack();
				throw $e;
			}
			DB::commit();
		}

		public function createSuperPermissions( array $data, string $area ): void {
			$permissionCollection = collect();
			foreach ( $data as $key => $datum ) {
				if ( is_string( $key ) ) {
					$permission = $this->normalizeModelName( $key ) . $this->splitter . $datum;
				} else {
					$permission = $datum . $this->splitter . $datum;
				}
				$permissionCollection->push( [
					'name' => $permission,
					'area' => $area
				] );
			}

			DB::beginTransaction();
			try {
				foreach ( $permissionCollection as $item ) {
					if ( Permission::query()->limit( 1 )->where( 'name', $item[ 'name' ] )->exists() ) {
						continue;
					}
					Permission::create( $item );
				}
			} catch ( Throwable $e ) {
				DB::rollBack();
				throw $e;
			}
			DB::commit();
		}

		/**
		 * assign permissions to the given role
		 *
		 * @param Role   $role
		 * @param array  $data
		 * @param string $area
		 *
		 * @throws Throwable
		 */
		public function assignPermissionsToRole( Role $role, array $data, string $area ) {
			DB::beginTransaction();
			try {
				foreach ( $data as $key => $datum ) {
					$model = $this->getModel( $key, $datum );
					if ( is_array( $datum ) ) {
						foreach ( $datum as $item ) {
							$permission = $item == '*' ? $this->generatePermissions( $model, null,
								$area ) : $this->generatePermission( $model, $item, $area );
							$role->givePermissionTo( ... collect( $permission )->pluck( 'name' ) );
						}
					} elseif ( is_string( $datum ) ) {
						$permission = $datum == '*' ? $this->generatePermissions( $model, null,
							$area ) : $this->generatePermission( $model, $datum, $area );
						$role->givePermissionTo( ... collect( $permission )->pluck( 'name' ) );
					}
				}
			} catch ( Throwable $e ) {
				DB::rollBack();
				throw $e;
			}
			DB::commit();
		}

		public function assignSuperPermissionsToRole( Role $role, array $data ) {
			$permissionCollection = collect();
			foreach ( $data as $datum ) {
				if ( $datum != '*' ) {
					$permission = $this->normalizeModelName( $datum ) . $this->splitter . '*';
				} else {
					$permission = '*' . $this->splitter . '*';
				}
				$permissionCollection->push( $permission );
			}
			try {
				DB::beginTransaction();
				$role->givePermissionTo( ...$permissionCollection->toArray() );
				DB::commit();
			} catch ( Throwable $e ) {
				DB::rollBack();
				dd( 'assigning super permissions to role failed!', $e->getMessage() );
			}
		}
	}

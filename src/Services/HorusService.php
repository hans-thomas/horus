<?php


	namespace Hans\Horus\Services;


	use Hans\Horus\Exceptions\HorusErrorCode;
	use Hans\Horus\Exceptions\HorusException;
	use Illuminate\Database\Eloquent\Model;
	use Spatie\Permission\Models\Permission;
	use Spatie\Permission\Models\Role;
	use Throwable;

	class HorusService {

		public function createRoles( array $roles, string $guard = null ): bool {
			$guard ??= config( 'auth.defaults.guard' );

			$data = array_map(
				function( $item ) use ( $guard ) {
					$result = [];
					if ( is_string( $item ) ) {
						$result = [ 'name' => $item, 'guard_name' => $guard ];
					}

					return $result;
				},
				$roles
			);

			$filtered = array_filter(
				$data,
				fn( $item ) => ! empty( $item )
			);

			try {
				batch()->insert(
					new Role,
					[ 'name', 'guard_name' ],
					$filtered
				);
			} catch ( Throwable $e ) {
				throw new HorusException(
					'Failed to create requested role(s)! ' . $e->getMessage(),
					HorusErrorCode::FAILED_TO_CREATE_ROLES
				);
			}

			return true;
		}

		public function createPermissions( array $permissions, string $guard = null ): bool {
			$data = array_map(
				function( $item, $index ) use ( $guard ) {
					$result = [];
					if ( is_int( $index ) and is_string( $item ) ) {
						$this->validateModel( $item );
						$result = $this->makeBasicPermissions( $item, $guard );
					} else if ( is_string( $index ) and is_array( $item ) ) {
						$this->validateModel( $index );
						if ( in_array( '*', $item ) ) {
							$result = $this->makeBasicPermissions( $index, $guard );
						}
						$withoutAsterisk = array_filter(
							$item,
							fn( $value ) => $value !== '*'
						);
						$result          = array_merge(
							$result,
							$this->makeCustomPermissions( $withoutAsterisk, $index, $guard )
						);
					} elseif ( is_string( $index ) and is_string( $item ) ) {
						$this->validateModel( $index );

						if ( $item === '*' ) {
							$result = $this->makeBasicPermissions( $index, $guard );
						} else {
							$result = $this->makeCustomPermissions( [ $item ], $index, $guard );
						}
					}

					return $result;
				},
				array_values( $permissions ),
				array_keys( $permissions )
			);

			$merged = $this->flatten( $data );
			$unique = $this->unique( $merged );

			try {
				batch()->insert(
					new Permission,
					[ 'name', 'guard_name' ],
					$unique
				);
			} catch ( Throwable $e ) {
				throw new HorusException(
					'Failed to create requested permission(s)! ' . $e->getMessage(),
					HorusErrorCode::FAILED_TO_CREATE_PERMISSIONS
				);
			}

			return true;
		}

		public function createSuperPermissions( array $permissions, string $guard = null ): bool {
			$data = array_map(
				function( $item ) use ( $guard ) {
					$this->validateModel( $item );

					return $this->makeCustomPermissions( [ '*' ], $item, $guard );
				},
				$permissions
			);

			$merged = $this->flatten( $data );
			$unique = $this->unique( $merged );

			try {
				batch()->insert(
					new Permission,
					[ 'name', 'guard_name' ],
					$unique
				);
			} catch ( Throwable $e ) {
				throw new HorusException(
					'Failed to create requested permission(s)! ' . $e->getMessage(),
					HorusErrorCode::FAILED_TO_CREATE_PERMISSIONS
				);
			}

			return true;
		}

		public function assignPermissionsToRole( string|Role $role, array $permissions ): bool {
			$role = is_string( $role ) ?
				Role::findByName( $role ) :
				$role;

			$data = array_map(
				function( $item, $index ) {
					$this->validateModel( $index );
					$item        = is_array( $item ) ? $item : [ $item ];
					$permissions = $this->makeCustomPermissions( $item, $index );
					foreach ( $permissions as $permission ) {
						$data[] = $permission[ 'name' ];
					}

					return $data ?? [];
				},
				array_values( $permissions ),
				array_keys( $permissions ),
			);

			$merged = $this->flatten( $data );

			try {
				$role->syncPermissions( $merged );
			} catch ( Throwable $e ) {
				throw new HorusException(
					'Failed to assign permissions to the role! ' . $e->getMessage(),
					HorusErrorCode::FAILED_TO_ASSIGN_PERMISSIONS_TO_ROLE
				);
			}

			return true;
		}

		private function validateModel( string $class ): void {
			if (
				! class_exists( $class ) and
				! is_a( $class, Model::class, true )
			) {
				throw new HorusException(
					"Class is not a valid model! [ $class ].",
					HorusErrorCode::CLASS_IS_NOT_VALID
				);
			}
		}

		private function makePrefixUsingModel( string $model, string $separator = '.' ): string {
			$exploded = explode( '\\', $model );
			$prefix   = ( $explodedCount = count( $exploded ) ) > 3 ?
				array_slice( $exploded, 2, $explodedCount - 1 ) :
				[ last( $exploded ) ];

			return strtolower( implode( $separator, $prefix ) );
		}

		private function makeBasicPermissions( string $model, string $guard = null, string $separator = '.' ): array {
			$result = [];
			$guard  ??= config( 'auth.defaults.guard' );
			$prefix = $this->makePrefixUsingModel( $model, $separator );

			foreach ( horus_config( 'basic_permissions' ) as $permission ) {
				$result[] = [
					'name'       => "{$prefix}{$separator}{$permission}",
					'guard_name' => $guard
				];
			}

			return $result;
		}

		private function makeCustomPermissions( array $permissions, string $model, string $guard = null, string $separator = '.' ): array {
			$result = [];
			$guard  ??= config( 'auth.defaults.guard' );
			$prefix = $this->makePrefixUsingModel( $model, $separator );

			foreach ( $permissions as $permission ) {
				$result[] = [
					'name'       => "{$prefix}{$separator}{$permission}",
					'guard_name' => $guard
				];
			}

			return $result;
		}

		private function flatten( array $data ): array {
			$merged = [];
			foreach ( $data as $datum ) {
				$merged = array_merge( $merged, $datum );
			}

			return $merged;
		}

		private function unique( array $data ): array {
			$unique = [];
			foreach ( $data as $value ) {
				$unique[ $value[ 'name' ] ] = $value;
			}

			return array_values( $unique );
		}

	}

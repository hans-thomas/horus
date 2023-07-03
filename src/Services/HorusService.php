<?php


	namespace Hans\Horus\Services;


	use Hans\Horus\Exceptions\HorusErrorCode;
	use Hans\Horus\Exceptions\HorusException;
	use Illuminate\Database\Eloquent\Model;
	use Spatie\Permission\Models\Permission;
	use Spatie\Permission\Models\Role;
	use Throwable;

	class HorusService {

		public function createRoles( array $roles ): bool {
			$data = array_map(
				function( $item ) {
					if ( is_string( $item ) ) {
						$item = [ 'name' => $item, 'guard_name' => config( 'auth.defaults.guard' ) ];
					} elseif ( is_array( $item ) ) {
						if ( ! isset( $item[ 'guard_name' ] ) ) {
							$item[ 'guard_name' ] = config( 'auth.defaults.guard' );
						}
					}

					return $item;
				},
				$roles
			);

			try {
				batch()->insert(
					new Role,
					[ 'name', 'guard_name' ],
					$data
				);
			} catch ( Throwable $e ) {
				throw new HorusException(
					'Failed to create requested roles! ' . $e->getMessage(),
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

			$merged = [];
			foreach ( $data as $datum ) {
				$merged = array_merge( $merged, $datum );
			}

			$unique = [];
			foreach ( $merged as $value ) {
				$unique[ $value[ 'name' ] ] = $value;
			}

			$data = array_values( $unique );

			try {
				batch()->insert(
					new Permission,
					[ 'name', 'guard_name' ],
					$data
				);
			} catch ( Throwable $e ) {
				throw new HorusException(
					'Failed to create requested permissions! ' . $e->getMessage(),
					HorusErrorCode::FAILED_TO_CREATE_PERMISSIONS
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

		private function makePrefixUsingModel( string $model ): string {
			$exploded = explode( '\\', $model );
			$prefix   = ( $explodedCount = count( $exploded ) ) > 3 ?
				array_slice( $exploded, 2, $explodedCount - 1 ) :
				[ last( $exploded ) ];

			return strtolower( implode( '-', $prefix ) );
		}

		private function makeBasicPermissions( string $model, string $guard = null ): array {
			$result = [];
			$guard  ??= config( 'auth.defaults.guard' );
			$prefix = $this->makePrefixUsingModel( $model );

			foreach ( horus_config( 'basic_permissions' ) as $permission ) {
				$result[] = [
					'name'       => "$prefix-$permission",
					'guard_name' => $guard
				];
			}

			return $result;
		}

		private function makeCustomPermissions( array $permissions, string $model, string $guard = null ): array {
			$result = [];
			$guard  ??= config( 'auth.defaults.guard' );
			$prefix = $this->makePrefixUsingModel( $model );

			foreach ( $permissions as $permission ) {
				$result[] = [
					'name'       => "$prefix-$permission",
					'guard_name' => $guard
				];
			}

			return $result;
		}

	}

<?php


	namespace Hans\Horus\Services;


	use Hans\Horus\Exceptions\HorusErrorCode;
	use Hans\Horus\Exceptions\HorusException;
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

	}

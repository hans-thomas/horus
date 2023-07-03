<?php


	namespace Hans\Horus\Facades;


	use Illuminate\Support\Facades\Facade;
	use RuntimeException;

	/**
	 * @method static bool createRoles( array $roles, string $guard = null )
	 * @method static bool createPermissions( array $permissions, string $guard = null )
	 * @method static bool createSuperPermissions( array $permissions, string $guard = null )
	 */
	class Horus extends Facade {

		/**
		 * Get the registered name of the component.
		 *
		 * @return string
		 *
		 * @throws RuntimeException
		 */
		protected static function getFacadeAccessor(): string {
			return 'horus-service';
		}

	}

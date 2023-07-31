<?php

namespace Hans\Horus\Facades;

    use Hans\Horus\Services\HorusService;
    use Illuminate\Support\Facades\Facade;
    use RuntimeException;
    use Spatie\Permission\Models\Role;

    /**
     * @method static bool createRoles( array $roles, string $guard = null )
     * @method static bool createPermissions( array $permissions, string $guard = null )
     * @method static bool createSuperPermissions( array $permissions, string $guard = null )
     * @method static bool assignPermissionsToRole( string|Role $role, array $permissions )
     * @method static bool assignSuperPermissionsToRole( string|Role $role, array $permissions )
     * @method static bool createPermissionsUsingPolicy( string $policyClass, string $model, string $guard = null, array $methodsToIgnore = [] )
     *
     * @see HorusService
     */
    class Horus extends Facade
    {
        /**
         * Get the registered name of the component.
         *
         * @throws RuntimeException
         *
         * @return string
         */
        protected static function getFacadeAccessor(): string
        {
            return 'horus-service';
        }
    }

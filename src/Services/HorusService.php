<?php

namespace Hans\Horus\Services;

use Hans\Horus\Exceptions\HorusErrorCode;
use Hans\Horus\Exceptions\HorusException;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Throwable;

class HorusService
{
    /**
     * Permissions separator.
     *
     * @var string
     */
    private string $separator;

    public function __construct()
    {
        $this->separator = horus_config('separator');
    }

    /**
     * Create roles in given or default guard.
     *
     * @param array       $roles
     * @param string|null $guard
     *
     * @throws HorusException
     *
     * @return bool
     */
    public function createRoles(array $roles, string $guard = null): bool
    {
        $guard = $this->resolveGuard($guard);

        $data = array_map(
            function ($item) use ($guard) {
                $result = [];
                if (is_string($item)) {
                    $result = ['name' => $item, 'guard_name' => $guard];
                }

                return $result;
            },
            $roles
        );

        $filtered = array_filter(
            $data,
            fn ($item) => !empty($item)
        );

        try {
            batch()->insert(
                new Role(),
                ['name', 'guard_name'],
                $filtered
            );
        } catch (Throwable $e) {
            throw new HorusException(
                'Failed to create requested role(s)! '.$e->getMessage(),
                HorusErrorCode::FAILED_TO_CREATE_ROLES
            );
        }

        return true;
    }

    /**
     * Create permissions in given or default guard.
     *
     * @param array       $permissions
     * @param string|null $guard
     *
     * @throws HorusException
     *
     * @return bool
     */
    public function createPermissions(array $permissions, string $guard = null): bool
    {
        $data = array_map(
            function ($item, $index) use ($guard) {
                $result = [];
                if (is_int($index) and is_string($item)) {
                    $this->validateModel($item);
                    $result = $this->makeBasicPermissions($item, $guard);
                } elseif (is_string($index) and is_array($item)) {
                    $this->validateModel($index);
                    if (in_array('*', $item)) {
                        $result = $this->makeBasicPermissions($index, $guard);
                    }
                    $withoutAsterisk = array_filter(
                        $item,
                        fn ($value) => $value !== '*'
                    );
                    $result = array_merge(
                        $result,
                        $this->makeCustomPermissions($withoutAsterisk, $index, $guard)
                    );
                } elseif (is_string($index) and is_string($item)) {
                    $this->validateModel($index);

                    if ($item === '*') {
                        $result = $this->makeBasicPermissions($index, $guard);
                    } else {
                        $result = $this->makeCustomPermissions([$item], $index, $guard);
                    }
                }

                return $result;
            },
            array_values($permissions),
            array_keys($permissions)
        );

        $merged = $this->flatten($data);
        $unique = $this->unique($merged);

        try {
            batch()->insert(
                new Permission(),
                ['name', 'guard_name'],
                $unique
            );
        } catch (Throwable $e) {
            throw new HorusException(
                'Failed to create requested permission(s)! '.$e->getMessage(),
                HorusErrorCode::FAILED_TO_CREATE_PERMISSIONS
            );
        }

        return true;
    }

    /**
     * Create super permissions in given or default guard.
     *
     * @param array       $permissions
     * @param string|null $guard
     *
     * @throws HorusException
     *
     * @return bool
     */
    public function createSuperPermissions(array $permissions, string $guard = null): bool
    {
        $data = array_map(
            function ($item) use ($guard) {
                $this->validateModel($item);

                return $this->makeCustomPermissions(['*'], $item, $guard);
            },
            $permissions
        );

        $merged = $this->flatten($data);
        $unique = $this->unique($merged);

        try {
            batch()->insert(
                new Permission(),
                ['name', 'guard_name'],
                $unique
            );
        } catch (Throwable $e) {
            throw new HorusException(
                'Failed to create requested permission(s)! '.$e->getMessage(),
                HorusErrorCode::FAILED_TO_CREATE_PERMISSIONS
            );
        }

        return true;
    }

    /**
     * Assign given permissions to a specific role.
     *
     * @param string|Role $role
     * @param array       $permissions
     *
     * @throws HorusException
     *
     * @return bool
     */
    public function assignPermissionsToRole(string|Role $role, array $permissions): bool
    {
        $role = $this->resolveRole($role);

        $data = array_map(
            function ($item, $index) {
                $model = $index;
                $permissions = [];
                if (is_int($index)) {
                    $model = $item;
                    $permissions = $this->makeBasicPermissions($model);
                } else {
                    $item = is_array($item) ? $item : [$item];
                    if (in_array('*', $item)) {
                        $permissions = $this->makeBasicPermissions($model);
                        $item = array_filter(
                            $item,
                            fn ($value) => $value !== '*'
                        );
                    }
                    $permissions = array_merge($permissions, $this->makeCustomPermissions($item, $model));
                }
                $this->validateModel($model);
                foreach ($permissions as $permission) {
                    $data[] = $permission['name'];
                }

                return $data ?? [];
            },
            array_values($permissions),
            array_keys($permissions),
        );

        $merged = $this->flatten($data);

        try {
            $role->givePermissionTo($merged);
        } catch (Throwable $e) {
            throw new HorusException(
                'Failed to assign permissions to the role! '.$e->getMessage(),
                HorusErrorCode::FAILED_TO_ASSIGN_PERMISSIONS_TO_ROLE
            );
        }

        return true;
    }

    /**
     * Assign super permissions to a specific role.
     *
     * @param string|Role $role
     * @param array       $permissions
     *
     * @throws HorusException
     *
     * @return bool
     */
    public function assignSuperPermissionsToRole(string|Role $role, array $permissions): bool
    {
        $role = $this->resolveRole($role);

        $data = array_map(
            function ($item) {
                $this->validateModel($item);
                $permissions = $this->makeCustomPermissions(['*'], $item);
                foreach ($permissions as $permission) {
                    $data[] = $permission['name'];
                }

                return $data ?? [];
            },
            array_values($permissions),
        );

        $merged = $this->flatten($data);

        try {
            $role->givePermissionTo($merged);
        } catch (Throwable $e) {
            throw new HorusException(
                'Failed to assign permissions to the role! '.$e->getMessage(),
                HorusErrorCode::FAILED_TO_ASSIGN_PERMISSIONS_TO_ROLE
            );
        }

        return true;
    }

    /**
     * Create permissions using policy class's methods.
     *
     * @param string      $policyClass
     * @param string      $model
     * @param string|null $guard
     * @param array       $methodsToIgnore
     *
     * @throws HorusException
     *
     * @return bool
     */
    public function createPermissionsUsingPolicy(
        string $policyClass,
        string $model,
        string $guard = null,
        array $methodsToIgnore = []
    ): bool {
        $guard = $this->resolveGuard($guard);

        if (!class_exists($policyClass)) {
            throw new HorusException(
                'Policy class is not exists!',
                HorusErrorCode::POLICY_CLASS_IS_NOT_EXISTS
            );
        }

        $policy = collect(( new ReflectionClass($policyClass) )->getMethods())->pluck('name');

        // collect HandlesAuthorization trait method to ignore if used on policy class
        $handlesTrait = collect(
            ( new ReflectionClass(HandlesAuthorization::class) )->getMethods()
        )
            ->pluck('name');

        $extracted = $policy->filter(fn ($item) => !in_array($item, $handlesTrait->toArray()))
                            ->filter(fn ($item) => !in_array($item, $methodsToIgnore))
                            ->toArray();

        $data[$model] = $extracted;

        return $this->createPermissions($data, $guard);
    }

    /**
     * Validate the given model class.
     *
     * @param string $class
     *
     * @throws HorusException
     *
     * @return void
     */
    private function validateModel(string $class): void
    {
        if (
            !class_exists($class) and
            !is_a($class, Model::class, true)
        ) {
            throw new HorusException(
                "Class is not a valid model! [ $class ].",
                HorusErrorCode::CLASS_IS_NOT_VALID
            );
        }
    }

    /**
     * Make a prefix for given model.
     *
     * @param string $model
     *
     * @return string
     */
    private function makePrefixUsingModel(string $model): string
    {
        $exploded = explode('\\', $model);
        $prefix = ($explodedCount = count($exploded)) > 3 ?
            array_slice($exploded, 2, $explodedCount - 1) :
            [last($exploded)];

        return strtolower(implode($this->separator, $prefix));
    }

    /**
     * Make basic permissions defined in config file for given model in given or default guard.
     *
     * @param string      $model
     * @param string|null $guard
     *
     * @return array
     */
    private function makeBasicPermissions(string $model, string $guard = null): array
    {
        $result = [];
        $guard = $this->resolveGuard($guard);
        $prefix = $this->makePrefixUsingModel($model);

        foreach (horus_config('basic_permissions', []) as $permission) {
            $result[] = [
                'name'       => "{$prefix}{$this->separator}{$permission}",
                'guard_name' => $guard,
            ];
        }

        return $result;
    }

    /**
     * Make custom permissions for given model in given or default guard.
     *
     * @param array       $permissions
     * @param string      $model
     * @param string|null $guard
     *
     * @return array
     */
    private function makeCustomPermissions(array $permissions, string $model, string $guard = null): array
    {
        $result = [];
        $guard = $this->resolveGuard($guard);
        $prefix = $this->makePrefixUsingModel($model);

        foreach ($permissions as $permission) {
            $result[] = [
                'name'       => "{$prefix}{$this->separator}{$permission}",
                'guard_name' => $guard,
            ];
        }

        return $result;
    }

    /**
     * Flatten the data with depth of 1.
     *
     * @param array $data
     *
     * @return array
     */
    private function flatten(array $data): array
    {
        $merged = [];
        foreach ($data as $datum) {
            $merged = array_merge($merged, $datum);
        }

        return $merged;
    }

    /**
     * Check data has unique names.
     *
     * @param array $data
     *
     * @return array
     */
    private function unique(array $data): array
    {
        $unique = [];
        foreach ($data as $value) {
            $unique[$value['name']] = $value;
        }

        return array_values($unique);
    }

    /**
     * If role was string, resolve it to related model instance.
     *
     * @param string|Role $role
     *
     * @return Role
     */
    private function resolveRole(string|Role $role): Role
    {
        return is_string($role) ?
            Role::findByName($role) :
            $role;
    }

    /**
     * If guard is null, return the default guard.
     *
     * @param string|null $guard
     *
     * @return string
     */
    private function resolveGuard(?string $guard): string
    {
        return $guard ?? config('auth.defaults.guard');
    }
}

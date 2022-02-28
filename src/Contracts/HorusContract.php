<?php

    namespace Hans\Horus\Contracts;

    use Illuminate\Support\Collection;
    use Hans\Horus\Models\Permission;
    use Hans\Horus\Models\Role;

    interface HorusContract {
        public function findRole( Role|string|int $role ): Role;

        public function findAllRoles(): Collection;

        public function findAnyRoles( Role|string|int ...$roles ): Collection;

        public function findPermission( Permission|int|string $permission ): Permission;

        public function findAllPermissions(): Collection;

        public function findAnyPermissions( Permission|string|int ...$permissions ): Collection;

    }

<?php

    namespace Hans\Horus\Models\Traits;

    use Hans\Horus\Models\Permission;
    use Hans\Horus\Models\Role;
    use Illuminate\Database\Eloquent\Relations\MorphToMany;

    trait HasRelations {
        public function permissions(): MorphToMany {
            return $this->morphToMany( Permission::class, 'permissionable' );
        }

        public function roles(): MorphToMany {
            return $this->morphToMany( Role::class, 'rolable' );
        }
    }

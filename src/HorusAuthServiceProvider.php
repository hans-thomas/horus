<?php

    namespace Hans\Horus;

    use Hans\Horus\Models\Permission;
    use Hans\Horus\Models\Role;
    use Hans\Horus\Policies\PermissionPolicy;
    use Hans\Horus\Policies\RolePolicy;
    use Illuminate\Foundation\Support\Providers\AuthServiceProvider;

    class HorusAuthServiceProvider extends AuthServiceProvider {
        /**
         * The policy mappings for the application.
         *
         * @var array
         */
        protected $policies = [
            Role::class       => RolePolicy::class,
            Permission::class => PermissionPolicy::class,

        ];

        /**
         * Register any authentication / authorization services.
         *
         * @return void
         */
        public function boot() {
            $this->registerPolicies();

        }
    }

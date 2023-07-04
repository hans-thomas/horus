# Horus

[![codecov](https://codecov.io/gh/hans-thomas/horus/branch/master/graph/badge.svg?token=X1D6I0JLSZ)](https://codecov.io/gh/hans-thomas/horus)
![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/hans-thomas/horus/php.yml)
![GitHub top language](https://img.shields.io/github/languages/top/hans-thomas/horus)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/hans-thomas/horus)

it's a roles and permissions management system for laravel.

features:

- easy-to-use
- no dependency
- direct permissions
- using `Horus` create roles and permissions easier
- super permissions

# Table of contents

- [Installation](#installation)
- [Usage](#usage)
    - [HasRole trait](#hasrole-trait)
    - [HasPermission trait](#haspermission-trait)
    - [Horus facade](#horus-facade)

## Installation

install the package via composer:

```shell
composer require hans-thomas/horus
```

## Usage

to start using Horus, you just need to use `HasRoles` and `HasRelations` traits.

### HasRole trait

1. `assignRole`: assigns a role to the user.
2. `hasRole`: checks the user that has a specific role or not.
3. `hasAnyRole`: checks the user that has any of roles that specified.
4. `getRoleName`: gets the user role name.
5. `getRole`: gets the user's role.

### HasPermission trait

1. `hasPermissionTo`: checks if the user has the specified permission.
2. `hasAnyPermission`: checks if the user has any of specified permissions or not.
3. `hasAllPermissions`: checks if the user has all of specified permissions or not.
4. `hasDirectPermission`: checks if the user has a direct permission.
5. `hasAnyDirectPermission`: checks if the user has any of specified direct permissions.
6. `hasAllDirectPermissions`: checks if the user has all of specified direct permissions.
7. `getPermissionsViaRoles`: returns the permissions that given to the user by its role.
8. `getDirectPermissions`: returns the permissions that given to the user directly.
9. `getAllPermissions`: returns all permissions that user has.
10. `givePermissionTo`: give a permission(s) to the user.
11. `syncPermissions`:syncs permissions of a user.
12. `revokePermissionTo`: revokes a permission from the user.
13. `getPermissionsName`: returns permission's name.

### Horus facade

1. you can create roles for an area

```php
Horus::createRoles( [
    RolesEnum::DEFAULT_ADMINS, // roles name
], AreasEnum::ADMIN  // you can determine an area for a set of roles. you can assign permissions with same area to a role
);
```

2. creating permissions

```php
Horus::createPermissions( [
    // creates basic permissions for the given model plus additional permissions [ 'viewRelation', 'viewAuthor' ]
    // you can Model::class => 'permission' if you have one additional permission
    User::class => [
        'viewHorizon',
    ],
], AreasEnum::ADMIN // specified area for permissions
);
```

3. Assigning permissions to roles

```php
// assign permissions to the given roles
Horus::assignPermissionsToRole( Role::findByName( RolesEnum::DEFAULT_ADMINS, AreasEnum::ADMIN ), [
    User::class => [
        '*', // assigns basic permissions
        'viewHorizon', // assigns viewHorizon custom permission
    ]
], AreasEnum::ADMIN // determines the permissions' area for assigning to the role
);
```

> notice : role must be in same area as permissions are

4. Creating super permissions

if a user has a super permission, this means the user cans do anything.

```php
Horus::createSuperPermissions( [
    '*', // permissions to do any action on any model
    User::class => '*', // permissions to do anything on User model
], AreasEnum::ADMIN ); // area for super permissions
```

5. Assigning a super permission

```php
Horus::assignSuperPermissionsToRole( Role::findByName( RolesEnum::DEFAULT_ADMINS, AreasEnum::ADMIN ), [
    '*', // permissions to do anything on any model
    User::class, // permissions to do anything on User model
] );
```
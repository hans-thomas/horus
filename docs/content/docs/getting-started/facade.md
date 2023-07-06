---
title: 'Facade'
date: 2023-07-06
weight: 2
summary: Introduction to horus facade class.
---

`Horus` package provides a facade class to make working with this package easy. this facade class contains several
methods
that we are going to introduce them.

## createRoles

This method receives an array of roles and create them for you. if you don't pass a guard name as second parameter, the
default guard will be used.

```php
use Hans\Horus\Facades\Horus;

$roles = [ 'admin', 'user' ];

Horus::createRoles( [ 'admin' ] );
Horus::createRoles( [ 'user' ], 'customers' )
```

## createPermissions

Creates basic and custom permissions for your model.

```php
use Hans\Horus\Facades\Horus;

$permissions = [
    Post::class, // creates all basic permissions
    Category::class => [ 'viewPosts' ], // creates only viewPosts permission
    Comment::class  => [ '*', 'viewPost' ], // creates basics + viewPost permissions
    Article::class  => 'viewComments', // creates only viewComments permission
    Tag::class      => '*', // same as first one, creates all basic permissions
];

Horus::createPermissions( $permissions );
Horus::createPermissions( $permissions, 'customers' );
```

## createSuperPermissions

This feature supported by [Sphinx](https://github.com/hans-thomas/sphinx). let's assume we have basic permissions
for `Post::class` model. we can create a super permission for this model and assign that to a role. every user that has
the role, can do all actions that includes basics and custom permissions for the related model.

```php
use Hans\Horus\Facades\Horus;

Horus::createSuperPermissions(
    [
        Post::class,
    ]
);

Horus::createSuperPermissions(
    [
        Post::class,
    ],
    'customers'
);
```

This method will create a permission named `post-*` that is equals to `post-view` or other permissions related
to `Post::class` model.

## assignPermissionsToRole

Using this method, you can set a bunch of permissions ad once.

```php
use Hans\Horus\Facades\Horus;

Horus::createRoles( ['admin'] )

Horus::createPermissions( [ Post::class, Category::class ] );

Horus::assignPermissionsToRole(
    Role::findByName( 'admin' ), // or just pass the role's name
    [
        Category::class
    ]
);
```

> Notice: role and permissions must be in a same guard.

## assignSuperPermissionsToRole

This method can assign multiple super permissions to a role at once.

```php
use Hans\Horus\Facades\Horus;
use Spatie\Permission\Models\Role;

Horus::createRoles( ['admin'] )

Horus::createSuperPermissions( [ Post::class, Category::class ] );

Horus::assignSuperPermissionsToRole(
    Role::findByName( 'admin' ), // or just pass the role's name
    [
        Post::class,
        Category::class
    ]
);
```

> Notice: role and permissions must be in a same guard.

## createPermissionsUsingPolicy

To authorize an action you need to create a method on related policy. so, actually you can create your permissions based
on your policy class for related model.

```php
use Hans\Horus\Facades\Horus;

Horus::createPermissionsUsingPolicy(
    PostPolicy::class,
    Post::class,
);
```

You can create permission in a specific guard or ignore some methods of your policy.

```php
use Hans\Horus\Facades\Horus;

Horus::createPermissionsUsingPolicy(
    PostPolicy::class,
    Post::class,
    'customers', // your custom guard
    [ 'viewComments' ] // methods list to ignore
);
```

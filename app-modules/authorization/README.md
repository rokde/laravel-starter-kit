# Authorization

This module provides authorization through permissions. So permissions will be checked through the Laravel's Gate facade
and were previously defined via source code.

As a robust source we use Spatie's excellent `laravel-permission` package with additional sugar to make it easier to use
permission-based authorization.

The roles are groups of permissions and each user can have multiple roles assigned - all scoped within the current
workspace the user has active.

Additionally we want to support the domain driven architecture - and every domain should be able to register the
provided permissions itself.

On the frontend a user is then able to combine permissions to form a role within a workspace. And each user can get
multiple roles assigned.

We want to avoid giving permissions directly to a user.

## Register a permission

In your Module Service Provider please tell the PermissionManager about your permissions. He will then manage the
technical persistence of that. This is an additive system in mind.

Please use active deleting of unused permissions to get the storage clean.

## Anatomy of a permission

A permission should contain the resource and the action to work on it. So to make it clear we want it like this:

```php
//             RESOURCE  ACTION
$permission = 'workspace.create';
$permission = 'workspace.update';
```

Do not just think in CRUD verbs. Try to think in domain language is even better:

```php
//             RESOURCE          ACTION
$permission = 'calculation_taxes.process';
$permission = 'workspace_members.invite';
//             instead of
$permission = 'workspace_members.create';
```

To make that even clearer on the frontend side you have to add a descriptive description on registering the permission.

## Checking the permission

A user can have multiple roles and so he is able to do a lot of by the assigned indirect permissions.

Use the Laravel based system with `$user->can()` method on user or the `middleware('can:...')` for routes or the form
requests `authorize()` methods - as well as the `Gate` facade in your code.


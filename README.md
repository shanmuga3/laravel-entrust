# Laravel Entrust (Laravel 5, 6, 7, 8 and 9 Package)
Handle Role-based Permissions for your Laravel application.

> **Note:** You Must Use version 1.x If you are using below Laravel 8.

> **Note:** You Must Use version 2.x If you are using below Laravel 9.

## Contents

- [Installation & Configuration](#installation)
- [Usage](#usage)
    - [Concepts](#concepts)
        - [Checking for Roles & Permissions](#checking-for-roles--permissions)
        - [User ability](#user-ability)
    - [Middleware](#middleware)
- [Troubleshooting](#troubleshooting)
- [License](#license)
- [Contribution guidelines](#contribution-guidelines)
- [Additional information](#additional-information)

## Installation

1) You can install the Laravel-entrust package via composer:

```bash
composer require shanmuga/laravel-entrust
```
> **Note:** You Can Skip step 2 and 3 If you are using above Laravel 5.5.

2) Open your `config/app.php` and add the following to the `providers` array:

```php
Shanmuga\LaravelEntrust\LaravelEntrustServiceProvider::class,
```

3) In the same `config/app.php` and add the following to the `aliases ` array: 

```php
'LaravelEntrust'   => Shanmuga\LaravelEntrust\Facades\LaravelEntrustFacade::class,
```

4) Run the command below to publish the package config files `config/entrust.php` and `config/entrust_seeder.php`

```shell
php artisan vendor:publish --tag="LaravelEntrust"
```

5) Open your `config/entrust.php` and add the following to it:

Name of the migration file to be generated
```php
'migrationSuffix' => 'laravel_entrust_setup_tables',
```
Model and Table Used for Authorization
```php
'user_model' => 'App\Models\User',
'user_table' => 'users',
```
Name of the Models Used for Role and Permission
```php
'models' => [
    'role'          => 'App\Models\Role',
    'permission'    => 'App\Models\Permission',
],
```
Default Guard to perform user authentication, You Can also pass it manually when checking it.
```php
'defaults' => [
     'guard'          => 'web',
 ],
```
You can also use multiple guards: 
```php
'defaults' => [
     'guard'          => ['web', 'api'],
 ],
```
Table names used for roles and permissions
```php
'tables' => [
    'roles'             => 'roles',
    'permissions'       => 'permissions',
    'role_user'         => 'role_user',
    'permission_role'   => 'permission_role',
],
```
Foriegn keys used for roles and permissions
```php
'foreign_keys' => [
    'user' => 'user_id',
    'role' => 'role_id',
    'permission' => 'permission_id',
],
```
Middleware Setup for custom message, register set to true for register automatically, **Handling** is which handler to be used either *abort* or *redirect*. you can also configure what message should be display if authorization failed.
```php
'middleware' => [
    'register' => true,
    'handling' => 'abort',
    'handlers' => [
        'abort' => [
            'code' => 403,
            'message' => 'You don\'t Have a permission to Access this page.'
        ],
        'redirect' => [
            'url' => '/',
            'message' => [
                'key' => 'error',
                'content' => 'You don\'t Have a permission to Access this page'
            ]
        ],
    ],
],
```

6)  Run the following command to generate migration and seed

```bash
php artisan laravel-entrust:setup
```
> See [Entrust Seeder](#entrust_seeder) Configuration to learn more about create permissions.

7)  Finally Add the LaravelEntrustUserTrait to existing `User` model. For example:
```php
<?php

use Shanmuga\LaravelEntrust\Traits\LaravelEntrustUserTrait;

class User extends Model
{
    use LaravelEntrustUserTrait; // add this trait to your user model

    ...
}
```
This will enable the relation with `Role` and add the following methods `roles()`, `hasRole($name)`, `hasPermission($permission)`, and `ability($roles, $permissions, $options)` within your `User` model.

Don't forget to dump composer autoload

```bash
composer dump-autoload
```

**And you are ready to go.**

#### Soft Deleting

The default migration takes advantage of `onDelete('cascade')` clauses within the pivot tables to remove relations when a parent record is deleted. If for some reason you cannot use cascading deletes in your database, the EntrustRole and EntrustPermission classes, and the HasRole trait include event listeners to manually delete records in relevant pivot tables. In the interest of not accidentally deleting data, the event listeners will **not** delete pivot data if the model uses soft deleting. However, due to limitations in Laravel's event listeners, there is no way to distinguish between a call to `delete()` versus a call to `forceDelete()`. For this reason, **before you force delete a model, you must manually delete any of the relationship data** (unless your pivot tables uses cascading deletes). For example:

```php
$role = Role::findOrFail(1); // Pull back a given role

// Regular Delete
$role->delete(); // This will work no matter what

// Force Delete
$role->users()->sync([]); // Delete relationship data
$role->permissions()->sync([]); // Delete relationship data

$role->forceDelete(); // Now force delete will work regardless of whether the pivot table has cascading delete
```

## Usage

### Concepts
Let's start by configuring `entrust_seeder` to create role and permissions:
Your  `config/laratrust_seeder.php`  file looks like this:

```php
<?php
return [
    'role_structure' => [
        'admin' => [
            'users' => 'c,r,u,d',
            'admin' => 'c,r,u,d',
            'profile' => 'r,d'
        ],
        'subadmin' => [
            'users' => 'c,r,u',
            'profile' => 'r,u'
        ],
    ],
    'user_roles' => [
        'admin' => [
            ['name' => "Admin", "email" => "admin@demo.com", "password" => '123456'],
        ],
    ],
    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete',
    ],
];
```

Now Users are created and alse roles and it's permissions are assigned to that users.
You Can also attach and detach role is as easy as:

```php
$user = User::where('username', 'shan')->first();

// role attach alias
$user->attachRole($admin); // parameter can be an Role object, array, or id

// or eloquent's original technique
$user->roles()->attach($admin->id); // id only
```
#### Checking for Roles & Permissions

Now we can check for roles and permissions simply by doing:

```php
$user->hasRole('owner');   // false
$user->hasRole('admin');   // true
$user->hasPermission('edit-user');   // false
$user->hasPermission('create-post'); // true
```

Both `hasRole()` and `hasPermission()` can receive an array of roles & permissions to check:

```php
$user->hasRole(['owner', 'admin']);       // true
$user->hasPermission(['edit-user', 'create-post']); // true
```
By default, if any of the roles or permissions are present for a user then the method will return true.
Passing `true` as a second parameter instructs the method to require **all** of the items:

```php
$user->hasRole(['owner', 'admin']);             // true
$user->hasRole(['owner', 'admin'], true);       // false, user does not have admin role
$user->hasPermission(['edit-user', 'create-post']);       // true
$user->hasPermission(['edit-user', 'create-post'], true); // false, user does not have edit-user permission
```
You can have as many `Role`s as you want for each `User` and vice versa.

The `Entrust` class has shortcuts to both `can()` and `hasRole()` for the currently logged in user:

```php
Entrust::hasRole('role-name');
Entrust::can('permission-name');
Entrust::hasPermission('permission-name');

// is identical to
Auth::user()->hasRole('role-name');
Auth::user()->can('permission-name');
Auth::user()->hasPermission('permission-name');
Auth::user()->isAbleTo('permission-name');
```

You can also use placeholders (wildcards) to check any matching permission by doing:

```php
// match any permission about users
$user->hasPermission("*-users"); // true
```

#### User ability

More advanced checking can be done using the awesome `ability` function.
It takes in three parameters (roles, permissions, options):
- `roles` is a set of roles to check.
- `permissions` is a set of permissions to check.

Either of the roles or permissions variable can be a comma separated string or array:

```php
$user->ability(['admin', 'owner'], ['create-user', 'edit-user']);
// or
$user->ability('admin,owner', 'create-user,edit-user');
```
This will check whether the user has any of the provided roles and permissions.
In this case it will return true since the user is an `admin` and has the `create-user` permission.

The third parameter `validateAll` is a boolean flag to set whether to check all the values for true, or to return true if at least one role or permission is matched. It is optional and by default it is `false`.

### Middleware
You can use a middleware to filter routes and route groups by permission or role
```php
Route::group(['prefix' => 'admin', 'middleware' => ['role:admin']], function() {
    Route::get('/', [AdminController::class,'welcome']);
    Route::get('/view', [AdminController::class,'manageAdmins'])->middleware('permission:view-admin');
});
```

It is possible to use pipe symbol as *OR* operator:
```php
'middleware' => ['role:admin|root']
```
To emulate *AND* functionality just use multiple instances of middleware
```php
'middleware' => ['role:owner', 'role:writer']
```
## Troubleshooting

If you encounter an error when doing the migration that looks like:
```
SQLSTATE[HY000]: General error: 1005 Can't create table 'laravelbootstrapstarter.#sql-42c_f8' (errno: 150)
    (SQL: alter table `role_user` add constraint role_user_user_id_foreign foreign key (`user_id`)
    references `users` (`id`)) (Bindings: array ())
```
This occur when use laravel less than 5.8. It uses `Integer` for migration autoIncrement but laravel entrust uses `BigInteger`. So make sure both are  same..

When trying to use the EntrustUserTrait methods, you encounter the error which looks like

    Class name must be a valid object or a string

then probably you don't have published Entrust assets or something went wrong when you did it.
First of all check that you have the `entrust.php` file in your `config` directory.
If you don't, then try `php artisan vendor:publish --tag=LaravelEntrust` and, if it does not appear, manually copy the `/vendor/shanmuga/laravel-entrust/src/config/entrust.php` file in your config directory.

If your app uses a custom namespace then you'll need to tell entrust where your `permission` and `role` models are, you can do this by editing the config file in `config/entrust.php`

```
'models' => [
     'role'          => 'App\Models\Role',
     'permission'    => 'App\Models\Permission',
 ]
 ```
## License

Laravel-Entrust is free software distributed under the terms of the MIT license.

## Contribution guidelines

Support follows PSR-1 and PSR-4 PHP coding standards, and semantic versioning.

Please report any issue you find in the issues page.  
Pull requests are always welcome.

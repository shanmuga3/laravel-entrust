<?php

/**
 * This file is part of Laravel Entrust,
 * Handle Role-based Permissions for Laravel.
 *
 * @license     MIT
 * @package     Shanmuga\LaravelEntrust
 * @category    Facades
 * @author      Shanmugarajan
 */

namespace Shanmuga\LaravelEntrust\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool ability(array|string $roles, array|string $permissions, array $options = [])
 * @method static bool can(string $permission, bool $requireAll = false)
 * @method static bool canAndOwns(string|array $permission, Object $thing, array $options = [])
 * @method static bool hasRole(string $role, bool $requireAll = false)
 * @method static bool hasRoleAndOwns(string|array $role, Object $thing, array $options = [])
 * @method static bool owns(Object $thing, string|null $foreignKeyName = null)
 * @method static \Illuminate\Auth\UserInterface|null user()
 */
class LaravelEntrustFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel_entrust';
    }
}

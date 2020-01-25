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

namespace Shanmuga\LaravelEntrust\Traits;

use Illuminate\Support\Facades\Config;

trait LaravelEntrustPermissionTrait
{
    /**
     * Boot the permission model
     * Attach event listener to remove the many-to-many records when trying to delete
     * Will NOT delete any records if the permission model uses soft deletes.
     *
     * @return void|bool
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($permission) {
            if (!method_exists(Config::get('entrust.models.permission'), 'bootSoftDeletes')) {
                $permission->roles()->sync([]);
            }
        });
    }

    /**
     * Many-to-Many relations with role model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(
            Config::get('entrust.models.role'),
            Config::get('entrust.tables.permission_role'),
            Config::get('entrust.foreign_keys.permission'),
            Config::get('entrust.foreign_keys.role')
        );
    }
}
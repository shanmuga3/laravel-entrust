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
<?php

/**
 * This file is part of Laravel Entrust,
 * Handle Role-based Permissions for Laravel.
 *
 * @license     MIT
 * @package     Shanmuga\LaravelEntrust
 * @category    Middleware
 * @author      Shanmugarajan
 */

namespace Shanmuga\LaravelEntrust\Middleware;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;

class LaravelEntrustMiddleware
{
    const DELIMITER = '|';

    /**
     * Check if the request has authorization to continue.
     *
     * @param  string $type
     * @param  string $rolesPermissions
     * @param  string $options|null
     * @return boolean
     */
    protected function authorization($type, $rolesPermissions, $options = null)
    {
        $guard = isset($options[0]) ? $options[0] : Config::get('entrust.defaults.guard');

        $method = $type == 'roles' ? 'hasRole' : 'hasPermission';

        if (!is_array($rolesPermissions)) {
            $rolesPermissions = explode(self::DELIMITER, $rolesPermissions);
        }

        // Uses multiple guards
        if (is_array($guard)) {
            $isAuthorized = false;

            foreach ($guard as $one) {
                $isAuthorized =
                    !Auth::guard($one)->guest() &&
                    Auth::guard($one)
                        ->user()
                        ->$method($rolesPermissions);

                // Prevents the next one to invalidate the authorization
                if ($isAuthorized) {
                    return $isAuthorized;
                }
            }

            return $isAuthorized;
        } else {
            return !Auth::guard($guard)->guest() &&
                Auth::guard($guard)
                    ->user()
                    ->$method($rolesPermissions);
        }
    }

    /**
     * The request is unauthorized, so it handles the aborting/redirecting.
     *
     * @return \Illuminate\Http\Response
     */
    protected function unauthorized()
    {
        $handling = Config::get('entrust.middleware.handling');
        $handler = Config::get("entrust.middleware.handlers.{$handling}");

        if ($handling == 'abort') {
            $defaultMessage = 'User does not have any of the necessary access rights.';
            return App::abort($handler['code'], $handler['message'] ?? $defaultMessage);
        }

        $redirect = Redirect::to($handler['url']);
        if (!empty($handler['message']['content'])) {
            $redirect->with($handler['message']['key'], $handler['message']['content']);
        }

        return $redirect;
    }
}

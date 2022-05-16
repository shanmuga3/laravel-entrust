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

use Illuminate\Support\Facades\Auth;

class LaravelEntrustAbility extends LaravelEntrustMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param $roles
     * @param $permissions
     * @param Array|null $options
     * @return mixed
     */
    public function handle($request, \Closure $next, $roles, $permissions, $options = null)
    {
        if (!is_array($roles)) {
            $roles = explode(self::DELIMITER, $roles);
        }

        if (!is_array($permissions)) {
            $permissions = explode(self::DELIMITER, $permissions);
        }

        $guard = (is_array($options) && isset($options[0])) ? $options[0] : Config::get('entrust.defaults.guard');
        $validateAll = (is_array($options) && isset($options[1])) ? $options[1] : false;

        if (!is_bool($validateAll)) {
            $validateAll = filter_var($validateAll, FILTER_VALIDATE_BOOLEAN);
        }

        if (Auth::guard($guard)->guest() || !Auth::guard($guard)->user()->ability($roles, $permissions, [ 'validate_all' => $validateAll ])) {
            return $this->unauthorized();
        }

        return $next($request);
    }
}

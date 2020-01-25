<?php

/**
 * This file is part of Laravel Entrust,
 * Handle Role-based Permissions for Laravel.
 *
 * @license     MIT
 * @package     Shanmuga\LaravelEntrust
 * @category    Models
 * @author      Shanmugarajan
 */

namespace Shanmuga\LaravelEntrust\Middleware;

class LaravelEntrustRole extends LaratrustMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Closure $next
     * @param  $roles
     * @return mixed
     */
    public function handle($request, \Closure $next, $roles)
    {
        if (!is_array($roles)) {
            $roles = explode(self::DELIMITER, $roles);
        }

        if (!$this->authorization('roles', $roles)) {
            return $this->unauthorized();
        }

        return $next($request);
    }
}
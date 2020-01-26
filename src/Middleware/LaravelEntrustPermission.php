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

class LaravelEntrustPermission extends LaravelEntrustMiddleware
{
    /**
     * Handle incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure $next
     * @param  string  $permissions
     * @param  mixed  $options
     * @return mixed
     */
    public function handle($request, \Closure $next, $permissions, ...$options)
    {
        if (!$this->authorization('permissions', $permissions,$options)) {
            return $this->unauthorized();
        }

        return $next($request);
    }
}
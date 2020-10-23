<?php

/**
 * This file is part of Laravel Entrust,
 * Handle Role-based Permissions for Laravel.
 * 
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Migration Suffix
    |--------------------------------------------------------------------------
    |
    | This is the array that contains the information of the user models.
    | This information is used in the add-trait command, and for the roles and
    | permissions relationships with the possible user models.
    |
    | The key in the array is the name of the relationship inside the roles and permissions.
    |
    */
    'migrationSuffix' => 'laravel_entrust_setup_tables',

    /*
    |--------------------------------------------------------------------------
    | Laravel Entrust User Model
    |--------------------------------------------------------------------------
    |
    | This is the users Model used by the application to handle ACL.
    | If you want the Laravel Entrust User Model to be in a different namespace or
    | to have a different name, you can do it here.
    |
    */
    'user_model' => 'App\Models\User',

    /*
    |--------------------------------------------------------------------------
    | Laravel Entrust User Table
    |--------------------------------------------------------------------------
    |
    | This is the users table used by the application to save users to the database.
    | If you want the Laravel Entrust User Table to be in a different namespace or
    | to have a different name, you can do it here.
    |
    */
    'user_table' => 'users',

    /*
    |--------------------------------------------------------------------------
    | Laravel Entrust Models
    |--------------------------------------------------------------------------
    |
    | These are the models used by Laravel Entrust to define the roles and permissions.
    | If you want the Laravel Entrust models to be in a different namespace or
    | to have a different name, you can do it here.
    |
    */
    'models' => [
        'role'          => 'App\Models\Role',
        'permission'    => 'App\Models\Permission',
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel Entrust Default Configurations
    |--------------------------------------------------------------------------
    |
    | These Configurations are used by Laravel Entrust to define the defaults
    | If you want the Laravel Entrust to be in a different guards you can do it here.
    |
    */
    'defaults' => [
        'guard'          => 'web',
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel Entrust Tables
    |--------------------------------------------------------------------------
    |
    | These are the tables used by Laravel Entrust to store all the authorization data.
    |
    */
    'tables' => [
        'roles'             => 'roles',
        'permissions'       => 'permissions',
        'role_user'         => 'role_user',
        'permission_role'   => 'permission_role',
    ],

    /*
    |--------------------------------------------------------------------------
    | Laratrust Foreign Keys
    |--------------------------------------------------------------------------
    |
    | These are the foreign keys used by laratrust in the intermediate tables.
    |
    */
    'foreign_keys' => [
        'user' => 'user_id',
        'role' => 'role_id',
        'permission' => 'permission_id',
    ],

    /*
    |--------------------------------------------------------------------------
    | Laravel Entrust Middleware
    |--------------------------------------------------------------------------
    |
    | This configuration helps to customize the Laravel Entrust middleware behavior.
    |
    */
    'middleware' => [
        /**
         * Define if the laratrust middleware are registered automatically in the service provider
         */
        'register' => true,

        /**
         * Method to be called in the middleware return case.
         * Available: abort|redirect
         */
        'handling' => 'abort',

        /**
         * Handlers for the unauthorized method in the middlewares.
         * The name of the handler must be the same as the handling.
         */
        'handlers' => [
            /**
             * Aborts the execution with a 403 code and allows you to provide the response text
             */
            'abort' => [
                'code' => 403,
                'message' => 'You don\'t Have a permission to Access this page.'
            ],

            /**
             * Redirects the user to the given url.
             * If you want to flash a key to the session,
             * you can do it by setting the key and the content of the message
             * If the message content is empty it won't be added to the redirection.
             */
            'redirect' => [
                'url' => '/',
                'message' => [
                    'key' => 'error',
                    'content' => 'You don\'t Have a permission to Access this page'
                ]
            ],
        ],
    ],
];
<?php

/**
 * This file is part of Laravel Entrust,
 * Handle Role-based Permissions for Laravel.
 *
 * @license     MIT
 * @package     Shanmuga\LaravelEntrust
 * @category    Commands
 * @author      Shanmugarajan
 */

namespace Shanmuga\LaravelEntrust\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class SetupCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'laravel-entrust:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup migration and Seed for Laravel Entrust';

    /**
     * Commands to call with their description.
     *
     * @var array
     */
    protected $calls = [
        'laravel-entrust:migration' => 'Creating migration...',
        'laravel-entrust:seeder'    => 'Creating Seeds...',
        'laravel-entrust:role'      => 'Creating Roles Model...',
        'laravel-entrust:permission'=> 'Creating Permission Model...',
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->calls as $command => $info) {
            $this->line(PHP_EOL.$info);
            $this->call($command);
        }
    }
}
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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

class MakeSeederCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'laravel-entrust:seeder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates the seeder following the Laravel Entrust specifications';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (file_exists($this->seederPath())) {
            $this->warn("The LaravelEntrustSeeder file already exists. Delete the existing one if you want to create a new one.");
            return;
        }

        if ($this->createSeeder()) {
            $this->info("LaravelEntrustSeeder successfully created!");
        }
        else {
            $this->error(
                "Couldn't create seeder.\n".
                "Check the write permissions within the database/seeds directory."
            );
        }
    }

    /**
     * Create the seeder
     *
     * @return bool
     */
    protected function createSeeder()
    {
        $role = Config::get('entrust.models.role', 'App\Role');
        $permission = Config::get('entrust.models.permission', 'App\Permission');
        $user = Config::get('entrust.user_model', ['App\User']);

        $output = $this->laravel->view->make('laravel-entrust::seeder')
            ->with(compact([
                'role',
                'permission',
                'user',
            ]))
            ->render();

        $seederPath = $this->seederPath();
        if (!file_exists($seederPath) && $fs = fopen($seederPath, 'x')) {
            fwrite($fs, $output);
            fclose($fs);
            return true;
        }

        return false;
    }

    /**
     * Get the seeder path.
     *
     * @return string
     */
    private function seederPath()
    {
        return database_path("seeders/LaravelEntrustSeeder.php");
    }
}
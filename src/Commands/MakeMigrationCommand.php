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
use Illuminate\Support\Str;

class MakeMigrationCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'laravel-entrust:migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a migration following the Laravel Entrust specifications.';

    /**
     * Suffix of the migration name.
     *
     * @var string
     */
    protected $migrationSuffix = 'entrust_setup_tables';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info("Laravel Entrust Migration Creation.");
        $this->comment($this->generateMigrationMessage());

        $existingMigrations = $this->alreadyExistingMigrations();
        $defaultAnswer = true;

        if ($existingMigrations) {
            $this->warn($this->getExistingMigrationsWarning($existingMigrations));
            $defaultAnswer = false;
        }

        if (! $this->confirm("Proceed with the migration creation?", $defaultAnswer)) {
            return;
        }

        if ($this->createMigration()) {
            $this->info("Migration created successfully.");
        }
        else {
            $this->error(
                "Couldn't create migration.\n".
                "Check the write permissions within the database/migrations directory."
            );
        }
    }

    /**
     * Create the migration.
     *
     * @return bool
     */
    protected function createMigration()
    {
        $migrationPath = $this->getMigrationPath();
        $migrationSuffix = $this->getMigrationSuffix();
        $entrustClassName = Str::camel($migrationSuffix);
        $output = $this->laravel->view
            ->make('laravel-entrust::migration')
            ->with(['entrust' => Config::get('entrust'),'class' => ucfirst($entrustClassName)])
            ->render();

        if (!file_exists($migrationPath) && $fs = fopen($migrationPath, 'x')) {
            fwrite($fs, $output);
            fclose($fs);
            return true;
        }

        return false;
    }

    /**
     * Generate the message to display when running the
     * console command showing what tables are going
     * to be created.
     *
     * @return string
     */
    protected function generateMigrationMessage()
    {
        $tables = collect(Config::get('entrust.tables'));

        return "A migration that creates {$tables->implode(', ')} "
            . "tables will be created in database/migrations directory";
    }

    /**
     * Build a warning regarding possible duplication
     * due to already existing migrations.
     *
     * @param  array  $existingMigrations
     * @return string
     */
    protected function getExistingMigrationsWarning(array $existingMigrations)
    {
        if (count($existingMigrations) > 1) {
            $base = "Laratrust migrations already exist.\nFollowing files were found: ";
        } else {
            $base = "Laratrust migration already exists.\nFollowing file was found: ";
        }

        return $base . array_reduce($existingMigrations, function ($carry, $fileName) {
            return $carry . "\n - " . $fileName;
        });
    }

    /**
     * Check if there is another migration
     * with the same suffix.
     *
     * @return array
     */
    protected function alreadyExistingMigrations()
    {
        $matchingFiles = glob($this->getMigrationPath('*'));

        return array_map(function ($path) {
            return basename($path);
        }, $matchingFiles);
    }

    /**
     * Get the migration path.
     *
     * The date parameter is optional for ability
     * to provide a custom value or a wildcard.
     *
     * @param  string|null  $date
     * @return string
     */
    protected function getMigrationPath($date = null)
    {
        $date = $date ?: date('Y_m_d_His');

        $migrationSuffix = $this->getMigrationSuffix();

        return database_path("migrations/${date}_{$migrationSuffix}.php");
    }

    /**
     * Get the migration suffix.
     *
     * @return string
     */
    protected function getMigrationSuffix()
    {
        $migrationSuffix = Config::get('entrust.migrationSuffix');
        return $migrationSuffix ?: $this->migrationSuffix;
    }
}
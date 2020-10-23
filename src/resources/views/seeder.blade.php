<?php echo '<?php' ?>


namespace Database\Seeders;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class LaravelEntrustSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Truncating Roles, Permissions and Users tables');
        $this->truncateEntrustTables();

        $config = config('entrust_seeder.role_structure');
        $userRoles = config('entrust_seeder.user_roles');
        $mapPermission = collect(config('entrust_seeder.permissions_map'));

        foreach ($config as $key => $modules) {

            // Create a new role
            $role = \{{ $role }}::create([
                'name' => $key,
                'display_name' => ucwords(str_replace('_', ' ', $key)),
                'description' => ucwords(str_replace('_', ' ', $key))
            ]);
            $permissions = [];

            $this->command->info('Creating Role '. strtoupper($key));

            // Reading role permission modules
            foreach ($modules as $module => $value) {

                foreach (explode(',', $value) as $p => $perm) {

                    $permissionValue = $mapPermission->get($perm);

                    $permissions[] = \{{ $permission }}::firstOrCreate([
                        'name' => $permissionValue . '-' . $module,
                        'display_name' => ucfirst($permissionValue) . ' ' . ucwords(str_replace('_', ' ', $module)),
                        'description' => ucfirst($permissionValue) . ' ' . ucwords(str_replace('_', ' ', $module)),
                    ])->id;

                    $this->command->info('Creating Permission to '.$permissionValue.' for '. $module);
                }
            }

            // Attach all permissions to the role
            $role->permissions()->sync($permissions);

            if(isset($userRoles[$key])) {
                $this->command->info("Creating '{$key}' users");

                $role_users  = $userRoles[$key];

                foreach ($role_users as $role_user) {
                    if(isset($role_user["password"])) {
                        $role_user["password"] = Hash::make($role_user["password"]);
                    }
                    $user = \{{ $user }}::create($role_user);
                    $user->attachRole($role);
                }
            }
        }
    }

    /**
     * Truncates all the entrust tables and the users table
     *
     * @return  void
     */
    public function truncateEntrustTables()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('{{ config('entrust.tables.permission_role') }}')->truncate();
        DB::table('{{ config('entrust.tables.role_user') }}')->truncate();
        DB::table('{{ config('entrust.user_table') }}')->truncate();

        \{{ config('entrust.models.role') }}::truncate();
        \{{ config('entrust.models.permission') }}::truncate();

        Schema::enableForeignKeyConstraints();
    }
}
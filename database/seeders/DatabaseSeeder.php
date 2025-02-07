<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Roles\Database\Seeders\RolesDatabaseSeeder;
use Modules\Roles\Models\Role;
use Modules\RolesAndPermissions\Database\Seeders\PermissionDatabaseSeeder;
use Modules\RolesAndPermissions\Database\Seeders\RoleDatabaseSeeder;
use Modules\RolesAndPermissions\Models\Permission;
use Modules\Task\Database\Seeders\TaskDatabaseSeeder;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            UserDatabaseSeeder::class,
            PermissionDatabaseSeeder::class,
            RoleDatabaseSeeder::class,
            TaskDatabaseSeeder::class,
        ]);
    }
}

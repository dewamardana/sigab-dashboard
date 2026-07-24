<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleAndAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat role dasar
        $superadmin = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin_lokasi', 'guard_name' => 'web']);

        // Buat akun superadmin pertama
        $admin = User::firstOrCreate(
            ['email' => 'timedooracademydewa@gmail.com'],
            [
                'name' => 'Superadmin SIGAB',
                'password' => bcrypt('12345678'),
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole($superadmin);

        $this->command->info('Superadmin dibuat: admin@sigab.test / ubah_password_ini');
    }
}

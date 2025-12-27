<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AdminUser::create([
            'name' => 'Administrador',
            'email' => 'admin@appoficina.local',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $this->command->info('✅ Admin user created: admin@appoficina.local / password');
    }
}

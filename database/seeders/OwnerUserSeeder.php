<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OwnerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'owner@arigita.test'],
            [
                'name' => 'Owner Ari Gita',
                'password' => bcrypt('password'),
                'role' => 'owner',
                'phone' => '080000000000',
                'is_active' => true,
            ]
        );
    }
}

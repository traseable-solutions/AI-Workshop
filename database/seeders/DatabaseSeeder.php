<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Pat Secretary',
            'email' => 'ps@example.com',
            'role' => 'permanent_secretary',
            'level' => 12,
            'position' => 'Permanent Secretary',
            'duty_station' => 'Head Office',
        ]);

        $manager = User::factory()->create([
            'name' => 'Maria Manager',
            'email' => 'manager@example.com',
            'role' => 'manager',
            'level' => 8,
            'tpf' => 'TPF-1008',
            'position' => 'Head of Division',
            'duty_station' => 'Head Office',
        ]);

        User::factory()->create([
            'name' => 'Eve Employee',
            'email' => 'employee1@example.com',
            'role' => 'employee',
            'manager_id' => $manager->id,
            'level' => 3,
            'tpf' => 'TPF-1003',
            'position' => 'Clerical Officer',
            'duty_station' => 'Head Office',
        ]);

        User::factory()->create([
            'name' => 'Sam Staff',
            'email' => 'employee2@example.com',
            'role' => 'employee',
            'manager_id' => $manager->id,
            'level' => 6,
            'tpf' => 'TPF-1006',
            'position' => 'Senior Officer',
            'duty_station' => 'Head Office',
        ]);
    }
}

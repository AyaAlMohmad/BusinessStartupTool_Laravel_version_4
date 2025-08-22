<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        // \App\Models\StartupCost::create([
        //     'item' => 'Initial Setup', // أضف قيمة للحقل `item`
        //     'timing' => '2025-01-21', // أضف قيمة للحقل `timing`
        //     'notes' => 'Initial setup costs for the project', // أضف قيمة للحقل `notes`
        //     'category' => 'Other',
        //     'amount' => 4000,
        // ]);

        // \App\Models\FundingSource::create([
        //     'source' => 'Personal',
        //     'amount' => 47473783,
        // ]);
        // $this->call(ResourceSeeder::class);
        $this->call([
            // PermissionSeeder::class,
            ResourceSeeder::class,
            // AdminRoleAndPermissionsSeeder::class,
        ]);
    }
}

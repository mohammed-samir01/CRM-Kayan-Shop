<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            ProductSeeder::class,
            UsersSeeder::class,
            CampaignSeeder::class,
            LeadSeeder::class,
            OrderSeeder::class,
        ]);
    }
}

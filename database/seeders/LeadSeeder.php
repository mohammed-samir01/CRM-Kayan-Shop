<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        $campaigns = Campaign::all();
        $users = User::all();

        if ($campaigns->isEmpty() || $users->isEmpty()) {
            return;
        }

        Lead::factory()->count(50)->make()->each(function ($lead) use ($campaigns, $users) {
            $lead->campaign_id = $campaigns->random()->id;
            $lead->assigned_to = $users->random()->id;
            $lead->save();
        });
    }
}

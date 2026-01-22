<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_campaign_name_must_be_unique_on_creation(): void
    {
        $user = User::factory()->create();
        Campaign::create([
            'name' => 'Existing Campaign',
            'platform' => 'Facebook',
            'ad_type' => 'Image',
            'source' => 'Website',
        ]);

        $response = $this->actingAs($user)->post(route('campaigns.store'), [
            'name' => 'Existing Campaign',
            'platform' => 'TikTok',
            'ad_type' => 'Video',
            'source' => 'WhatsApp',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_campaign_name_must_be_unique_on_update(): void
    {
        $user = User::factory()->create();
        $campaign1 = Campaign::create([
            'name' => 'Campaign 1',
            'platform' => 'Facebook',
            'ad_type' => 'Image',
            'source' => 'Website',
        ]);
        Campaign::create([
            'name' => 'Campaign 2',
            'platform' => 'TikTok',
            'ad_type' => 'Video',
            'source' => 'WhatsApp',
        ]);

        $response = $this->actingAs($user)->put(route('campaigns.update', $campaign1), [
            'name' => 'Campaign 2',
            'platform' => 'Facebook',
            'ad_type' => 'Image',
            'source' => 'Website',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_campaign_can_be_updated_with_same_name(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::create([
            'name' => 'My Campaign',
            'platform' => 'Facebook',
            'ad_type' => 'Image',
            'source' => 'Website',
        ]);

        $response = $this->actingAs($user)->put(route('campaigns.update', $campaign), [
            'name' => 'My Campaign',
            'platform' => 'Instagram', // Changed platform
            'ad_type' => 'Image',
            'source' => 'Website',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertEquals('Instagram', $campaign->fresh()->platform);
    }

    public function test_campaign_platform_must_be_valid_enum(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('campaigns.store'), [
            'name' => 'New Campaign',
            'platform' => 'InvalidPlatform',
            'ad_type' => 'Video',
            'source' => 'WhatsApp',
        ]);

        $response->assertSessionHasErrors('platform');
    }
}

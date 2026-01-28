<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add new temporary column
        Schema::table('campaigns', function (Blueprint $table) {
            $table->text('platform_new')->nullable()->after('platform');
        });

        // 2. Migrate existing data (wrap single string in array)
        // Using raw SQL to ensure compatibility. 
        // For existing string 'Facebook', we want '["Facebook"]'.
        // We use simple string concatenation for JSON structure.
        DB::statement("UPDATE campaigns SET platform_new = CONCAT('[\"', platform, '\"]') WHERE platform IS NOT NULL");

        // 3. Drop old column
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('platform');
        });

        // 4. Rename new column to old name and make it proper JSON type (or TEXT castable to array)
        Schema::table('campaigns', function (Blueprint $table) {
            // Re-create as text/json. Using text is safer for migration on some DBs without doctrine/dbal for renaming
            // But here we dropped the old one, so we can just rename.
            // SQLite doesn't support renameColumn easily in old versions, but Laravel 10+ handles it.
            // However, to be super safe, let's just use 'platform_new' as 'platform' by renaming.
            $table->renameColumn('platform_new', 'platform');
        });
        
        // 5. Ensure it's nullable or default as needed (it was nullable in step 1)
        // If we want to enforce JSON type strictly on MySQL:
        // Schema::table('campaigns', function (Blueprint $table) {
        //    $table->json('platform')->change();
        // });
        // But TEXT is fine for Laravel array casting.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // It's hard to reverse perfectly if multiple platforms were added.
        // We will just pick the first one or default to 'Other'.
        
        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('platform_old')->nullable()->after('platform');
        });

        // Extract first element from JSON array. Simple regex or JSON_EXTRACT if supported.
        // Using generic approach:
        $campaigns = DB::table('campaigns')->get();
        foreach ($campaigns as $campaign) {
            $platforms = json_decode($campaign->platform);
            $singlePlatform = is_array($platforms) && count($platforms) > 0 ? $platforms[0] : 'Other';
            DB::table('campaigns')->where('id', $campaign->id)->update(['platform_old' => $singlePlatform]);
        }

        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('platform');
            $table->renameColumn('platform_old', 'platform');
        });
    }
};

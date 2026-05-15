<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id('setting_id');
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string'); // string, bool, int, json
            $table->string('group', 50)->default('general');
            $table->timestamps();
        });

        // Seed defaults for user-side controls
        $now = now();
        $defaults = [
            // General
            ['key' => 'site.announcement.enabled',  'value' => '0',  'type' => 'bool',   'group' => 'general'],
            ['key' => 'site.announcement.title',    'value' => '',   'type' => 'string', 'group' => 'general'],
            ['key' => 'site.announcement.message',  'value' => '',   'type' => 'string', 'group' => 'general'],
            ['key' => 'site.announcement.variant',  'value' => 'info', 'type' => 'string', 'group' => 'general'],

            // Feature toggles
            ['key' => 'user.registration.enabled',  'value' => '1',  'type' => 'bool', 'group' => 'features'],
            ['key' => 'user.google_login.enabled',  'value' => '1',  'type' => 'bool', 'group' => 'features'],
            ['key' => 'user.chatbot.enabled',       'value' => '1',  'type' => 'bool', 'group' => 'features'],
            ['key' => 'user.campaigns.public',      'value' => '1',  'type' => 'bool', 'group' => 'features'],
            ['key' => 'user.events.public',         'value' => '1',  'type' => 'bool', 'group' => 'features'],
            ['key' => 'user.inkind.public',         'value' => '1',  'type' => 'bool', 'group' => 'features'],

            // Maintenance
            ['key' => 'site.maintenance.enabled',   'value' => '0',  'type' => 'bool',   'group' => 'maintenance'],
            ['key' => 'site.maintenance.message',   'value' => 'We are performing scheduled maintenance. Please check back soon.', 'type' => 'string', 'group' => 'maintenance'],
        ];

        foreach ($defaults as $row) {
            DB::table('site_settings')->insert(array_merge($row, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // Add a status column to user_account for suspend/activate (safe if not present)
        if (!Schema::hasColumn('user_account', 'status')) {
            Schema::table('user_account', function (Blueprint $table) {
                $table->string('status')->default('active');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
        // Intentionally leave user_account.status in place to avoid data loss on rollback.
    }
};

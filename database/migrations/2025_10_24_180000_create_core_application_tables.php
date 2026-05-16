<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_account', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone_number', 20)->nullable()->unique();
            $table->string('password')->nullable();
            $table->string('google_id')->nullable()->unique();
            $table->string('profile_photo_url')->nullable();
            $table->string('status')->default('unverified');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('identity_statuses', function (Blueprint $table) {
            $table->id('status_id');
            $table->foreignId('user_id')->constrained('user_account', 'user_id')->cascadeOnDelete();
            $table->string('status')->default('Pending');
            $table->timestamps();

            $table->unique('user_id');
        });

        Schema::create('verification_requests', function (Blueprint $table) {
            $table->id('request_id');
            $table->foreignId('user_id')->constrained('user_account', 'user_id')->cascadeOnDelete();
            $table->string('id_type');
            $table->string('id_number')->nullable();
            $table->string('id_number_hash')->nullable();
            $table->date('dob')->nullable();
            $table->string('sex', 10)->nullable();
            $table->string('first_name', 80);
            $table->string('middle_name', 80)->nullable();
            $table->string('last_name', 80);
            $table->string('address')->nullable();
            $table->date('id_expiry')->nullable();
            $table->string('id_front_path');
            $table->string('id_back_path')->nullable();
            $table->string('face_photo_path');
            $table->string('selfie_path');
            $table->string('supporting_doc_path')->nullable();
            $table->json('rule_flags')->nullable();
            $table->json('reupload_fields')->nullable();
            $table->string('status')->default('pending');
            $table->text('review_notes')->nullable();
            $table->timestamps();
        });

        $this->createVerificationRequestIdentityIndexes();

        Schema::create('campaigns', function (Blueprint $table) {
            $table->increments('campaign_id');
            $table->foreignId('user_id')->nullable()->constrained('user_account', 'user_id')->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('campaign_organizer', 100)->nullable();
            $table->decimal('target_amount', 12, 2)->default(0);
            $table->decimal('current_amount', 12, 2)->default(0);
            $table->string('status')->default('active');
            $table->string('schedule_type')->default('one_time');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->json('recurring_days')->nullable();
            $table->time('recurring_time')->nullable();
            $table->json('images')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('qr_code')->nullable();
            $table->string('gcash_number', 20)->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('donor_count')->default(0);
            $table->boolean('allow_anonymous')->default(false);
            $table->timestamps();
        });

        Schema::create('campaign_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('campaign_id');
            $table->foreignId('user_id')->nullable()->constrained('user_account', 'user_id')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('campaign_id')->references('campaign_id')->on('campaigns')->cascadeOnDelete();
            $table->index(['campaign_id', 'user_id']);
        });

        Schema::create('donations', function (Blueprint $table) {
            $table->bigIncrements('donation_id');
            $table->unsignedInteger('campaign_id');
            $table->foreignId('user_id')->nullable()->constrained('user_account', 'user_id')->nullOnDelete();
            $table->boolean('is_anonymous')->default(false);
            $table->string('donor_name')->nullable();
            $table->string('donor_email')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('reference_number', 100)->nullable()->unique();
            $table->string('proof_image')->nullable();
            $table->string('message_code')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->foreign('campaign_id')->references('campaign_id')->on('campaigns')->cascadeOnDelete();
        });

        Schema::create('manual_donation_requests', function (Blueprint $table) {
            $table->id('request_id');
            $table->unsignedInteger('campaign_id');
            $table->foreignId('user_id')->nullable()->constrained('user_account', 'user_id')->nullOnDelete();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('reference_number', 100)->nullable();
            $table->string('proof_image')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->foreign('campaign_id')->references('campaign_id')->on('campaigns')->cascadeOnDelete();
        });

        Schema::create('drop_off_points', function (Blueprint $table) {
            $table->increments('dropoff_id');
            $table->string('name');
            $table->text('address');
            $table->string('schedule_datetime');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('in_kind_donations', function (Blueprint $table) {
            $table->bigIncrements('inkind_id');
            $table->foreignId('user_id')->nullable()->constrained('user_account', 'user_id')->nullOnDelete();
            $table->string('donor_name')->nullable();
            $table->string('donor_email');
            $table->string('donor_phone', 20)->nullable();
            $table->unsignedInteger('dropoff_id')->nullable();
            $table->string('item_name');
            $table->string('category');
            $table->text('description')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('status')->default('Scheduled');
            $table->timestamps();

            $table->foreign('dropoff_id')->references('dropoff_id')->on('drop_off_points')->nullOnDelete();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->increments('event_id');
            $table->string('title');
            $table->text('description');
            $table->string('photo')->nullable();
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->string('location');
            $table->timestamp('deadline')->nullable();
            $table->string('coordinator_name');
            $table->string('coordinator_email');
            $table->string('coordinator_phone', 20);
            $table->timestamps();
        });

        Schema::create('volunteer_roles', function (Blueprint $table) {
            $table->increments('vroles_id');
            $table->unsignedInteger('event_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('event_id')->references('event_id')->on('events')->cascadeOnDelete();
        });

        Schema::create('event_registrations', function (Blueprint $table) {
            $table->bigIncrements('registration_id');
            $table->foreignId('user_id')->nullable()->constrained('user_account', 'user_id')->nullOnDelete();
            $table->unsignedInteger('event_id');
            $table->unsignedInteger('vroles_id')->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 150);
            $table->string('phone', 50);
            $table->string('messenger_link', 250)->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->string('sex', 10)->nullable();
            $table->string('address')->nullable();
            $table->string('registered_role')->nullable();
            $table->boolean('remind_me')->default(false);
            $table->unsignedInteger('reminder_minutes')->nullable();
            $table->string('status')->default('registered');

            $table->foreign('event_id')->references('event_id')->on('events')->cascadeOnDelete();
            $table->foreign('vroles_id')->references('vroles_id')->on('volunteer_roles')->nullOnDelete();
        });

        Schema::create('dnc_records', function (Blueprint $table) {
            $table->id('dnc_id');
            $table->date('date');
            $table->string('assessor')->nullable();
            $table->string('event');
            $table->string('province');
            $table->string('municipality');
            $table->string('barangay');
            $table->unsignedInteger('households')->nullable();
            $table->unsignedInteger('individuals')->nullable();
            $table->unsignedInteger('pop_male')->nullable();
            $table->unsignedInteger('pop_female')->nullable();
            $table->unsignedInteger('pop_children')->nullable();
            $table->unsignedInteger('pop_elderly')->nullable();
            $table->unsignedInteger('pop_pwds')->nullable();
            $table->unsignedInteger('houses_full')->nullable();
            $table->unsignedInteger('houses_partial')->nullable();
            $table->json('infrastructure')->nullable();
            $table->string('crop_type')->nullable();
            $table->decimal('crop_hectares', 10, 2)->nullable();
            $table->string('livestock_type')->nullable();
            $table->unsignedInteger('livestock_number')->nullable();
            $table->string('tools_destroyed')->nullable();
            $table->string('facilities_affected')->nullable();
            $table->text('facilities_notes')->nullable();
            $table->json('needs')->nullable();
            $table->text('needs_other')->nullable();
            $table->string('groups')->nullable();
            $table->string('facilities')->nullable();
            $table->string('skills')->nullable();
            $table->text('initiatives')->nullable();
            $table->string('priority');
            $table->text('solutions')->nullable();
            $table->string('top_need_1');
            $table->string('top_need_2')->nullable();
            $table->string('top_need_3')->nullable();
            $table->timestamps();
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('dnc_records');
        Schema::dropIfExists('event_registrations');
        Schema::dropIfExists('volunteer_roles');
        Schema::dropIfExists('events');
        Schema::dropIfExists('in_kind_donations');
        Schema::dropIfExists('drop_off_points');
        Schema::dropIfExists('manual_donation_requests');
        Schema::dropIfExists('donations');
        Schema::dropIfExists('campaign_views');
        Schema::dropIfExists('campaigns');
        Schema::dropIfExists('verification_requests');
        Schema::dropIfExists('identity_statuses');
        Schema::dropIfExists('user_account');
    }

    private function createVerificationRequestIdentityIndexes(): void
    {
        if (DB::connection()->getDriverName() === 'mongodb') {
            $collection = DB::connection('mongodb')->getCollection('verification_requests');

            $collection->createIndex(
                ['id_number_hash' => 1],
                [
                    'name' => 'id_number_hash_1',
                    'unique' => true,
                    'partialFilterExpression' => [
                        'id_number_hash' => ['$type' => 'string'],
                    ],
                ]
            );

            return;
        }

        Schema::table('verification_requests', function (Blueprint $table) {
            $table->unique('id_number_hash');
        });
    }
};

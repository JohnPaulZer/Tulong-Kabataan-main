<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mongodb') {
            $collection = DB::connection('mongodb')->getCollection('upload_sessions');
            $collection->createIndex(['upload_id' => 1], ['unique' => true]);
            $collection->createIndex(['user_id' => 1, 'status' => 1]);
            $collection->createIndex(['updated_at' => 1]);
            return;
        }

        Schema::create('upload_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('upload_id')->unique();
            $table->string('user_id')->index();
            $table->string('module', 80);
            $table->string('original_file_name');
            $table->unsignedBigInteger('file_size');
            $table->string('mime_type', 150);
            $table->string('extension', 20);
            $table->unsignedInteger('total_chunks');
            $table->json('uploaded_chunks')->nullable();
            $table->string('status', 30)->index();
            $table->string('final_file_url')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->timestamp('completed_at')->nullable();
        });
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mongodb') {
            DB::connection('mongodb')->getCollection('upload_sessions')->drop();
            return;
        }

        Schema::dropIfExists('upload_sessions');
    }
};

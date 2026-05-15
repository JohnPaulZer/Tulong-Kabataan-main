<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_media', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->string('page_name');
            $table->string('section_name')->nullable();
            $table->text('image_path')->nullable();
            $table->text('image_url')->nullable();
            $table->string('image_type', 80)->nullable();
            $table->string('recommended_size')->nullable();
            $table->string('status', 30)->default('default');
            $table->string('updated_by')->nullable();
            $table->string('updated_by_id')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->timestamps();

            $table->index(['page_name', 'section_name']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_media');
    }
};

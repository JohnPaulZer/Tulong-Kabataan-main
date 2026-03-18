<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop if exists to avoid errors
        Schema::dropIfExists('impact_report_donation');

        // Create pivot table with EXACT same types
        Schema::create('impact_report_donation', function (Blueprint $table) {
            $table->id();

            // These MUST match exactly your existing tables
            $table->unsignedBigInteger('impact_report_id');
            $table->unsignedBigInteger('inkind_id');

            $table->timestamps();

            // Add unique constraint
            $table->unique(['impact_report_id', 'inkind_id']);
        });

        // Add foreign keys SEPARATELY to ensure tables exist
        Schema::table('impact_report_donation', function (Blueprint $table) {
            // Foreign key to impact_reports
            $table->foreign('impact_report_id')
                ->references('impact_report_id')
                ->on('impact_reports')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Foreign key to in_kind_donations
            $table->foreign('inkind_id')
                ->references('inkind_id')
                ->on('in_kind_donations')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Add indexes for performance
            $table->index('impact_report_id');
            $table->index('inkind_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impact_report_donation');
    }
};

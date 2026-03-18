<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('campaign_updates', function (Blueprint $table) {
            $table->id('update_id');
            $table->unsignedInteger('campaign_id'); // Changed to unsignedInteger to match campaigns table
            $table->unsignedBigInteger('user_id'); // Keep as unsignedBigInteger for user_account
            $table->text('message');
            $table->json('images')->nullable();
            $table->unsignedInteger('reaction_count')->default(0);
            $table->timestamps();

            // Foreign key for campaigns (using unsignedInteger)
            $table->foreign('campaign_id')
                ->references('campaign_id')
                ->on('campaigns')
                ->onDelete('cascade');

            // Foreign key for user_account (using unsignedBigInteger)
            $table->foreign('user_id')
                ->references('user_id')
                ->on('user_account')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('campaign_updates');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('update_reactions', function (Blueprint $table) {
            $table->id('reaction_id');
            $table->unsignedBigInteger('update_id'); // campaign_updates uses id() which creates bigint
            $table->unsignedBigInteger('user_id'); // user_account uses bigint
            $table->timestamps();

            $table->foreign('update_id')
                ->references('update_id')
                ->on('campaign_updates')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('user_id')
                ->on('user_account')
                ->onDelete('cascade');

            // Ensure a user can only react once per update
            $table->unique(['update_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('update_reactions');
    }
};
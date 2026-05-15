<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_account', function (Blueprint $table) {
            $table->date('birthday')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('user_account', function (Blueprint $table) {
            $table->dropColumn('birthday');
        });
    }
};

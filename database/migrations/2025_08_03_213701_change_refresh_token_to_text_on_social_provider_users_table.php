<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('social_provider_user', function (Blueprint $table) {
            // Add a new 'refresh_token' column
            $table->text('refresh_token')->change();
        });
    }

    public function down(): void
    {
        Schema::table('social_provider_user', function (Blueprint $table) {
            $table->string('refresh_token')->change();
        });
    }
};

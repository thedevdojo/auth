<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Some providers return tokens longer than the old 400 / 255 character columns.
        Schema::table('social_provider_user', function (Blueprint $table) {
            $table->text('token')->change();
            $table->text('refresh_token')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('social_provider_user', function (Blueprint $table) {
            $table->string('token', 400)->change();
            $table->string('refresh_token')->nullable()->change();
        });
    }
};

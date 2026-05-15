<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('social_provider_user', function (Blueprint $table) {
            $table->text('token')->change();
        });
    }

    public function down(): void
    {
        Schema::table('social_provider_user', function (Blueprint $table) {
            $table->string('token', 400)->change();
        });
    }
};

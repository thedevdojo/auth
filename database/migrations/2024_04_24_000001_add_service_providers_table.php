<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('social_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('scopes')->nullable();
            $table->json('parameters')->nullable();
            $table->boolean('override_scopes')->default(false);
            $table->boolean('stateless')->default(false);
            $table->boolean('active')->default(true);
            $table->text('svg')->nullable();
            $table->timestamps();
        });

        Schema::create('social_provider_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('social_provider_id')->constrained('social_providers')->onDelete('cascade');
            $table->string('provider_user_id');
            $table->string('nickname')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('avatar')->nullable();
            $table->string('token');
            $table->string('refresh_token')->nullable();
            $table->text('provider_data')->nullable(); // Changed to JSON for consistent handling of structured data
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamps();

            $table->primary(['user_id', 'social_provider_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_provider_user');
        Schema::dropIfExists('social_providers');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Enums\PhpVersion;
use App\Enums\WordpressVersion;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration creates the initial database schema for the Blueprint Generator application.
     * It includes tables for users, blueprints, blueprint statistics, and personal access tokens.
     * All tables use UUID as primary keys and include proper foreign key constraints and indexes.
     */
    public function up(): void
    {

        // Create blueprints table
        Schema::create('blueprints', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', ['public', 'private'])->default('public');
            $table->enum('php_version', PhpVersion::values());
            $table->enum('wordpress_version', WordpressVersion::values());
            $table->json('steps');
            $table->boolean('is_anonymous')->default(false);
            $table->timestamps();
            $table->softDeletes();

        });


        // Create blueprint_statistics table
        Schema::create('blueprint_statistics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('blueprint_id')->constrained('blueprints')->onDelete('cascade');
            $table->integer('views_count')->default(0);
            $table->integer('runs_count')->default(0);
            $table->timestamp('last_viewed_at')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();

            $table->index('views_count');
            $table->index('runs_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blueprint_statistics');
        Schema::dropIfExists('blueprints');
    }
}; 
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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone')->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('cover_letter')->nullable();
            $table->enum('user_type', ['jobseeker', 'employer', 'admin'])->default('jobseeker');
            $table->string('location')->nullable();
            $table->string('website')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('github')->nullable();
            $table->string('portfolio')->nullable();
            $table->enum('experience_level', ['entry', 'junior', 'mid', 'senior', 'expert'])->nullable();
            $table->string('current_salary')->nullable();
            $table->string('expected_salary')->nullable();
            $table->enum('employment_status', ['employed', 'unemployed', 'freelancer', 'student'])->nullable();
            $table->boolean('open_to_work')->default(false);
            $table->boolean('open_to_relocation')->default(false);
            $table->boolean('open_to_remote')->default(false);
            $table->json('preferred_job_types')->nullable();
            $table->json('preferred_locations')->nullable();
            $table->json('preferred_industries')->nullable();
            $table->timestamps();
            
            $table->index(['user_type', 'open_to_work']);
            $table->index(['location', 'experience_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};

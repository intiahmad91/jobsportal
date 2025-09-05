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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->text('responsibilities')->nullable();
            $table->text('benefits')->nullable();
            $table->foreignId('category_id')->constrained('job_categories')->onDelete('cascade');
            $table->foreignId('location_id')->constrained('job_locations')->onDelete('cascade');
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'internship', 'freelance', 'temporary']);
            $table->enum('experience_level', ['entry', 'junior', 'mid', 'senior', 'expert']);
            $table->string('min_salary')->nullable();
            $table->string('max_salary')->nullable();
            $table->string('salary_currency')->default('USD');
            $table->enum('salary_period', ['hourly', 'daily', 'weekly', 'monthly', 'yearly'])->default('monthly');
            $table->boolean('salary_negotiable')->default(false);
            $table->boolean('remote_work')->default(false);
            $table->boolean('relocation_assistance')->default(false);
            $table->string('application_deadline')->nullable();
            $table->integer('positions_available')->default(1);
            $table->enum('status', ['active', 'paused', 'closed', 'draft'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_premium')->default(false);
            $table->timestamp('featured_until')->nullable();
            $table->timestamp('premium_until')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('applications_count')->default(0);
            $table->json('tags')->nullable();
            $table->timestamps();
            
            $table->index(['title', 'status']);
            $table->index(['category_id', 'location_id']);
            $table->index(['employment_type', 'experience_level']);
            $table->index(['status', 'is_featured']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};

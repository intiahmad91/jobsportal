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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('cover_letter')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('github_url')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'shortlisted', 'interviewed', 'offered', 'rejected', 'withdrawn'])->default('pending');
            $table->text('employer_notes')->nullable();
            $table->text('candidate_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('shortlisted_at')->nullable();
            $table->timestamp('interviewed_at')->nullable();
            $table->timestamp('offered_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->integer('rating')->nullable();
            $table->timestamps();
            
            $table->unique(['job_id', 'user_id']);
            $table->index(['status', 'created_at']);
            $table->index(['job_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};

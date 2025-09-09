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
        Schema::create('candidate_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('candidate_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('job_id')->nullable()->constrained('jobs')->onDelete('cascade');
            $table->enum('interaction_type', ['contact', 'view_resume', 'hire', 'interview', 'reject']);
            $table->enum('status', ['initiated', 'pending', 'completed', 'cancelled'])->default('initiated');
            $table->text('message')->nullable();
            $table->json('metadata')->nullable(); // For storing additional data like salary, dates, etc.
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['employer_id', 'candidate_id']);
            $table->index(['interaction_type', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_interactions');
    }
};

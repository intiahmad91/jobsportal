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
        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('issuing_organization');
            $table->string('credential_id')->nullable();
            $table->string('credential_url')->nullable();
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->boolean('never_expires')->default(false);
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->json('skills_covered')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'issue_date']);
            $table->index(['issuing_organization', 'name']);
            $table->index(['expiry_date', 'never_expires']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certifications');
    }
};

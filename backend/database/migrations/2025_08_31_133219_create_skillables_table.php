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
        Schema::create('skillables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skill_id')->constrained()->onDelete('cascade');
            $table->morphs('skillable');
            $table->enum('proficiency_level', ['beginner', 'intermediate', 'advanced', 'expert'])->nullable();
            $table->integer('years_experience')->nullable();
            $table->boolean('is_endorsed')->default(false);
            $table->integer('endorsement_count')->default(0);
            $table->timestamps();
            
            $table->unique(['skill_id', 'skillable_type', 'skillable_id']);
            $table->index(['skill_id', 'proficiency_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skillables');
    }
};

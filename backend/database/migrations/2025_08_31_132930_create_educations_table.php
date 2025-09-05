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
        Schema::create('educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('degree');
            $table->string('field_of_study');
            $table->string('institution');
            $table->string('location')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->string('grade')->nullable();
            $table->text('description')->nullable();
            $table->string('certificate_url')->nullable();
            $table->json('activities')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'start_date']);
            $table->index(['institution', 'degree']);
            $table->index(['field_of_study', 'degree']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('educations');
    }
};

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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('industry')->nullable();
            $table->string('company_size')->nullable();
            $table->string('founded_year')->nullable();
            $table->text('mission')->nullable();
            $table->text('vision')->nullable();
            $table->json('benefits')->nullable();
            $table->json('social_links')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamps();
            
            $table->index(['name', 'industry']);
            $table->index(['city', 'country']);
            $table->index(['is_verified', 'is_featured']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};

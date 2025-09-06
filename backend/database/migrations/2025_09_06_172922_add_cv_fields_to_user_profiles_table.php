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
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('cv_original_name')->nullable()->after('cv_path');
            $table->bigInteger('cv_size')->nullable()->after('cv_original_name');
            $table->string('cv_mime_type')->nullable()->after('cv_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['cv_original_name', 'cv_size', 'cv_mime_type']);
        });
    }
};
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
            // General settings
            $table->string('timezone')->default('America/New_York')->after('location');
            $table->string('language', 10)->default('en')->after('timezone');
            $table->string('currency', 10)->default('USD')->after('language');
            
            // Notification settings
            $table->boolean('email_notifications')->default(true)->after('currency');
            $table->boolean('application_alerts')->default(true)->after('email_notifications');
            $table->boolean('job_expiry_alerts')->default(true)->after('application_alerts');
            $table->boolean('weekly_reports')->default(false)->after('job_expiry_alerts');
            $table->boolean('marketing_emails')->default(false)->after('weekly_reports');
            $table->boolean('sms_notifications')->default(false)->after('marketing_emails');
            
            // Privacy settings
            $table->string('profile_visibility')->default('public')->after('sms_notifications');
            $table->boolean('show_contact_info')->default(true)->after('profile_visibility');
            $table->boolean('allow_direct_messages')->default(true)->after('show_contact_info');
            $table->boolean('data_sharing')->default(false)->after('allow_direct_messages');
            $table->boolean('analytics_tracking')->default(true)->after('data_sharing');
            
            // Security settings
            $table->boolean('two_factor_auth')->default(false)->after('analytics_tracking');
            $table->boolean('login_alerts')->default(true)->after('two_factor_auth');
            $table->integer('session_timeout')->default(30)->after('login_alerts');
            $table->integer('password_expiry')->default(90)->after('session_timeout');
            $table->json('ip_whitelist')->nullable()->after('password_expiry');
            
            // Integration settings
            $table->boolean('linkedin_integration')->default(false)->after('ip_whitelist');
            $table->boolean('indeed_integration')->default(false)->after('linkedin_integration');
            $table->boolean('glassdoor_integration')->default(false)->after('indeed_integration');
            $table->boolean('google_analytics')->default(false)->after('glassdoor_integration');
            $table->boolean('facebook_pixel')->default(false)->after('google_analytics');
            
            // Billing settings
            $table->string('billing_plan')->default('Professional')->after('facebook_pixel');
            $table->string('billing_cycle')->default('monthly')->after('billing_plan');
            $table->date('next_billing_date')->nullable()->after('billing_cycle');
            $table->string('payment_method')->nullable()->after('next_billing_date');
            $table->boolean('auto_renewal')->default(true)->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'timezone', 'language', 'currency',
                'email_notifications', 'application_alerts', 'job_expiry_alerts', 
                'weekly_reports', 'marketing_emails', 'sms_notifications',
                'profile_visibility', 'show_contact_info', 'allow_direct_messages', 
                'data_sharing', 'analytics_tracking',
                'two_factor_auth', 'login_alerts', 'session_timeout', 
                'password_expiry', 'ip_whitelist',
                'linkedin_integration', 'indeed_integration', 'glassdoor_integration', 
                'google_analytics', 'facebook_pixel',
                'billing_plan', 'billing_cycle', 'next_billing_date', 
                'payment_method', 'auto_renewal'
            ]);
        });
    }
};

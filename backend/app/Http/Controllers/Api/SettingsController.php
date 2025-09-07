<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    /**
     * Get settings for the current employer.
     */
    public function getEmployerSettings(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Load the profile relationship
            $user->load('profile');
            
            // Get the user's company
            $company = $user->company;
            
            if (!$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'No company profile found for this user'
                ], 404);
            }

            // Get user settings from profile or create default
            $profile = $user->profile;
            
            $settings = [
                'general' => [
                    'companyName' => $company->name,
                    'email' => $user->email,
                    'phone' => $company->phone ?? '',
                    'timezone' => $profile->timezone ?? 'America/New_York',
                    'language' => $profile->language ?? 'en',
                    'currency' => $profile->currency ?? 'USD'
                ],
                'notifications' => [
                    'emailNotifications' => (bool)($profile->email_notifications ?? true),
                    'applicationAlerts' => (bool)($profile->application_alerts ?? true),
                    'jobExpiryAlerts' => (bool)($profile->job_expiry_alerts ?? true),
                    'weeklyReports' => (bool)($profile->weekly_reports ?? false),
                    'marketingEmails' => (bool)($profile->marketing_emails ?? false),
                    'smsNotifications' => (bool)($profile->sms_notifications ?? false)
                ],
                'privacy' => [
                    'profileVisibility' => $profile->profile_visibility ?? 'public',
                    'showContactInfo' => (bool)($profile->show_contact_info ?? true),
                    'allowDirectMessages' => (bool)($profile->allow_direct_messages ?? true),
                    'dataSharing' => (bool)($profile->data_sharing ?? false),
                    'analyticsTracking' => (bool)($profile->analytics_tracking ?? true)
                ],
                'security' => [
                    'twoFactorAuth' => (bool)($profile->two_factor_auth ?? false),
                    'loginAlerts' => (bool)($profile->login_alerts ?? true),
                    'sessionTimeout' => (int)($profile->session_timeout ?? 30),
                    'passwordExpiry' => (int)($profile->password_expiry ?? 90),
                    'ipWhitelist' => $profile->ip_whitelist ? json_decode($profile->ip_whitelist, true) : []
                ],
                'integrations' => [
                    'linkedinIntegration' => (bool)($profile->linkedin_integration ?? false),
                    'indeedIntegration' => (bool)($profile->indeed_integration ?? false),
                    'glassdoorIntegration' => (bool)($profile->glassdoor_integration ?? false),
                    'googleAnalytics' => (bool)($profile->google_analytics ?? false),
                    'facebookPixel' => (bool)($profile->facebook_pixel ?? false)
                ],
                'billing' => [
                    'plan' => $profile->billing_plan ?? 'Professional',
                    'billingCycle' => $profile->billing_cycle ?? 'monthly',
                    'nextBillingDate' => $profile->next_billing_date ?? date('Y-m-d', strtotime('+1 month')),
                    'paymentMethod' => $profile->payment_method ?? 'Credit Card ending in 4242',
                    'autoRenewal' => (bool)($profile->auto_renewal ?? true)
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $settings
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update settings for the current employer.
     */
    public function updateEmployerSettings(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            // Load the profile relationship
            $user->load('profile');
            
            // Get the user's company
            $company = $user->company;
            
            if (!$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'No company profile found for this user'
                ], 404);
            }

            $validated = $request->validate([
                'general' => 'sometimes|array',
                'general.companyName' => 'sometimes|string|max:255',
                'general.email' => 'sometimes|email|max:255',
                'general.phone' => 'sometimes|string|max:20',
                'general.timezone' => 'sometimes|string|max:50',
                'general.language' => 'sometimes|string|max:10',
                'general.currency' => 'sometimes|string|max:10',
                
                'notifications' => 'sometimes|array',
                'notifications.emailNotifications' => 'sometimes|boolean',
                'notifications.applicationAlerts' => 'sometimes|boolean',
                'notifications.jobExpiryAlerts' => 'sometimes|boolean',
                'notifications.weeklyReports' => 'sometimes|boolean',
                'notifications.marketingEmails' => 'sometimes|boolean',
                'notifications.smsNotifications' => 'sometimes|boolean',
                
                'privacy' => 'sometimes|array',
                'privacy.profileVisibility' => 'sometimes|string|in:public,private,limited',
                'privacy.showContactInfo' => 'sometimes|boolean',
                'privacy.allowDirectMessages' => 'sometimes|boolean',
                'privacy.dataSharing' => 'sometimes|boolean',
                'privacy.analyticsTracking' => 'sometimes|boolean',
                
                'security' => 'sometimes|array',
                'security.twoFactorAuth' => 'sometimes|boolean',
                'security.loginAlerts' => 'sometimes|boolean',
                'security.sessionTimeout' => 'sometimes|integer|min:15|max:480',
                'security.passwordExpiry' => 'sometimes|integer|min:30|max:365',
                'security.ipWhitelist' => 'sometimes|array',
                
                'integrations' => 'sometimes|array',
                'integrations.linkedinIntegration' => 'sometimes|boolean',
                'integrations.indeedIntegration' => 'sometimes|boolean',
                'integrations.glassdoorIntegration' => 'sometimes|boolean',
                'integrations.googleAnalytics' => 'sometimes|boolean',
                'integrations.facebookPixel' => 'sometimes|boolean',
                
                'billing' => 'sometimes|array',
                'billing.billingCycle' => 'sometimes|string|in:monthly,quarterly,yearly',
                'billing.autoRenewal' => 'sometimes|boolean'
            ]);

            DB::beginTransaction();

            try {
                // Update company information
                if (isset($validated['general'])) {
                    $companyData = [];
                    if (isset($validated['general']['companyName'])) {
                        $companyData['name'] = $validated['general']['companyName'];
                    }
                    if (isset($validated['general']['phone'])) {
                        $companyData['phone'] = $validated['general']['phone'];
                    }
                    if (!empty($companyData)) {
                        $company->update($companyData);
                    }
                }

                // Update user email
                if (isset($validated['general']['email'])) {
                    $user->update(['email' => $validated['general']['email']]);
                }

                // Update profile settings
                $profileData = [];
                
                if (isset($validated['general'])) {
                    $general = $validated['general'];
                    if (isset($general['timezone'])) $profileData['timezone'] = $general['timezone'];
                    if (isset($general['language'])) $profileData['language'] = $general['language'];
                    if (isset($general['currency'])) $profileData['currency'] = $general['currency'];
                }
                
                if (isset($validated['notifications'])) {
                    $notifications = $validated['notifications'];
                    if (isset($notifications['emailNotifications'])) $profileData['email_notifications'] = $notifications['emailNotifications'];
                    if (isset($notifications['applicationAlerts'])) $profileData['application_alerts'] = $notifications['applicationAlerts'];
                    if (isset($notifications['jobExpiryAlerts'])) $profileData['job_expiry_alerts'] = $notifications['jobExpiryAlerts'];
                    if (isset($notifications['weeklyReports'])) $profileData['weekly_reports'] = $notifications['weeklyReports'];
                    if (isset($notifications['marketingEmails'])) $profileData['marketing_emails'] = $notifications['marketingEmails'];
                    if (isset($notifications['smsNotifications'])) $profileData['sms_notifications'] = $notifications['smsNotifications'];
                }
                
                if (isset($validated['privacy'])) {
                    $privacy = $validated['privacy'];
                    if (isset($privacy['profileVisibility'])) $profileData['profile_visibility'] = $privacy['profileVisibility'];
                    if (isset($privacy['showContactInfo'])) $profileData['show_contact_info'] = $privacy['showContactInfo'];
                    if (isset($privacy['allowDirectMessages'])) $profileData['allow_direct_messages'] = $privacy['allowDirectMessages'];
                    if (isset($privacy['dataSharing'])) $profileData['data_sharing'] = $privacy['dataSharing'];
                    if (isset($privacy['analyticsTracking'])) $profileData['analytics_tracking'] = $privacy['analyticsTracking'];
                }
                
                if (isset($validated['security'])) {
                    $security = $validated['security'];
                    if (isset($security['twoFactorAuth'])) $profileData['two_factor_auth'] = $security['twoFactorAuth'];
                    if (isset($security['loginAlerts'])) $profileData['login_alerts'] = $security['loginAlerts'];
                    if (isset($security['sessionTimeout'])) $profileData['session_timeout'] = $security['sessionTimeout'];
                    if (isset($security['passwordExpiry'])) $profileData['password_expiry'] = $security['passwordExpiry'];
                    if (isset($security['ipWhitelist'])) $profileData['ip_whitelist'] = json_encode($security['ipWhitelist']);
                }
                
                if (isset($validated['integrations'])) {
                    $integrations = $validated['integrations'];
                    if (isset($integrations['linkedinIntegration'])) $profileData['linkedin_integration'] = $integrations['linkedinIntegration'];
                    if (isset($integrations['indeedIntegration'])) $profileData['indeed_integration'] = $integrations['indeedIntegration'];
                    if (isset($integrations['glassdoorIntegration'])) $profileData['glassdoor_integration'] = $integrations['glassdoorIntegration'];
                    if (isset($integrations['googleAnalytics'])) $profileData['google_analytics'] = $integrations['googleAnalytics'];
                    if (isset($integrations['facebookPixel'])) $profileData['facebook_pixel'] = $integrations['facebookPixel'];
                }
                
                if (isset($validated['billing'])) {
                    $billing = $validated['billing'];
                    if (isset($billing['billingCycle'])) $profileData['billing_cycle'] = $billing['billingCycle'];
                    if (isset($billing['autoRenewal'])) $profileData['auto_renewal'] = $billing['autoRenewal'];
                }

                if (!empty($profileData)) {
                    $user->profile->update($profileData);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Settings updated successfully'
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

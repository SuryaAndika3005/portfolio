<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Gemini (AI Project Assistant, Admin only)
    |--------------------------------------------------------------------------
    |
    | The key reuses GOOGLE_API_KEY (already present in this app's local
    | environment) rather than introducing a second env var name for the
    | same credential. Server-side only — see app/Services/Gemini/.
    |
    | Two models, not one (provider-usage audit, V1.3): default_model
    | handles every operation that either sends images or continues an
    | existing previous_interaction_id conversation — analyze, reply,
    | generate draft, refine, rank covers, suggest gallery order. Those
    | must never switch models mid-conversation (undefined/unsafe per the
    | Interactions API's own "subsequent models must support the output
    | modalities of the previous models as input" constraint), so all of
    | them stay on one model for the whole chain. light_model is only ever
    | used for the three operations that are always independent, text-only,
    | stateless single turns (suggest tools, quality review, SEO) —
    | confirmed via GeminiProjectAssistant that none of those three methods
    | accept a previous_interaction_id parameter at all. Free Tier RPM
    | (Google AI Studio, checked 2026-08-27): gemini-3.7-flash 5, "gemini-3.5-flash-lite" 15,
    | gemini-2.5-flash-lite 10, gemini-2.5-flash 5 — light_model defaults to
    | the 3.5 Flash-Lite generation for the largest RPM headroom while
    | staying in the same model family as the default.
    */
    'google_analytics' => [
        'measurement_id' => env('GOOGLE_ANALYTICS_ID'),
    ],

    'gemini' => [
        'key' => env('GOOGLE_API_KEY'),
        'default_model' => env('GEMINI_MODEL', 'gemini-3.7-flash'),
        'light_model' => env('GEMINI_LIGHT_MODEL', 'gemini-3.5-flash-lite'),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
        'api_revision' => env('GEMINI_API_REVISION', '2026-05-20'),
        'timeout' => (int) env('GEMINI_TIMEOUT', 45),
    ],

];

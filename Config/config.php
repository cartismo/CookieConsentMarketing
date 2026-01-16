<?php

return [
    'name' => 'CookieConsentMarketing',

    'settings' => [
        'enabled' => false,
        'position' => 'bottom', // bottom, top, center
        'layout' => 'bar', // bar, box, popup
        'theme' => 'light', // light, dark, auto

        // Colors
        'primary_color' => '#4F46E5',
        'secondary_color' => '#6B7280',
        'background_color' => '#FFFFFF',
        'text_color' => '#1F2937',

        // Behavior
        'cookie_expiry_days' => 365,
        'show_reject_all' => true,
        'show_customize' => true,
        'block_scripts_until_consent' => true,

        // Links
        'privacy_policy_url' => '/privacy-policy',
        'cookie_policy_url' => '/cookie-policy',

        // Text customization
        'texts' => [
            'banner_title' => 'We use cookies',
            'banner_description' => 'We use cookies to enhance your browsing experience, serve personalized ads or content, and analyze our traffic. By clicking "Accept All", you consent to our use of cookies.',
            'accept_all' => 'Accept All',
            'reject_all' => 'Reject All',
            'customize' => 'Customize',
            'save_preferences' => 'Save Preferences',
            'close' => 'Close',
        ],

        // Cookie categories
        'categories' => [
            'necessary' => [
                'enabled' => true,
                'readonly' => true,
                'title' => 'Necessary',
                'description' => 'These cookies are essential for the website to function properly. They cannot be disabled.',
            ],
            'functional' => [
                'enabled' => false,
                'readonly' => false,
                'title' => 'Functional',
                'description' => 'These cookies enable personalized features and functionality.',
            ],
            'analytics' => [
                'enabled' => false,
                'readonly' => false,
                'title' => 'Analytics',
                'description' => 'These cookies help us understand how visitors interact with the website.',
            ],
            'marketing' => [
                'enabled' => false,
                'readonly' => false,
                'title' => 'Marketing',
                'description' => 'These cookies are used to deliver personalized advertisements.',
            ],
        ],

        // Scripts to load per category
        'scripts' => [
            'analytics' => '',
            'marketing' => '',
            'functional' => '',
        ],

        'sort_order' => 0,
    ],
];
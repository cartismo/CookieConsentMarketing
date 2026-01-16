<?php

namespace Modules\CookieConsentMarketing\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\HasMultiStoreModuleSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    use HasMultiStoreModuleSettings;

    protected function getModuleSlug(): string
    {
        return 'cookie-consent-marketing';
    }

    protected function getDefaultSettings(): array
    {
        return [
            'enabled' => false,
            'position' => 'bottom',
            'layout' => 'bar',
            'theme' => 'light',
            'primary_color' => '#4F46E5',
            'secondary_color' => '#6B7280',
            'background_color' => '#FFFFFF',
            'text_color' => '#1F2937',
            'cookie_expiry_days' => 365,
            'show_reject_all' => true,
            'show_customize' => true,
            'block_scripts_until_consent' => true,
            'privacy_policy_url' => '/privacy-policy',
            'cookie_policy_url' => '/cookie-policy',
            'texts' => [
                'banner_title' => 'We use cookies',
                'banner_description' => 'We use cookies to enhance your browsing experience, serve personalized ads or content, and analyze our traffic. By clicking "Accept All", you consent to our use of cookies.',
                'accept_all' => 'Accept All',
                'reject_all' => 'Reject All',
                'customize' => 'Customize',
                'save_preferences' => 'Save Preferences',
                'close' => 'Close',
            ],
            'categories' => [
                'necessary' => [
                    'enabled' => true,
                    'readonly' => true,
                    'title' => 'Necessary',
                    'description' => 'These cookies are essential for the website to function properly.',
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
            'scripts' => [
                'analytics' => '',
                'marketing' => '',
                'functional' => '',
            ],
            'sort_order' => 0,
        ];
    }

    public function index(): Response
    {
        $data = $this->getMultiStoreData();
        $data['positionOptions'] = [
            ['value' => 'bottom', 'label' => 'Bottom'],
            ['value' => 'top', 'label' => 'Top'],
            ['value' => 'center', 'label' => 'Center (Modal)'],
        ];
        $data['layoutOptions'] = [
            ['value' => 'bar', 'label' => 'Bar'],
            ['value' => 'box', 'label' => 'Box'],
            ['value' => 'popup', 'label' => 'Popup'],
        ];
        $data['themeOptions'] = [
            ['value' => 'light', 'label' => 'Light'],
            ['value' => 'dark', 'label' => 'Dark'],
            ['value' => 'auto', 'label' => 'Auto (System)'],
        ];

        return Inertia::render('CookieConsentMarketing::Admin/Settings', $data);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'is_enabled' => 'boolean',
            'settings.enabled' => 'boolean',
            'settings.position' => 'required|string|in:bottom,top,center',
            'settings.layout' => 'required|string|in:bar,box,popup',
            'settings.theme' => 'required|string|in:light,dark,auto',
            'settings.primary_color' => 'required|string|max:20',
            'settings.secondary_color' => 'required|string|max:20',
            'settings.background_color' => 'required|string|max:20',
            'settings.text_color' => 'required|string|max:20',
            'settings.cookie_expiry_days' => 'required|integer|min:1|max:730',
            'settings.show_reject_all' => 'boolean',
            'settings.show_customize' => 'boolean',
            'settings.block_scripts_until_consent' => 'boolean',
            'settings.privacy_policy_url' => 'nullable|string|max:255',
            'settings.cookie_policy_url' => 'nullable|string|max:255',
            'settings.texts' => 'required|array',
            'settings.texts.banner_title' => 'required|string|max:255',
            'settings.texts.banner_description' => 'required|string|max:1000',
            'settings.texts.accept_all' => 'required|string|max:50',
            'settings.texts.reject_all' => 'required|string|max:50',
            'settings.texts.customize' => 'required|string|max:50',
            'settings.texts.save_preferences' => 'required|string|max:50',
            'settings.texts.close' => 'required|string|max:50',
            'settings.categories' => 'required|array',
            'settings.categories.*.enabled' => 'boolean',
            'settings.categories.*.readonly' => 'boolean',
            'settings.categories.*.title' => 'required|string|max:100',
            'settings.categories.*.description' => 'required|string|max:500',
            'settings.scripts' => 'array',
            'settings.scripts.analytics' => 'nullable|string|max:10000',
            'settings.scripts.marketing' => 'nullable|string|max:10000',
            'settings.scripts.functional' => 'nullable|string|max:10000',
            'settings.sort_order' => 'integer|min:0',
        ]);

        return $this->saveStoreSettings($request);
    }
}
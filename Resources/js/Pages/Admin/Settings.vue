<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StoreSettingsTabs from '@/Components/Admin/StoreSettingsTabs.vue';
import {
    ShieldCheckIcon,
    ArrowLeftIcon,
    ArrowPathIcon,
    CheckCircleIcon,
    XCircleIcon,
    Cog6ToothIcon,
    InformationCircleIcon,
    SwatchIcon,
    DocumentTextIcon,
    CodeBracketIcon,
    EyeIcon,
} from '@heroicons/vue/24/outline';
import { CheckIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    module: Object,
    stores: Array,
    storeSettings: Object,
    defaultSettings: Object,
    positionOptions: Array,
    layoutOptions: Array,
    themeOptions: Array,
});

const storeTabsRef = ref(null);
const saving = ref(false);

const activeTab = ref('general');

const tabs = [
    { id: 'general', label: 'General', icon: Cog6ToothIcon },
    { id: 'appearance', label: 'Appearance', icon: SwatchIcon },
    { id: 'texts', label: 'Texts', icon: DocumentTextIcon },
    { id: 'categories', label: 'Categories', icon: ShieldCheckIcon },
    { id: 'scripts', label: 'Scripts', icon: CodeBracketIcon },
];

const submit = () => {
    if (!storeTabsRef.value) return;

    saving.value = true;
    router.put(route('admin.marketing.cookie-consent.settings.update'), {
        store_id: storeTabsRef.value.activeStoreId,
        is_enabled: storeTabsRef.value.isEnabled,
        settings: storeTabsRef.value.localSettings,
    }, {
        preserveScroll: true,
        onFinish: () => saving.value = false,
    });
};

const resetAll = () => {
    if (confirm('Reset all settings to defaults?') && storeTabsRef.value) {
        Object.assign(storeTabsRef.value.localSettings, props.defaultSettings);
    }
};

const hasChanges = computed(() => {
    if (!storeTabsRef.value) return false;
    const currentStoreSettings = props.storeSettings[storeTabsRef.value.activeStoreId];
    if (!currentStoreSettings) return true;
    const original = { ...props.defaultSettings, ...(currentStoreSettings.settings || {}) };
    return JSON.stringify(storeTabsRef.value.localSettings) !== JSON.stringify(original) ||
           storeTabsRef.value.isEnabled !== currentStoreSettings.is_enabled;
});

const categoryKeys = ['necessary', 'functional', 'analytics', 'marketing'];
</script>

<template>
    <AdminLayout :title="`${module.name} Settings`">
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <Link
                        :href="route('admin.modules.installed.index')"
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                    >
                        <ArrowLeftIcon class="w-5 h-5" />
                    </Link>
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl shadow-lg shadow-emerald-500/25">
                            <ShieldCheckIcon class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Cookie Consent</h1>
                            <p class="text-sm text-gray-500">GDPR Cookie Banner Settings</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span v-if="hasChanges" class="flex items-center text-sm text-amber-600 font-medium">
                        <span class="w-2 h-2 bg-amber-500 rounded-full mr-2 animate-pulse"></span>
                        Unsaved changes
                    </span>
                    <button
                        type="button"
                        @click="resetAll"
                        class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors"
                    >
                        <ArrowPathIcon class="w-4 h-4 inline mr-2" />
                        Reset
                    </button>
                    <button
                        type="button"
                        @click="submit"
                        :disabled="saving || !hasChanges"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl hover:from-emerald-600 hover:to-teal-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg shadow-emerald-500/25"
                    >
                        <CheckIcon class="w-4 h-4 inline mr-2" />
                        {{ saving ? 'Saving...' : 'Save Changes' }}
                    </button>
                </div>
            </div>
        </template>

        <StoreSettingsTabs ref="storeTabsRef" :stores="stores" :store-settings="storeSettings" :default-settings="defaultSettings" module-slug="cookie-consent-marketing">
            <template #default="{ store, settings, updateSetting, isEnabled }">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <!-- Left Sidebar -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Status Card -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="p-5 border-b border-gray-100">
                                <h3 class="font-semibold text-gray-900">Module Status</h3>
                            </div>
                            <div class="p-5 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Status</span>
                                    <span
                                        :class="settings.enabled ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                                        class="px-3 py-1 text-xs font-semibold rounded-full"
                                    >
                                        {{ settings.enabled ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Version</span>
                                    <span class="text-sm font-mono text-gray-900">v{{ module.installed_version }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Type</span>
                                    <span class="text-sm text-gray-900">Marketing</span>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Tabs -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="p-3">
                                <nav class="space-y-1">
                                    <button
                                        v-for="tab in tabs"
                                        :key="tab.id"
                                        type="button"
                                        @click="activeTab = tab.id"
                                        :class="[
                                            'w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-sm font-medium transition-all',
                                            activeTab === tab.id
                                                ? 'bg-emerald-50 text-emerald-700'
                                                : 'text-gray-600 hover:bg-gray-50'
                                        ]"
                                    >
                                        <component :is="tab.icon" class="w-5 h-5" />
                                        <span>{{ tab.label }}</span>
                                    </button>
                                </nav>
                            </div>
                        </div>

                        <!-- Help Card -->
                        <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-100">
                            <div class="flex items-start space-x-3">
                                <InformationCircleIcon class="w-5 h-5 text-emerald-500 mt-0.5 flex-shrink-0" />
                                <div>
                                    <h4 class="text-sm font-medium text-emerald-900">GDPR Compliant</h4>
                                    <p class="text-sm text-emerald-700 mt-1">
                                        This banner helps you comply with GDPR, CCPA, and other privacy regulations.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side - Settings -->
                    <div class="lg:col-span-3 space-y-6">
                        <!-- Enable/Disable -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div :class="settings.enabled ? 'bg-green-100' : 'bg-gray-100'" class="p-3 rounded-xl transition-colors">
                                        <component :is="settings.enabled ? CheckCircleIcon : XCircleIcon"
                                            :class="settings.enabled ? 'text-green-600' : 'text-gray-400'"
                                            class="w-6 h-6"
                                        />
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">Enable Cookie Consent</h3>
                                        <p class="text-sm text-gray-500">Show cookie consent banner to visitors for {{ store?.name }}</p>
                                    </div>
                                </div>
                                <button
                                    type="button"
                                    @click="updateSetting('enabled', !settings.enabled)"
                                    :class="settings.enabled ? 'bg-green-500' : 'bg-gray-300'"
                                    class="relative inline-flex h-7 w-12 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out"
                                >
                                    <span
                                        :class="settings.enabled ? 'translate-x-5' : 'translate-x-0'"
                                        class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow-lg ring-0 transition duration-200 ease-in-out"
                                    />
                                </button>
                            </div>
                        </div>

                        <!-- General Tab -->
                        <template v-if="activeTab === 'general'">
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                    <div class="flex items-center space-x-3">
                                        <Cog6ToothIcon class="w-5 h-5 text-gray-400" />
                                        <h2 class="font-semibold text-gray-900">General Settings</h2>
                                    </div>
                                </div>
                                <div class="p-6 space-y-6">
                                    <div class="grid grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                                            <select
                                                :value="settings.position"
                                                @change="updateSetting('position', $event.target.value)"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                                            >
                                                <option v-for="opt in positionOptions" :key="opt.value" :value="opt.value">
                                                    {{ opt.label }}
                                                </option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Layout</label>
                                            <select
                                                :value="settings.layout"
                                                @change="updateSetting('layout', $event.target.value)"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                                            >
                                                <option v-for="opt in layoutOptions" :key="opt.value" :value="opt.value">
                                                    {{ opt.label }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Theme</label>
                                            <select
                                                :value="settings.theme"
                                                @change="updateSetting('theme', $event.target.value)"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                                            >
                                                <option v-for="opt in themeOptions" :key="opt.value" :value="opt.value">
                                                    {{ opt.label }}
                                                </option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Cookie Expiry (days)</label>
                                            <input
                                                type="number"
                                                :value="settings.cookie_expiry_days"
                                                @input="updateSetting('cookie_expiry_days', parseInt($event.target.value))"
                                                min="1"
                                                max="730"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                                            />
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Privacy Policy URL</label>
                                            <input
                                                type="text"
                                                :value="settings.privacy_policy_url"
                                                @input="updateSetting('privacy_policy_url', $event.target.value)"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                                                placeholder="/privacy-policy"
                                            />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Cookie Policy URL</label>
                                            <input
                                                type="text"
                                                :value="settings.cookie_policy_url"
                                                @input="updateSetting('cookie_policy_url', $event.target.value)"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                                                placeholder="/cookie-policy"
                                            />
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <label
                                            class="relative flex items-center p-4 rounded-xl cursor-pointer transition-all"
                                            :class="settings.show_reject_all
                                                ? 'bg-emerald-50 border-2 border-emerald-200'
                                                : 'bg-gray-50 border-2 border-transparent hover:bg-gray-100'"
                                        >
                                            <input
                                                type="checkbox"
                                                :checked="settings.show_reject_all"
                                                @change="updateSetting('show_reject_all', $event.target.checked)"
                                                class="sr-only"
                                            />
                                            <div
                                                class="w-10 h-6 rounded-full relative transition-colors mr-3"
                                                :class="settings.show_reject_all ? 'bg-emerald-500' : 'bg-gray-300'"
                                            >
                                                <div
                                                    class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform"
                                                    :class="settings.show_reject_all ? 'translate-x-5' : 'translate-x-1'"
                                                ></div>
                                            </div>
                                            <div>
                                                <span class="text-sm font-medium text-gray-700">Show "Reject All" Button</span>
                                                <p class="text-xs text-gray-500">Allow users to reject all non-essential cookies</p>
                                            </div>
                                        </label>

                                        <label
                                            class="relative flex items-center p-4 rounded-xl cursor-pointer transition-all"
                                            :class="settings.show_customize
                                                ? 'bg-emerald-50 border-2 border-emerald-200'
                                                : 'bg-gray-50 border-2 border-transparent hover:bg-gray-100'"
                                        >
                                            <input
                                                type="checkbox"
                                                :checked="settings.show_customize"
                                                @change="updateSetting('show_customize', $event.target.checked)"
                                                class="sr-only"
                                            />
                                            <div
                                                class="w-10 h-6 rounded-full relative transition-colors mr-3"
                                                :class="settings.show_customize ? 'bg-emerald-500' : 'bg-gray-300'"
                                            >
                                                <div
                                                    class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform"
                                                    :class="settings.show_customize ? 'translate-x-5' : 'translate-x-1'"
                                                ></div>
                                            </div>
                                            <div>
                                                <span class="text-sm font-medium text-gray-700">Show "Customize" Button</span>
                                                <p class="text-xs text-gray-500">Allow users to customize cookie preferences</p>
                                            </div>
                                        </label>

                                        <label
                                            class="relative flex items-center p-4 rounded-xl cursor-pointer transition-all"
                                            :class="settings.block_scripts_until_consent
                                                ? 'bg-emerald-50 border-2 border-emerald-200'
                                                : 'bg-gray-50 border-2 border-transparent hover:bg-gray-100'"
                                        >
                                            <input
                                                type="checkbox"
                                                :checked="settings.block_scripts_until_consent"
                                                @change="updateSetting('block_scripts_until_consent', $event.target.checked)"
                                                class="sr-only"
                                            />
                                            <div
                                                class="w-10 h-6 rounded-full relative transition-colors mr-3"
                                                :class="settings.block_scripts_until_consent ? 'bg-emerald-500' : 'bg-gray-300'"
                                            >
                                                <div
                                                    class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform"
                                                    :class="settings.block_scripts_until_consent ? 'translate-x-5' : 'translate-x-1'"
                                                ></div>
                                            </div>
                                            <div>
                                                <span class="text-sm font-medium text-gray-700">Block Scripts Until Consent</span>
                                                <p class="text-xs text-gray-500">Don't load tracking scripts until user gives consent</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Appearance Tab -->
                        <template v-if="activeTab === 'appearance'">
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                    <div class="flex items-center space-x-3">
                                        <SwatchIcon class="w-5 h-5 text-gray-400" />
                                        <h2 class="font-semibold text-gray-900">Colors</h2>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <div class="grid grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Primary Color</label>
                                            <div class="flex items-center space-x-3">
                                                <input
                                                    type="color"
                                                    :value="settings.primary_color"
                                                    @input="updateSetting('primary_color', $event.target.value)"
                                                    class="w-12 h-12 rounded-lg border border-gray-300 cursor-pointer"
                                                />
                                                <input
                                                    type="text"
                                                    :value="settings.primary_color"
                                                    @input="updateSetting('primary_color', $event.target.value)"
                                                    class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 font-mono"
                                                />
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Secondary Color</label>
                                            <div class="flex items-center space-x-3">
                                                <input
                                                    type="color"
                                                    :value="settings.secondary_color"
                                                    @input="updateSetting('secondary_color', $event.target.value)"
                                                    class="w-12 h-12 rounded-lg border border-gray-300 cursor-pointer"
                                                />
                                                <input
                                                    type="text"
                                                    :value="settings.secondary_color"
                                                    @input="updateSetting('secondary_color', $event.target.value)"
                                                    class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 font-mono"
                                                />
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Background Color</label>
                                            <div class="flex items-center space-x-3">
                                                <input
                                                    type="color"
                                                    :value="settings.background_color"
                                                    @input="updateSetting('background_color', $event.target.value)"
                                                    class="w-12 h-12 rounded-lg border border-gray-300 cursor-pointer"
                                                />
                                                <input
                                                    type="text"
                                                    :value="settings.background_color"
                                                    @input="updateSetting('background_color', $event.target.value)"
                                                    class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 font-mono"
                                                />
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Text Color</label>
                                            <div class="flex items-center space-x-3">
                                                <input
                                                    type="color"
                                                    :value="settings.text_color"
                                                    @input="updateSetting('text_color', $event.target.value)"
                                                    class="w-12 h-12 rounded-lg border border-gray-300 cursor-pointer"
                                                />
                                                <input
                                                    type="text"
                                                    :value="settings.text_color"
                                                    @input="updateSetting('text_color', $event.target.value)"
                                                    class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 font-mono"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview -->
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                    <div class="flex items-center space-x-3">
                                        <EyeIcon class="w-5 h-5 text-gray-400" />
                                        <h2 class="font-semibold text-gray-900">Preview</h2>
                                    </div>
                                </div>
                                <div class="p-6">
                                    <div
                                        class="p-6 rounded-xl shadow-lg"
                                        :style="{
                                            backgroundColor: settings.background_color,
                                            color: settings.text_color,
                                        }"
                                    >
                                        <h3 class="font-semibold text-lg mb-2">{{ settings.texts?.banner_title }}</h3>
                                        <p class="text-sm mb-4 opacity-80">{{ settings.texts?.banner_description }}</p>
                                        <div class="flex flex-wrap gap-3">
                                            <button
                                                type="button"
                                                class="px-4 py-2 rounded-lg text-sm font-medium text-white"
                                                :style="{ backgroundColor: settings.primary_color }"
                                            >
                                                {{ settings.texts?.accept_all }}
                                            </button>
                                            <button
                                                v-if="settings.show_reject_all"
                                                type="button"
                                                class="px-4 py-2 rounded-lg text-sm font-medium border"
                                                :style="{
                                                    borderColor: settings.secondary_color,
                                                    color: settings.text_color,
                                                }"
                                            >
                                                {{ settings.texts?.reject_all }}
                                            </button>
                                            <button
                                                v-if="settings.show_customize"
                                                type="button"
                                                class="px-4 py-2 rounded-lg text-sm font-medium"
                                                :style="{ color: settings.primary_color }"
                                            >
                                                {{ settings.texts?.customize }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Texts Tab -->
                        <template v-if="activeTab === 'texts'">
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                    <div class="flex items-center space-x-3">
                                        <DocumentTextIcon class="w-5 h-5 text-gray-400" />
                                        <h2 class="font-semibold text-gray-900">Banner Texts</h2>
                                    </div>
                                </div>
                                <div class="p-6 space-y-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Banner Title</label>
                                        <input
                                            type="text"
                                            :value="settings.texts?.banner_title"
                                            @input="updateSetting('texts', { ...settings.texts, banner_title: $event.target.value })"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                                        />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Banner Description</label>
                                        <textarea
                                            :value="settings.texts?.banner_description"
                                            @input="updateSetting('texts', { ...settings.texts, banner_description: $event.target.value })"
                                            rows="3"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                                        ></textarea>
                                    </div>

                                    <div class="grid grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">"Accept All" Button</label>
                                            <input
                                                type="text"
                                                :value="settings.texts?.accept_all"
                                                @input="updateSetting('texts', { ...settings.texts, accept_all: $event.target.value })"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                                            />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">"Reject All" Button</label>
                                            <input
                                                type="text"
                                                :value="settings.texts?.reject_all"
                                                @input="updateSetting('texts', { ...settings.texts, reject_all: $event.target.value })"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                                            />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">"Customize" Button</label>
                                            <input
                                                type="text"
                                                :value="settings.texts?.customize"
                                                @input="updateSetting('texts', { ...settings.texts, customize: $event.target.value })"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                                            />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">"Save Preferences" Button</label>
                                            <input
                                                type="text"
                                                :value="settings.texts?.save_preferences"
                                                @input="updateSetting('texts', { ...settings.texts, save_preferences: $event.target.value })"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Categories Tab -->
                        <template v-if="activeTab === 'categories'">
                            <div
                                v-for="key in categoryKeys"
                                :key="key"
                                class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden"
                            >
                                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <ShieldCheckIcon class="w-5 h-5 text-gray-400" />
                                            <h2 class="font-semibold text-gray-900">{{ settings.categories?.[key]?.title }}</h2>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <span
                                                v-if="settings.categories?.[key]?.readonly"
                                                class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded"
                                            >
                                                Required
                                            </span>
                                            <label v-else class="relative inline-flex items-center cursor-pointer">
                                                <input
                                                    type="checkbox"
                                                    :checked="settings.categories?.[key]?.enabled"
                                                    @change="updateSetting('categories', {
                                                        ...settings.categories,
                                                        [key]: { ...settings.categories?.[key], enabled: $event.target.checked }
                                                    })"
                                                    class="sr-only"
                                                />
                                                <div
                                                    class="w-10 h-6 rounded-full transition-colors"
                                                    :class="settings.categories?.[key]?.enabled ? 'bg-emerald-500' : 'bg-gray-300'"
                                                >
                                                    <div
                                                        class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform"
                                                        :class="settings.categories?.[key]?.enabled ? 'translate-x-5' : 'translate-x-1'"
                                                    ></div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-6 space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                                        <input
                                            type="text"
                                            :value="settings.categories?.[key]?.title"
                                            @input="updateSetting('categories', {
                                                ...settings.categories,
                                                [key]: { ...settings.categories?.[key], title: $event.target.value }
                                            })"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                                        />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                        <textarea
                                            :value="settings.categories?.[key]?.description"
                                            @input="updateSetting('categories', {
                                                ...settings.categories,
                                                [key]: { ...settings.categories?.[key], description: $event.target.value }
                                            })"
                                            rows="2"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Scripts Tab -->
                        <template v-if="activeTab === 'scripts'">
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                    <div class="flex items-center space-x-3">
                                        <CodeBracketIcon class="w-5 h-5 text-gray-400" />
                                        <h2 class="font-semibold text-gray-900">Analytics Scripts</h2>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Scripts loaded when user accepts Analytics cookies (e.g., Google Analytics)
                                    </p>
                                </div>
                                <div class="p-6">
                                    <textarea
                                        :value="settings.scripts?.analytics"
                                        @input="updateSetting('scripts', { ...settings.scripts, analytics: $event.target.value })"
                                        rows="6"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 font-mono text-sm"
                                        placeholder="<!-- Google Analytics -->"
                                    ></textarea>
                                </div>
                            </div>

                            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                    <div class="flex items-center space-x-3">
                                        <CodeBracketIcon class="w-5 h-5 text-gray-400" />
                                        <h2 class="font-semibold text-gray-900">Marketing Scripts</h2>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Scripts loaded when user accepts Marketing cookies (e.g., Facebook Pixel)
                                    </p>
                                </div>
                                <div class="p-6">
                                    <textarea
                                        :value="settings.scripts?.marketing"
                                        @input="updateSetting('scripts', { ...settings.scripts, marketing: $event.target.value })"
                                        rows="6"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 font-mono text-sm"
                                        placeholder="<!-- Facebook Pixel -->"
                                    ></textarea>
                                </div>
                            </div>

                            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                    <div class="flex items-center space-x-3">
                                        <CodeBracketIcon class="w-5 h-5 text-gray-400" />
                                        <h2 class="font-semibold text-gray-900">Functional Scripts</h2>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Scripts loaded when user accepts Functional cookies (e.g., chat widgets)
                                    </p>
                                </div>
                                <div class="p-6">
                                    <textarea
                                        :value="settings.scripts?.functional"
                                        @input="updateSetting('scripts', { ...settings.scripts, functional: $event.target.value })"
                                        rows="6"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 font-mono text-sm"
                                        placeholder="<!-- Chat Widget -->"
                                    ></textarea>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </StoreSettingsTabs>
    </AdminLayout>
</template>
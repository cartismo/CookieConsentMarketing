<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { XMarkIcon, ChevronDownIcon, ChevronUpIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    settings: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['consent-given']);

const isVisible = ref(false);
const showPreferences = ref(false);
const consent = ref({
    necessary: true,
    functional: false,
    analytics: false,
    marketing: false,
});

const COOKIE_NAME = 'cookie_consent';

// Check if consent was already given
onMounted(() => {
    const storedConsent = getCookie(COOKIE_NAME);
    if (storedConsent) {
        try {
            consent.value = JSON.parse(storedConsent);
            loadScripts();
        } catch (e) {
            isVisible.value = true;
        }
    } else {
        isVisible.value = true;
    }
});

// Cookie helpers
function setCookie(name, value, days) {
    const expires = new Date();
    expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
    document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/;SameSite=Lax`;
}

function getCookie(name) {
    const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    return match ? match[2] : null;
}

// Accept all cookies
function acceptAll() {
    consent.value = {
        necessary: true,
        functional: true,
        analytics: true,
        marketing: true,
    };
    saveConsent();
}

// Reject all non-essential cookies
function rejectAll() {
    consent.value = {
        necessary: true,
        functional: false,
        analytics: false,
        marketing: false,
    };
    saveConsent();
}

// Save preferences
function savePreferences() {
    saveConsent();
}

// Save consent to cookie and load scripts
function saveConsent() {
    setCookie(COOKIE_NAME, JSON.stringify(consent.value), props.settings.cookie_expiry_days);
    isVisible.value = false;
    showPreferences.value = false;
    loadScripts();
    emit('consent-given', consent.value);
}

// Load scripts based on consent
function loadScripts() {
    if (!props.settings.block_scripts_until_consent) return;

    const scripts = props.settings.scripts || {};

    if (consent.value.analytics && scripts.analytics) {
        injectScript(scripts.analytics);
    }
    if (consent.value.marketing && scripts.marketing) {
        injectScript(scripts.marketing);
    }
    if (consent.value.functional && scripts.functional) {
        injectScript(scripts.functional);
    }
}

// Inject script into page
function injectScript(scriptContent) {
    if (!scriptContent) return;

    const container = document.createElement('div');
    container.innerHTML = scriptContent;

    // Handle script tags
    const scripts = container.querySelectorAll('script');
    scripts.forEach((script) => {
        const newScript = document.createElement('script');
        if (script.src) {
            newScript.src = script.src;
            newScript.async = true;
        } else {
            newScript.textContent = script.textContent;
        }
        document.head.appendChild(newScript);
    });

    // Handle other elements (like noscript)
    const others = container.querySelectorAll(':not(script)');
    others.forEach((el) => {
        document.body.appendChild(el.cloneNode(true));
    });
}

// Position classes
const positionClasses = computed(() => {
    const pos = props.settings.position;
    if (pos === 'top') return 'top-0 left-0 right-0';
    if (pos === 'center') return 'inset-0 flex items-center justify-center';
    return 'bottom-0 left-0 right-0'; // default bottom
});

// Layout classes
const layoutClasses = computed(() => {
    const layout = props.settings.layout;
    if (layout === 'box') return 'max-w-lg mx-4 mb-4 rounded-2xl';
    if (layout === 'popup') return 'max-w-xl rounded-2xl shadow-2xl';
    return 'w-full'; // bar
});

// Theme styles
const themeStyles = computed(() => ({
    backgroundColor: props.settings.background_color,
    color: props.settings.text_color,
}));

const primaryButtonStyles = computed(() => ({
    backgroundColor: props.settings.primary_color,
    color: '#FFFFFF',
}));

const secondaryButtonStyles = computed(() => ({
    borderColor: props.settings.secondary_color,
    color: props.settings.text_color,
}));

const categories = computed(() => {
    const cats = props.settings.categories || {};
    return [
        { key: 'necessary', ...cats.necessary },
        { key: 'functional', ...cats.functional },
        { key: 'analytics', ...cats.analytics },
        { key: 'marketing', ...cats.marketing },
    ];
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 translate-y-4"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-4"
        >
            <div
                v-if="isVisible"
                class="fixed z-[9999]"
                :class="positionClasses"
            >
                <!-- Overlay for center/popup -->
                <div
                    v-if="settings.position === 'center'"
                    class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                    @click="() => {}"
                ></div>

                <!-- Banner -->
                <div
                    class="relative p-6 shadow-xl"
                    :class="layoutClasses"
                    :style="themeStyles"
                >
                    <!-- Close button (only for popup) -->
                    <button
                        v-if="settings.layout === 'popup'"
                        type="button"
                        @click="rejectAll"
                        class="absolute top-4 right-4 p-1 rounded-full hover:bg-black/10 transition-colors"
                    >
                        <XMarkIcon class="w-5 h-5" />
                    </button>

                    <!-- Main Content -->
                    <div v-if="!showPreferences">
                        <h3 class="font-semibold text-lg mb-2">
                            {{ settings.texts?.banner_title || 'We use cookies' }}
                        </h3>
                        <p class="text-sm opacity-80 mb-4">
                            {{ settings.texts?.banner_description || 'We use cookies to enhance your experience.' }}
                        </p>

                        <!-- Links -->
                        <div class="flex flex-wrap gap-4 text-sm mb-4">
                            <a
                                v-if="settings.privacy_policy_url"
                                :href="settings.privacy_policy_url"
                                class="underline opacity-70 hover:opacity-100"
                            >
                                Privacy Policy
                            </a>
                            <a
                                v-if="settings.cookie_policy_url"
                                :href="settings.cookie_policy_url"
                                class="underline opacity-70 hover:opacity-100"
                            >
                                Cookie Policy
                            </a>
                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-wrap gap-3">
                            <button
                                type="button"
                                @click="acceptAll"
                                class="px-5 py-2.5 rounded-lg text-sm font-medium transition-opacity hover:opacity-90"
                                :style="primaryButtonStyles"
                            >
                                {{ settings.texts?.accept_all || 'Accept All' }}
                            </button>
                            <button
                                v-if="settings.show_reject_all"
                                type="button"
                                @click="rejectAll"
                                class="px-5 py-2.5 rounded-lg text-sm font-medium border-2 transition-opacity hover:opacity-80"
                                :style="secondaryButtonStyles"
                            >
                                {{ settings.texts?.reject_all || 'Reject All' }}
                            </button>
                            <button
                                v-if="settings.show_customize"
                                type="button"
                                @click="showPreferences = true"
                                class="px-5 py-2.5 rounded-lg text-sm font-medium transition-opacity hover:opacity-80"
                                :style="{ color: settings.primary_color }"
                            >
                                {{ settings.texts?.customize || 'Customize' }}
                            </button>
                        </div>
                    </div>

                    <!-- Preferences Panel -->
                    <div v-else>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-lg">Cookie Preferences</h3>
                            <button
                                type="button"
                                @click="showPreferences = false"
                                class="p-1 rounded-full hover:bg-black/10 transition-colors"
                            >
                                <XMarkIcon class="w-5 h-5" />
                            </button>
                        </div>

                        <div class="space-y-4 mb-6 max-h-[300px] overflow-y-auto">
                            <div
                                v-for="cat in categories"
                                :key="cat.key"
                                class="p-4 rounded-lg"
                                :style="{ backgroundColor: settings.primary_color + '10' }"
                            >
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-medium">{{ cat.title }}</span>
                                    <label
                                        v-if="!cat.readonly"
                                        class="relative inline-flex items-center cursor-pointer"
                                    >
                                        <input
                                            type="checkbox"
                                            v-model="consent[cat.key]"
                                            class="sr-only"
                                        />
                                        <div
                                            class="w-10 h-6 rounded-full transition-colors"
                                            :style="{
                                                backgroundColor: consent[cat.key]
                                                    ? settings.primary_color
                                                    : settings.secondary_color,
                                            }"
                                        >
                                            <div
                                                class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform"
                                                :class="consent[cat.key] ? 'translate-x-5' : 'translate-x-1'"
                                            ></div>
                                        </div>
                                    </label>
                                    <span
                                        v-else
                                        class="text-xs font-medium px-2 py-1 rounded"
                                        :style="{ backgroundColor: settings.primary_color + '20' }"
                                    >
                                        Required
                                    </span>
                                </div>
                                <p class="text-sm opacity-70">{{ cat.description }}</p>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-wrap gap-3">
                            <button
                                type="button"
                                @click="savePreferences"
                                class="px-5 py-2.5 rounded-lg text-sm font-medium transition-opacity hover:opacity-90"
                                :style="primaryButtonStyles"
                            >
                                {{ settings.texts?.save_preferences || 'Save Preferences' }}
                            </button>
                            <button
                                type="button"
                                @click="acceptAll"
                                class="px-5 py-2.5 rounded-lg text-sm font-medium border-2 transition-opacity hover:opacity-80"
                                :style="secondaryButtonStyles"
                            >
                                {{ settings.texts?.accept_all || 'Accept All' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
<script setup>
import {usePage} from "@inertiajs/vue3";
import {router} from "@inertiajs/vue3";
import {useToast} from "primevue";
import {watch} from "vue";
import {computed} from "vue";
import {onMounted} from "vue";
import {onUnmounted} from "vue";
import {watchEffect} from "vue";
import {ref} from "vue";
import {route} from "ziggy-js";
import MainSelect from "../components/main/MainSelect.vue";
import {useTranslation} from "../composables/useTranslation.js";

const page = usePage();
const toast = useToast();
const {translate} = useTranslation();

const authenticated = computed(() => !!page.props.auth.user);

const items = computed(() => [
    {
        label: translate('modules.nav.home'),
        icon: 'pi pi-home',
        command: () => {
            router.get(route('home'));
        },
    },
    {
        label: translate('modules.nav.dashboard'),
        icon: 'pi pi-globe',
        command: () => {
            router.get(route('dashboard'));
        },
        visible: authenticated.value,
    },
    {
        label: translate('modules.nav.login'),
        icon: 'pi pi-user',
        command: () => {
            router.get(route('login'));
        },
        visible: !authenticated.value,
    },
    {
        label: translate('modules.nav.registration'),
        icon: 'pi pi-plus',
        command: () => {
            router.get(route('registration'));
        },
        visible: !authenticated.value,
    },
    {
        label: translate('modules.nav.emails'),
        icon: 'pi pi-envelope',
        items: [
            // {
            //     label: translate('modules.nav.personal_inboxes'),
            //     command: () => {
            //         router.get(route('auth.logout'));
            //     },
            // },
            // {
            //     label: translate('modules.nav.tickets'),
            //     command: () => {
            //         router.get(route('auth.logout'));
            //     },
            // },
            // {
            //     label: translate('modules.nav.email_inbox_settings'),
            //     command: () => {
            //         router.get(route('auth.logout'));
            //     },
            // },
            {
                label: translate('modules.nav.emails'),
                command: () => {
                    router.get(route('modules.emails.index'));
                },
            },
            {
                label: translate('modules.nav.email_settings'),
                command: () => {
                    router.get(route('modules.email-settings.index'));
                },
            },
            {
                label: translate('modules.nav.email_inbox_settings'),
                command: () => {
                    router.get(route('modules.email-inbox-settings.index'));
                },
            },
        ]
    },
    {
        label: translate('modules.nav.logout'),
        icon: 'pi pi-sign-out',
        command: () => {
            router.get(route('auth.logout'));
        },
        visible: authenticated.value,
    },
]);

const showSuccess = ref(false);
const showError = ref(false);

const locale = ref(localStorage.getItem('user_locale') || page.props.locale || 'en');

const setLocale = () => {
    localStorage.setItem('user_locale', locale.value);

    router.post(route('set-locale'), {locale: locale.value}, {
        onSuccess: () => {
            router.reload();
        }
    });
};

const locales = computed(() => {
    return [
        {
            id: 'lt',
            name: translate(`app.locale.locales.lt`)
        },
        {
            id: 'en',
            name: translate(`app.locale.locales.en`)
        },
    ]
});

watch(
    () => page.props.flash,
    (newFlash) => {
        if (newFlash?.success) {
            toast.add({
                severity: 'success',
                summary: newFlash.success,
                life: 5000,
            });
        }
        if (newFlash?.error) {
            toast.add({
                severity: 'error',
                summary: newFlash.error,
                life: 5000,
            });
        }
    },
);

// scroll to top start
const showScrollTop = ref(false);

const handleScroll = () => {
    showScrollTop.value = window.scrollY > 300;
};

const scrollToTop = () => {
    window.scrollTo({top: 0, behavior: "smooth"});
};

onMounted(() => {
    window.addEventListener("scroll", handleScroll);
});

onUnmounted(() => {
    window.removeEventListener("scroll", handleScroll);
});
//scroll to top end
</script>

<template>
    <main>
        <Toast
            position="bottom-center"
        />
        <header class="container mx-auto p-4">
            <Menubar :model="items">
                <Menubar :model="items">
                    <template v-if="item.visible" #item="{ item, props, hasSubmenu, root }">
                        <a v-ripple class="flex items-center" v-bind="props.action">
                            <span>{{ item.label }}</span>
                            <Badge v-if="item.badge" :class="{ 'ml-auto': !root, 'ml-2': root }" :value="item.badge" />
                            <span v-if="item.shortcut" class="ml-auto border border-surface rounded bg-emphasis text-muted-color text-xs p-1">{{ item.shortcut }}</span>
                            <i v-if="hasSubmenu" :class="['pi pi-angle-down ml-auto', { 'pi-angle-down': root, 'pi-angle-right': !root }]"></i>
                        </a>
                    </template>
                </Menubar>
                <template #end>
                    <div class="flex items-center gap-5">
                        <MainSelect
                            v-model:value="locale"
                            name="locale"
                            size="small"
                            :options="locales"
                            :show-clear="false"
                            @change="setLocale"
                        />
                    </div>
                </template>
            </Menubar>
        </header>
        <article>
            <slot/>
        </article>
    </main>
</template>

<style>
* {
    font-family: 'Inter', 'Roboto', sans-serif;
}
</style>

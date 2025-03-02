<script setup>
import {usePage} from "@inertiajs/vue3";
import {router} from "@inertiajs/vue3";
import {computed} from "vue";
import {onMounted} from "vue";
import {onUnmounted} from "vue";
import {watchEffect} from "vue";
import {ref} from "vue";
import {route} from "ziggy-js";
import MainSelect from "../components/main/MainSelect.vue";
import {useTranslation} from "../composables/useTranslation.js";

const page = usePage();

const lastDownloadedFileEvent = ref();
const isVisible = ref(false);
let timeoutId = null;

// Echo.channel('ripper')
//     .listen('.GlobalNewFileEvent', (event) => {
//         lastDownloadedFileEvent.value = event;
//         isVisible.value = true;
//
//         if (timeoutId) clearTimeout(timeoutId);
//         timeoutId = setTimeout(() => {
//             isVisible.value = false;
//         }, 10000);
//     });

const {translate} = useTranslation();

const items = computed(() => [
    {
        label: translate('modules.nav.home'),
        icon: 'pi pi-home',
        command: () => {
            router.get(route('home'));
        },
    },
    {
        label: translate('modules.nav.login'),
        icon: 'pi pi-user',
        command: () => {
            router.get(route('login'));
        },
    },
    {
        label: translate('modules.nav.registration'),
        icon: 'pi pi-plus',
        command: () => {
            router.get(route('registration'));
        },
    },
    {
        label: translate('modules.nav.files'),
        icon: 'pi pi-folder',
        command: () => {
            router.get(route('files.index'));
        },
    },
    {
        label: translate('modules.nav.ripper'),
        icon: 'pi pi-wave-pulse',
        command: () => {
            router.get(route('fileripper.index'));
        },
    },
    {
        label: translate('modules.nav.favorites'),
        icon: 'pi pi-star',
        command: () => {
            router.get(route('files.index', {path: 'favorites'}));
        },
    },
]);

const showSuccess = ref(false);
const showError = ref(false);

const locale = ref({
    code: localStorage.getItem('user_locale') || page.props.locale || 'en',
    name: (localStorage.getItem('user_locale') === 'lt') ? 'Lietuvių' : 'English',
});

const setLocale = () => {
    localStorage.setItem('user_locale', locale.value.code);

    router.post(route('set-locale'), {locale: locale.value.code}, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            router.reload();
        }
    });
};

watchEffect(() => {
    showSuccess.value = !!page.props.flash?.success;
    showError.value = !!page.props.flash?.error;
});

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
        <header class="container mx-auto p-4">
            <Menubar :model="items">
                <template #end>
                    <div class="flex items-center gap-5">
                        <div v-if="isVisible">
                            {{ lastDownloadedFileEvent?.url }}
                        </div>
                        <MainSelect
                            v-model:value="locale"
                            name="locale"
                            size="small"
                            :options="[
                            {
                                code: 'lt',
                                name: 'Lietuvių'
                            },
                            {
                                code: 'en',
                                name: 'English'
                            },
                        ]"
                            @change="setLocale"
                        />
                    </div>

                </template>
            </Menubar>
        </header>
        <div class="container mx-auto p-4 py-0">
            <div
                v-if="showSuccess"
                class="bg-green-200 text-green-700 p-3 px-4 rounded mb-4 flex justify-between"
            >
                <div>{{ $page?.props?.flash?.success }}</div>
                <div @click="showSuccess = false">
                    <i class="pi pi-times cursor-pointer"></i>
                </div>
            </div>

            <div
                v-if="showError"
                class="bg-red-200 px-6 text-red-700 p-2 rounded mb-4 flex justify-between"
            >
                <div>{{ $page?.props?.flash?.error }}</div>
                <div @click="showError = false">
                    <i class="pi pi-times cursor-pointer"></i>
                </div>
            </div>
        </div>
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

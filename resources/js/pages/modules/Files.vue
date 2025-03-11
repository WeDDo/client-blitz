<template>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl mb-4">
            {{ translate('modules.file.h1') }}
        </h1>
        <div class="my-4 flex items-center space-x-2">
            <Button v-if="path" severity="secondary" @click="navigateUp">
                <i class="pi pi-arrow-circle-left"></i> <span>{{ translate('global.back') }}</span>
            </Button>
            <span class="text-gray-600 dark:text-gray-300">
                {{ translate('modules.file.current_path', {path: `/${path || ''}`}) }}
            </span>
        </div>

        <div class="mb-4 flex items-center space-x-2 mt-8">
            <div class="sm:visible w-16">
                <FloatLabel>
                    <label for="columns">{{ translate('modules.file.columns') }}</label>
                    <InputNumber
                        fluid
                        id="columns"
                        v-model="columns"
                        placeholder="Columns"
                        :min="1"
                        :max="4"
                        size="small"
                    />
                </FloatLabel>
            </div>

            <IconField>
                <InputIcon class="pi pi-search"/>
                <InputText
                    v-model="search"
                    fluid
                    :placeholder="translate('global.search')"
                    size="small"
                />
            </IconField>

            <Button severity="secondary" size="small" @click="toggleRecursive">
                <i class="pi pi-folder"></i>
                {{ recursive ? translate('modules.file.recursive') : translate('modules.file.not_recursive') }}
            </Button>

            <Button severity="secondary" size="small" @click="toggleSortOrder">
                <i :class="sortOrder === 'desc' ? 'pi pi-sort-amount-down' : 'pi pi-sort-amount-up'"></i>
                {{ translate(sortOrder === 'desc' ? 'global.sort_newest' : 'global.sort_oldest') }}
            </Button>
        </div>

        <div v-if="filteredDirectories.length > 0"
             class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
            <div v-for="dir in directories" :key="dir.path"
                 class="bg-gray-200 p-4 rounded-lg shadow cursor-pointer hover:bg-gray-300 text-gray-600 dark:text-gray-800"
                 @click="navigateTo(dir.path)">
                📂 {{ dir.name }}
            </div>
        </div>

        <div v-if="filteredDirectories.length === 0 && filteredFiles.length === 0">
            {{ translate('modules.file.no_folders_or_files_error') }}
        </div>

        <div :class="fileGridClass">
            <div
                v-for="file in filteredFiles" :key="file.url"
                class="bg-white p-3 rounded-lg shadow flex flex-col relative min-h-[250px]"
            >
                <div class="flex-grow flex justify-center items-center mx-auto bg-gray-200">
                    <Image
                        v-if="isImage(file.url)"
                        :src="file.url"
                        :alt="file.name"
                        class="w-full h-auto rounded-lg object-cover"
                        loading="lazy"
                        preview
                    />
                    <DeferredContent v-else-if="isVideo(file.url)">
                        <video
                            ref="videos"
                            :src="file.url"
                            controls
                            muted
                            loop
                            class="w-full h-auto rounded-lg"
                        ></video>
                    </DeferredContent>

                    <a v-else :href="file.url" target="_blank" class="text-blue-600 hover:underline">
                        {{ file.name }}
                    </a>
                </div>

                <div class="flex justify-between items-center w-full p-2 text-sm border-t mt-auto">
                    <div class="flex items-center gap-2 text-sm min-w-0">
                        <Checkbox v-model="selectedFiles" :value="file.url"/>
                        <div class="truncate min-w-0 flex-1">{{ file.name }}</div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <a :href="file.url" target="_blank" class="text-blue-500 hover:underline">
                            {{ translate('modules.file.raw') }}
                        </a>
                        <a :href="file.url" :download="file.name" class="text-blue-500 hover:underline">
                            {{ translate('modules.file.download') }}
                        </a>
                        <i v-tooltip.bottom="file.resolution ?? 'unknown'" class="pi pi-desktop ml-1"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="fixed bottom-0 left-0 z-10 w-full">
            <div class="flex gap-2 m-2">
                <!-- Left-aligned buttons -->
                <Button v-if="selectedFiles.length > 0" severity="secondary" size="small" @click="selectedFiles = []">
                    <i class="pi pi-times-circle"></i> {{ translate('modules.file.deselect_all') }}
                </Button>
                <Button v-if="selectedFiles.length !== files.length" severity="secondary" size="small" @click="selectedFiles = files.map(file => file.url)">
                    <i class="pi pi-times-circle"></i> {{ translate('modules.file.select_all') }}
                </Button>
                <Button v-if="selectedFiles.length > 0" severity="success" size="small" @click="addToFavoritesSelectedFiles">
                    <i class="pi pi-star"></i> {{ translate('modules.file.favorite_selected') }}
                </Button>
                <Button v-if="selectedFiles.length > 0" severity="danger" size="small" @click="deleteSelectedFiles">
                    <i class="pi pi-trash"></i> {{ translate('modules.file.delete_selected') }}
                </Button>
                <Button v-if="videos.length > 0" severity="primary" size="small" @click="toggleAutoplay">
                    <i :class="autoplayEnabled ? 'pi pi-pause' : 'pi pi-play'"></i>
                    {{ translate('modules.file.autoplay') }}
                </Button>

                <div class="flex-grow"></div>

                <Button v-if="showScrollTop" @click="scrollToTop">
                    <i class="pi pi-arrow-circle-up"></i>
                </Button>
            </div>
        </div>
    </div>
</template>

<script setup>
import {usePage} from "@inertiajs/vue3";
import {router} from '@inertiajs/vue3';
import {onMounted} from "vue";
import {reactive} from "vue";
import {onUnmounted} from "vue";
import {nextTick} from "vue";
import {watch} from "vue";
import {computed} from "vue";
import {ref} from "vue";
import {route} from "ziggy-js";
import {useTranslation} from "../../composables/useTranslation.js";

const props = defineProps({
    path: String,
    directories: Array,
    // files: Array,
});

const page = usePage();

const {translate} = useTranslation();

const videos = ref([]);
const columns = ref(3);

const files = ref(page.props.files);
const selectedFiles = ref([]);
const selectAll = ref(false);
const sortOrder = ref(page.props.sortOrder);
const recursive = ref(page.props.recursive);

const toggleRecursive = () => {
    const newRecursive = recursive.value ? 0 : 1;

    router.get(route("files.index", { path: props.path, recursive: newRecursive }), {
        preserveScroll: true,
        onSuccess: () => {
            recursive.value = !!newRecursive;
        },
    });
};

const toggleSortOrder = () => {
    const newSortOrder = sortOrder.value === "desc" ? "asc" : "desc";
    router.get(route("files.index", { path: props.path, sort: newSortOrder }), {
        preserveScroll: true,
        recursive: recursive.value ? 1 : 0,
        onSuccess: () => {
            sortOrder.value = newSortOrder;
        },
    });
};

// function formatChannelName(channelName) {
//     return channelName.replace(/\/+$/, "").replace(/\//g, "_");
// }
//
// Echo.channel(formatChannelName(`files.${props.path}`))
//     .listen('.NewFileEvent', (event) => {
//         console.log('event', event, recursive.value)
//         if (event.path === props.path && !recursive.value) {
//             files.value.unshift(event);
//         }
//     });

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

// #Search
const search = ref('');

const autoplayEnabled = ref(true);
const toggleAutoplay = () => {
    autoplayEnabled.value = !autoplayEnabled.value;
    handleAutoplay();
};
const handleAutoplay = () => {
    nextTick(() => {
        videos.value.forEach((video) => {
            if (autoplayEnabled.value) {
                if (video && video.paused) {
                    video.play().catch(err => console.warn("Autoplay failed:", err));
                }
            } else {
                if (!video.paused) {
                    video.pause();
                }
            }
        });
    });
};
watch(videos.value, () => {
    handleAutoplay();
});

const deleteSelectedFiles = () => {
    if (selectedFiles.value.length === 0) {
        alert(translate('modules.file.no_files_selected_error'));
        return;
    }

    if (!confirm(translate('modules.file.mass_delete_confirm', {file_count: selectedFiles.value.length}))) {
        return;
    }

    router.delete(route('files.massDelete'), {
        data: {urls: selectedFiles.value},
        preserveScroll: true,
        onSuccess: () => {
            files.value = files.value.filter(file => !selectedFiles.value.includes(file.url));

            selectedFiles.value = [];
            selectAll.value = false;
        },
        onError: (error) => {
            console.error("Error deleting files:", error);
        }
    });
};

const addToFavoritesSelectedFiles = () => {
    if (selectedFiles.value.length === 0) {
        alert(translate('modules.file.no_files_selected_error'));
        return;
    }

    if (!confirm(translate('modules.file.add_to_favorites_confirm', {file_count: selectedFiles.value.length}))) {
        return;
    }

    router.post(route('files.addToFavorites', {path: props.path}), {
        urls: selectedFiles.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            selectedFiles.value = [];
            selectAll.value = false;
        },
        onError: (error) => {
            console.error("Error adding to favorites:", error);
        }
    });
};

const filteredDirectories = computed(() => {
    if (!search.value) return props.directories;
    return props.directories.filter((dir) =>
        dir.name.toLowerCase().includes(search.value.toLowerCase())
    );
});

const filteredFiles = computed(() => {
    if (!search.value) return files.value;
    const searchTerm = search.value.toLowerCase();
    return files.value.filter((file) =>
        file.name.toLowerCase().includes(searchTerm) ||
        file.url.toLowerCase().includes(searchTerm)
    );
});
// #Search end

const fileGridClass = computed(() => {
    switch (columns.value) {
        case 1:
            return "grid grid-cols-1 gap-4";
        case 2:
            return "grid grid-cols-1 sm:grid-cols-2 gap-4";
        case 3:
            return "grid grid-cols-1 sm:grid-cols-3 gap-4";
        case 4:
            return "grid grid-cols-1 sm:grid-cols-4 gap-4";
        default:
            return "grid grid-cols-3 gap-4";
    }
});

const navigateTo = (newPath) => {
    router.get(route('files.index', {path: newPath}));
};

const navigateUp = () => {
    const parts = props.path.split('/').filter(Boolean);
    parts.pop();
    router.get(route('files.index', {path: parts.join('/')}), {}, {preserveState: false});
};

const deleteFile = (file) => {
    if (!confirm(translate('modules.file.delete_confirm', {filename: file.name}))) {
        return;
    }

    router.delete(route('files.delete', {url: file.url}), {
        preserveScroll: true,
        onSuccess: () => {
            const index = files.value.findIndex((existingFile) => existingFile.url === file.url);
            if (index !== -1) {
                files.value.splice(index, 1);
            }
        },
        onError: (error) => {
            console.error("Error deleting file:", error);
        }
    });
};

const isImage = (url) => /\.(gif|jpe?g|tiff?|png|webp|bmp)$/i.test(url);
const isVideo = (url) => /\.(mp4|webm|ogg)$/i.test(url);
</script>

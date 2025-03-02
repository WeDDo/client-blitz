<script setup>
import {usePage} from "@inertiajs/vue3";
import {router} from "@inertiajs/vue3";
import {computed} from "vue";
import {watch} from "vue";
import {nextTick} from "vue";
import {onUnmounted} from "vue";
import {ref} from "vue";
import {route} from "ziggy-js";
import {useTranslation} from "../../composables/useTranslation.js";

const page = usePage();
const {translate} = useTranslation();

const props = defineProps({
    name: {
        type: String,
        default: 'Files',
    },
    files: {
        type: Boolean,
        default: true,
    },
    directories: {
        type: Boolean,
        default: false,
    },
});

const files = defineModel('files');
const videos = ref([]);
const columns = ref(3);

const selectedFiles = ref([]);
const selectAll = ref(false);
const sortOrder = ref(page.props.sortOrder);

const toggleSortOrder = () => {
    const newSortOrder = sortOrder.value === "desc" ? "asc" : "desc";
    // router.get(route("files.index", { path: props.path, sort: newSortOrder }), {
    //     preserveState: false,
    //     preserveScroll: true,
    //     onSuccess: () => {
    //         sortOrder.value = newSortOrder;
    //     },
    // });
};

// #Auto scroll
const autoScrollEnabled = ref(false);
let scrollFrame = null;

const toggleAutoScroll = () => {
    if (autoScrollEnabled.value) {
        stopAutoScroll();
    } else {
        startAutoScroll();
    }
};

const startAutoScroll = () => {
    document.addEventListener("dblclick", stopAutoScroll); // Stop scrolling on any click
    autoScrollEnabled.value = true;
    scrollStep();
};

const scrollStep = () => {
    if (!autoScrollEnabled.value) return;
    window.scrollBy({top: 1, behavior: "smooth"});
    scrollFrame = requestAnimationFrame(scrollStep);
};

const stopAutoScroll = () => {
    autoScrollEnabled.value = false;
    document.removeEventListener("dblclick", stopAutoScroll); // Remove event listener
    if (scrollFrame) {
        cancelAnimationFrame(scrollFrame);
        scrollFrame = null;
    }
};

onUnmounted(() => {
    stopAutoScroll();
});
// #Auto scroll end


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

    // router.delete(route('files.massDelete', {path: props.path}), {
    //     data: {files: selectedFiles.value},
    //     preserveState: true,
    //     preserveScroll: true,
    //     onSuccess: () => {
    //         files.value = files.value.filter(file => !selectedFiles.value.includes(file.name));
    //
    //         selectedFiles.value = [];
    //         selectAll.value = false;
    //     },
    //     onError: (error) => {
    //         console.error("Error deleting files:", error);
    //     }
    // });
};

const addToFavoritesSelectedFiles = () => {
    if (selectedFiles.value.length === 0) {
        alert(translate('modules.file.no_files_selected_error'));
        return;
    }

    if (!confirm(translate('modules.file.add_to_favorites_confirm', {file_count: selectedFiles.value.length}))) {
        return;
    }

    // router.post(route('files.addToFavorites', {path: props.path}), {
    //     files: selectedFiles.value,
    // }, {
    //     preserveState: true,
    //     preserveScroll: true,
    //     onSuccess: () => {
    //         selectedFiles.value = [];
    //         selectAll.value = false;
    //     },
    //     onError: (error) => {
    //         console.error("Error adding to favorites:", error);
    //     }
    // });
};

// const filteredDirectories = computed(() => {
//     if (!search.value) return page.props.directories;
//     return page.props.directories.filter((dir) =>
//         dir.name.toLowerCase().includes(search.value.toLowerCase())
//     );
// });

const filteredFiles = computed(() => {
    if (!search.value) return files.value;
    return files.value.filter((file) =>
        file.name.toLowerCase().includes(search.value.toLowerCase())
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
    router.post('/set-locale', {locale: 'lt'});
    router.get(route('files.index', {path: newPath}));
};

const navigateUp = () => {
    // const parts = props.path.split('/').filter(Boolean);
    // parts.pop();
    // router.get(route('files.index', {path: parts.join('/')}), {}, {preserveState: false});
};

const deleteFile = (fileName) => {
    if (!confirm(translate('modules.file.delete_confirm', {filename: fileName}))) {
        return;
    }

    // router.delete(route('files.delete', {path: props.path, file: fileName}), {
    //     preserveState: false,
    //     preserveScroll: true,
    //     onSuccess: () => {
    //         const index = files.value.findIndex(file => file.name === fileName);
    //         if (index !== -1) {
    //             files.value.splice(index, 1);
    //         }
    //     },
    //     onError: (error) => {
    //         console.error("Error deleting file:", error);
    //     }
    // });
};

const isImage = (url) => /\.(gif|jpe?g|tiff?|png|webp|bmp)$/i.test(url);
const isVideo = (url) => /\.(mp4|webm|ogg)$/i.test(url);
</script>

<template>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl mb-4">
            {{ props.name }}
        </h1>
        <div class="mb-4 flex items-center space-x-2 mt-8">
            <div class="sm:visible w-16">
                <FloatLabel>
                    <label for="columns">
                        {{ translate('modules.file.columns') }}
                    </label>
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

            <Button severity="primary" size="small" @click="toggleSortOrder">
                <i :class="sortOrder === 'desc' ? 'pi pi-sort-amount-down' : 'pi pi-sort-amount-up'"></i>
                {{ translate(sortOrder === 'desc' ? 'global.sort_newest' : 'global.sort_oldest') }}
            </Button>

            <Button v-if="videos.length > 0" severity="primary" size="small" @click="toggleAutoplay">
                <i :class="autoplayEnabled ? 'pi pi-pause' : 'pi pi-play'"></i>
                {{
                    autoplayEnabled ? translate('modules.file.disable_autoplay') : translate('modules.file.enable_autoplay')
                }}
            </Button>

            <Button v-if="selectedFiles.length > 0" severity="success" size="small"
                    @click="addToFavoritesSelectedFiles">
                <i class="pi pi-star"></i> {{ translate('modules.file.favorite_selected') }}
            </Button>

            <Button v-if="selectedFiles.length > 0" severity="danger" size="small" @click="deleteSelectedFiles">
                <i class="pi pi-trash"></i> {{ translate('modules.file.delete_selected') }}
            </Button>

            <Button v-if="selectedFiles.length > 0" severity="secondary" size="small" @click="selectedFiles = []">
                <i class="pi pi-times-circle"></i> {{ translate('modules.file.deselect_all') }}
            </Button>
        </div>

<!--        <div v-if="filteredDirectories.length > 0"-->
<!--             class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">-->
<!--            <div v-for="dir in directories" :key="dir.path"-->
<!--                 class="bg-gray-200 p-4 rounded-lg shadow cursor-pointer hover:bg-gray-300 text-gray-600 dark:text-gray-800"-->
<!--                 @click="navigateTo(dir.path)">-->
<!--                📂 {{ dir.name }}-->
<!--            </div>-->
<!--        </div>-->

<!--        <div v-if="filteredDirectories.length === 0 && filteredFiles.length === 0">-->
<!--            {{ translate('modules.file.no_folders_or_files_error') }}-->
<!--        </div>-->

        <div v-if="filteredFiles.length === 0">
            {{ translate('modules.file.no_folders_or_files_error') }}
        </div>

        <div :class="fileGridClass">
            <div
                v-for="file in filteredFiles" :key="file.name"
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
                    <Checkbox v-model="selectedFiles" :value="file.name"/>
                    <div class="truncate w-1/2">{{ file.name }}</div>
                    <div class="flex items-center space-x-2">
                        <a :href="file.url" target="_blank" class="text-blue-500 hover:underline">
                            {{ translate('modules.file.raw') }}
                        </a>
                        <a :href="file.url" :download="file.name" class="text-blue-500 hover:underline">
                            {{ translate('modules.file.download') }}
                        </a>
                        <button
                            @click="deleteFile(file.name)"
                            class="text-red-500 hover:text-red-700"
                        >
                            {{ translate('modules.file.delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {usePage} from "@inertiajs/vue3";
import {ref} from "vue";
import {useTranslation} from "../composables/useTranslation.js";

const page = usePage();
const { translate } = useTranslation();
const props = defineProps({
    title: {
        type: String,
        default: null,
    }
});

const files = ref(page.props.latest_files);
</script>

<template>
    <div class="container mx-auto p-4">
       <h1 class="text-2xl">
           {{ translate('modules.home.h1', {title: props.title}) }}
       </h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-5">
            <Card>
                <template #title>
                    {{ translate('modules.home.downloaded_files_count', { count: page.props.downloaded_files_count }) }}
                </template>
            </Card>
            <Card>
                <template #title>
                    {{ translate('modules.home.favorited_files_count', { count: page.props.favorited_files_count }) }}
                </template>
            </Card>
        </div>
    </div>
</template>

<template>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl mb-4">
            {{ translate('modules.emailSetting.h1') }}
        </h1>
        <div class="flex gap-2">
            <Button @click="goToIndex">
                {{ translate('global.back') }}
            </Button>
            <Button @click="goToCreate">
                {{ translate('global.create') }}
            </Button>
        </div>
        <div>
            <div
                v-for="dataRow in dataTableData.data"
                :key="dataRow.id"
                class="card mt-5"
                @click="goToEdit(dataRow.id)"
            >
                {{dataRow}}
            </div>
        </div>
    </div>
</template>

<script setup>
import {router} from "@inertiajs/vue3";
import {usePage} from "@inertiajs/vue3";
import {ref} from "vue";
import {route} from "ziggy-js";
import {useTranslation} from "../../../composables/useTranslation.js";

const page = usePage();
const {translate} = useTranslation();

const dataTableData = ref(page.props.data_table);

function goToIndex() {
    router.get(route('dashboard'));
}

function goToCreate() {
    router.get(route('modules.email-settings.create'));
}

function goToEdit(emailSettingId) {
    router.get(route('modules.email-settings.show', { emailSetting: emailSettingId }));
}
</script>

<template>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl mb-4">
            {{ translate('modules.emailInboxSetting.h1') }}
        </h1>
        <div class="flex gap-2 justify-end">
            <Button size="small" @click="goToImportIndex">
                <i class="pi pi-plus"></i> {{ translate('global.go_to_import_index') }}
            </Button>
            <Button size="small" @click="goToCreate">
                <i class="pi pi-plus"></i> {{ translate('global.create') }}
            </Button>
            <Button size="small" @click="goToIndex">
                <i class="pi pi-times"></i> {{ translate('global.back') }}
            </Button>
        </div>
        <div>
            <div class="card mt-5">
                <MainDataTable
                    v-model:data-table-data="dataTableData"
                    :edit-route-fn="(item) => getShowRoute(item)"
                    @refresh="fetchData"
                >
                    <Column field="id" header="id"></Column>
                    <Column field="name" header="name"></Column>
                </MainDataTable>
            </div>
        </div>
    </div>
</template>

<script setup>
import {router} from "@inertiajs/vue3";
import {usePage} from "@inertiajs/vue3";
import {ref} from "vue";
import {route} from "ziggy-js";
import MainDataTable from "../../../components/main/MainDataTable.vue";
import {useTranslation} from "../../../composables/useTranslation.js";

const page = usePage();
const {translate} = useTranslation();

const dataTableData = ref(page.props.data_table);

function getShowRoute(item) {
    return route('modules.email-inbox-settings.show', { emailInboxSetting: item.data.id });
}

function goToIndex() {
    router.get(route('dashboard'));
}

function goToCreate() {
    router.get(route('modules.email-inbox-settings.create'));
}

function goToImportIndex() {
    router.get(route('modules.email-inbox-settings.import.index'));
}

async function fetchData(event = null) {
    router.get(route("modules.email-inbox-settings.index"), {page: event.page + 1}, {
        preserveState: true,
        replace: true,
        only: ['data_table'],
        onSuccess: (page) => {
            dataTableData.value = page.props.data_table;
        }
    });
}
</script>

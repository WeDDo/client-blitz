<template>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl mb-4">
            {{ translate('modules.emailMessage.h1') }}
        </h1>
        <div class="flex gap-2 justify-end">
            <Button size="small" @click="fetchEmails">
                <i class="pi pi-plus"></i> {{ translate('global.fetch') }}
            </Button>
            <Button size="small" @click="goToIndex">
                <i class="pi pi-times"></i> {{ translate('global.back') }}
            </Button>
        </div>
        <div>
            <div class="card mt-5">
                <MainDataTable
                    v-model:data-table-data="dataTableData"
                    :edit-route-fn="(item) => route('modules.email-messages.show', { emailMessage: item.data.id })"
                    @refresh="fetchData"
                >
                    <Column field="id" header="id"></Column>
                    <Column field="subject" header="subject"></Column>
                    <Column field="from" header="from"></Column>
                    <Column field="to" header="to"></Column>
                    <Column field="cc" header="cc"></Column>
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

function goToIndex() {
    router.get(route('dashboard'));
}

function fetchEmails() {
    router.get(route('modules.email-messages.get-emails-using-imap'), {}, {
        onSuccess: () => {
            router.reload();
        }
    });
}

function goToEdit(emailSettingId) {
    router.get(route('modules.email-messages.show', { emailSetting: emailSettingId }));
}

async function fetchData(event = null) {
    router.get(route('modules.email-messages.index'), {page: event.page + 1}, {});
}
</script>

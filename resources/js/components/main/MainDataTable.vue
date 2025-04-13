<script setup>
import {router} from "@inertiajs/vue3";
import {useConfirm} from "primevue";
import {useToast} from "primevue";
import {nextTick} from "vue";
import {onMounted} from "vue";
import {computed} from "vue";
import {ref} from "vue";
import {route} from "ziggy-js";

const props = defineProps({
    paginate: {
        type: Boolean,
        default: true,
    },
    editRouteFn: {
        type: Function,
        default: null,
    },
    deleteRoute: {
        type: String,
        default: null,
    }
});

const confirm = useConfirm();
const toast = useToast();

const dataTable = ref();
const selection = ref([]);

const dataTableData = defineModel('dataTableData');

const emit = defineEmits([
    'refresh'
]);

function handleRowDblClick(item) {
    router.get(props.editRouteFn(item));
}

function handlePageChange(event) {
    selection.value = null;
    emit('refresh', event)
}

function confirmDelete(event) {
    confirm.require({
        target: event.currentTarget,
        message: 'Do you want to delete this record?',
        icon: 'pi pi-info-circle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Delete',
            severity: 'danger'
        },
        accept: () => {
            const currentPage = dataTableData.value.current_page;
            const remainingItems = dataTableData.value.data.length - selectionIds.value.length;
            const shouldGoToPrevPage = remainingItems <= 0 && currentPage > 1;
            const newPage = shouldGoToPrevPage ? currentPage - 1 : currentPage;

            router.delete(props.deleteRoute, {
                data: { ids: selectionIds.value },
                onSuccess: () => {
                    emit('refresh', { page: newPage - 1 });
                    selection.value = [];
                },
                onError: () => {
                    toast.add({
                        severity: 'error',
                        summary: 'Error',
                        detail: 'Could not delete selected records',
                        life: 3000
                    });
                }
            });
        },
        reject: () => {}
    });
};

const selectionIds = computed(() =>
    Array.isArray(selection.value) ? selection.value.map(item => item.id) : [],
);

onMounted(() => {
    nextTick(() => {
        const container = dataTable.value?.$el?.querySelector('.p-datatable-table-container')
        if (container) {
            container.style.height = '55vh';
        }
    })
})
</script>

<template>
    <div>
        <DataTable
            ref="dataTable"
            v-model:selection="selection"
            data-key="id"
            selection-mode="multiple"
            meta-key-selection
            :value="dataTableData.data"
            tableStyle="min-width: 50rem"
            scrollable
            @row-dblclick="handleRowDblClick"
        >
            <template #header>
                <div class="text-end">
                    <Button
                        label="Delete"
                        severity="secondary"
                        size="small"
                        icon="pi pi-external-link"
                        :disabled="selectionIds.length === 0"
                        @click="confirmDelete"
                    />
                </div>
            </template>
            <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
            <slot></slot>
        </DataTable>
        <div
            v-if="props.paginate"
            class="flex justify-end mt-2"
        >
            <Paginator
                :rows="dataTableData.per_page ?? 0"
                :total-records="dataTableData.total ?? 0"
                :first="((dataTableData.current_page ?? 0) - 1) * (dataTableData.per_page ?? 0)"
                @page="handlePageChange"
            >
                <template #start="slotProps">
                    <div class="text-sm">
                        <div v-if="(dataTableData.total ?? 0) > 0">
                            {{
                                slotProps.state.first + 1
                            }}-{{ slotProps.state.first + Math.min(slotProps.state.rows, dataTableData.total) }} of
                            {{ dataTableData.total }}
                        </div>
                        <div v-else>
                            No data
                        </div>
                    </div>
                </template>
            </Paginator>
        </div>
        <ConfirmPopup></ConfirmPopup>
    </div>
</template>

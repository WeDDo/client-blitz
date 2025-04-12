<script setup>
import {router} from "@inertiajs/vue3";
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
});

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
</script>

<template>
    <div>
        <DataTable
            v-model:selection="selection"
            data-key="id"
            selection-mode="multiple"
            meta-key-selection
            :value="dataTableData.data"
            tableStyle="min-width: 50rem"
            scrollable
            scroll-height="500px"
            @row-dblclick="handleRowDblClick"
        >
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
    </div>
</template>

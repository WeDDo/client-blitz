<script setup>
import {router} from "@inertiajs/vue3";
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

const dataTableData = defineModel('dataTableData');

const emit = defineEmits([
    'refresh'
]);

function handleRowDblClick(item) {
    router.get(props.editRouteFn(item));
}
</script>

<template>
    <div>
        <DataTable
            :value="dataTableData.data"
            tableStyle="min-width: 50rem"
            scrollable
            scroll-height="500px"
            @row-dblclick="handleRowDblClick"
        >
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
                @page="emit('refresh', $event)"
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

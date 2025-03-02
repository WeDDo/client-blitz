<script setup>
import {useTranslation} from "../../composables/useTranslation.js";

const props = defineProps({
    name: {
        type: String,
        default: 'name',
    },
    label: {
        type: String,
        default: undefined,
    },
    errors: {
        type: Object,
        default: () => {},
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    required: {
        type: Boolean,
        default: false,
    },
    options: {
        type: Array,
        default: () => [],
    },
    optionLabel: {
        type: String,
        default: 'name',
    },
});

const { translate } = useTranslation();

const value = defineModel('value');
</script>

<template>
    <div>
<!--        <FloatLabel>-->
            <label :for="props.name" class="text-sm">
                {{ label }} <span v-if="props.required">*</span>
            </label>
            <MultiSelect
                v-model="value"
                :options="options"
                optionLabel="name"
                filter
                :maxSelectedLabels="5"
                fluid
                :placeholder="translate('global.nothing_selected')"
                display="chip"
                :invalid="!!props.errors?.value?.[props.name]"
            />
<!--        </FloatLabel>-->
        <Message
            v-if="props.errors?.value?.[props.name]"
            severity="error"
            size="small"
            variant="simple"
        >
            {{ props.errors?.value?.[props.name] }}
        </Message>
    </div>
</template>

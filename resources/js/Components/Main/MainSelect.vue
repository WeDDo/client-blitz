<script setup>
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
    size: {
        type: String,
        default: undefined,
    },
});

const value = defineModel('value');
const emit = defineEmits([
    'change'
]);
</script>

<template>
    <div>
<!--        <FloatLabel>-->
            <label :for="props.name" class="text-sm">
                {{ label }} <span v-if="props.required">*</span>
            </label>
            <Select
                v-model="value"
                :options="options"
                optionLabel="name"
                :maxSelectedLabels="5"
                fluid
                placeholder="-"
                :size="size"
                :invalid="!!props.errors?.value?.[props.name]"
                @change="emit('change', $event)"
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

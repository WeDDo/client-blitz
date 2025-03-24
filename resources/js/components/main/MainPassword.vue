<script setup>
const props = defineProps({
    name: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        default: undefined,
    },
    errors: {
        type: Object,
        default: () => {}, // ✅ Ensure this is always an object
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    required: {
        type: Boolean,
        default: false,
    },
});

const value = defineModel('value');
</script>

<template>
    <div>
            <label :for="props.name" class="text-sm">
                {{ label }} <span v-if="props.required">*</span>
            </label>
            <Password
                v-model="value"
                :id="props.name"
                :name="props.name"
                fluid
                :feedback="false"
                :invalid="!!props.errors?.value?.[props.name]"
            />
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

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
<!--        <FloatLabel>-->
            <label :for="props.name" class="text-sm">
                {{ label }} <span v-if="props.required">*</span>
            </label>
            <InputText
                v-model="value"
                :id="props.name"
                :name="props.name"
                type="text"
                fluid
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

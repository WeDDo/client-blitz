<template>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl mb-4">
            {{ translate('modules.emailInboxSetting.import.h1') }}
        </h1>
        <div class="flex gap-2 justify-end">
            <Button
                size="small"
                :disabled="isNoFolders()"
                @click="handleCreate"
            >
                <i class="pi pi-save"></i> {{ translate('global.save') }}
            </Button>
            <Button size="small" @click="goToIndex">
                <i class="pi pi-times"></i> {{ translate('global.back') }}
            </Button>
        </div>
        <div>
            <MainForm
                v-model:form="form"
            />
        </div>
    </div>
</template>

<script setup>
import {router} from "@inertiajs/vue3";
import {usePage} from "@inertiajs/vue3";
import {useForm} from "vee-validate";
import {ref} from "vue";
import {route} from "ziggy-js";
import MainForm from "../../../../components/modules/email-inbox-settings/import/MainForm.vue";
import {useFormValidation} from "../../../../composables/modules/email-inbox-settings/import/useFormValidation.js";
import {useTranslation} from "../../../../composables/useTranslation.js";

const page = usePage();
const {translate} = useTranslation();
const {schema, initialValues} = useFormValidation();

const isLoading = ref(false);

const form = useForm({
    validationSchema: schema,
    initialValues: initialValues,
});

const handleCreate = form.handleSubmit((values) => {
    isLoading.value = true;
    router.post(route('modules.email-inbox-settings.import.store'), values, {
        onFinish: () => {
            isLoading.value = false;
        },
        onError: (errors) => {
            form.setErrors(errors);
        }
    });
});

function goToIndex() {
    router.get(route('modules.email-inbox-settings.index'));
}

function isNoFolders() {
    return (
        Array.isArray(form.values.folders) &&
        form.values.folders.length === 2 &&
        form.values.folders.every(item => Array.isArray(item) && item.length === 0)
    );
}
</script>

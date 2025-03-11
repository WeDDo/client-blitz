<template>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl mb-4">
            {{ translate('modules.emailSetting.h1') }}
        </h1>
        <div class="flex gap-2 justify-end">
            <Button size="small" @click="handleUpdate">
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
import MainForm from "../../../components/modules/email-settings/MainForm.vue";
import {useFormValidation} from "../../../composables/modules/useFormValidation.js";
import {useTranslation} from "../../../composables/useTranslation.js";

const page = usePage();
const {translate} = useTranslation();
const {schema, initialValues} = useFormValidation();

const isLoading = ref(false);

const form = useForm({
    validationSchema: schema,
    initialValues: page.props.item
});

const handleUpdate = form.handleSubmit((values) => {
    isLoading.value = true;
    router.put(route('modules.email-settings.update', {emailSetting: values.id}), form.values, {
        onFinish: () => {
            isLoading.value = false;
        },
        onError: (errors) => {
            form.setErrors(errors);
        }
    });
});

function goToIndex() {
    router.get(route('modules.email-settings.index'));
}
</script>

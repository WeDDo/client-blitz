<template>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl mb-4">
            {{ translate('modules.emailSetting.h1') }}
        </h1>
        <div class="flex gap-2 justify-end">
            <Button size="small" :severity="connectionSeverity" @click="checkConnection">
                <i class="pi pi-check"></i> {{ translate('global.check_connection') }}
            </Button>
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
import {useFormValidation} from "../../../composables/modules/email-settings/useFormValidation.js";
import {useTranslation} from "../../../composables/useTranslation.js";

const page = usePage();
const {translate} = useTranslation();
const {schema, initialValues} = useFormValidation();

const connectionSeverity = ref('secondary');
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

const goToIndex = () => {
    router.get(route('modules.email-settings.index'));
}

const checkConnection = () => {
    router.get(route('modules.email-settings.check-connection', form.values.id), {}, {
        preserveState: true,
        onSuccess: (response) => {
            if(response.props.flash.success) {
                connectionSeverity.value = 'success';
            } else {
                connectionSeverity.value = 'danger';
            }
        },
    });
};
</script>

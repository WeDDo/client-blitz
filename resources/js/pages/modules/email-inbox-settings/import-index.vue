<template>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl mb-4">
            {{ translate('modules.emailInboxSetting.import.h1') }}
        </h1>
        <div class="flex gap-2 justify-end">
            <Button size="small" @click="handleCreate">
                <i class="pi pi-save"></i> {{ translate('global.save') }}
            </Button>
            <Button size="small" @click="goToIndex">
                <i class="pi pi-times"></i> {{ translate('global.back') }}
            </Button>
        </div>
        <div>
            <PickList v-model="data" listStyle="height:342px" dataKey="id" breakpoint="1400px">
                <template #sourceheader>
                    Available
                </template>
                <template #targetheader>
                    Selected
                </template>
                <template #item="slotProps">
                    <div>
                        {{slotProps.item.name}}
                    </div>
                </template>
            </PickList>
        </div>
    </div>
</template>

<script setup>
import {router} from "@inertiajs/vue3";
import {usePage} from "@inertiajs/vue3";
import {useForm} from "vee-validate";
import {ref} from "vue";
import {route} from "ziggy-js";
import MainForm from "../../../components/modules/email-inbox-settings/MainForm.vue";
import {useFormValidation} from "../../../composables/modules/email-inbox-settings/useFormValidation.js";
import {useTranslation} from "../../../composables/useTranslation.js";

const page = usePage();
const {translate} = useTranslation();
// const {schema, initialValues} = useFormValidation();

const isLoading = ref(false);

// const form = useForm({
//     validationSchema: schema,
//     initialValues: initialValues,
// });

const data = ref(page.props.data);

const handleCreate = () => {
    isLoading.value = true;
    router.post(route('modules.email-inbox-settings.create-inboxes'), data.value, {
        onFinish: () => {
            isLoading.value = false;
        },
        onError: (errors) => {
            // form.setErrors(errors);
        }
    });
}

function goToIndex() {
    router.get(route('modules.email-inbox-settings.index'));
}
</script>

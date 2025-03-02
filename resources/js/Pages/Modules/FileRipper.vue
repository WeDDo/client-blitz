<!--suppress JSCheckFunctionSignatures -->
<template>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl">
            {{ translate('modules.fileripper.h1') }}
        </h1>

        <div class="card mt-5">
            <form>
                <div class="grid grid-cols-3 sm:grid-cols-2 gap-4">
                    <MainMultiSelect
                        v-model:value="boards"
                        name="boards"
                        :label="translate('modules.fileripper.boards')"
                        required
                        :errors="form.errors"
                        :options="options.boards"
                    />
                    <MainInputText
                        v-model:value="keywords"
                        name="keywords"
                        :label="translate('modules.fileripper.keywords')"
                        required
                        :errors="form.errors"
                    />
                </div>
                <Button
                    class="w-full sm:w-auto mt-4"
                    :disabled="isLoading"
                    @click="handleRip"
                >
                    {{ isLoading ? translate('modules.fileripper.processing') : translate('modules.fileripper.rip') }}
                </Button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { useForm } from "vee-validate";
import {computed} from "vue";
import { ref } from "vue";
import * as yup from "yup";
import { router, usePage } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import MainInputText from "../../Components/Main/MainInputText.vue";
import MainMultiSelect from "../../Components/Main/MainMultiSelect.vue";
import {useTranslation} from "../../composables/useTranslation.js";
const page = usePage();

const { translate } = useTranslation();

const options = ref(page.props.selects);
const isLoading = ref(false);

const schema = computed(() => {
    return yup.object({
        boards: yup
            .array()
            .min(1, translate('validation.min.array', { attribute: translate('modules.fileripper.boards'), min: 1 }))
            .required(translate('validation.required', { attribute: translate('modules.fileripper.boards')})),
        keywords: yup
            .string()
            .required(translate('validation.required', { attribute: translate('modules.fileripper.keywords')})),
    });
})

const form = useForm({
    validationSchema: schema,
    initialValues: {
        boards: page.props.boards,
        keywords: page.props.keywords ?? ''
    }
});

const [boards] = form.defineField('boards');
const [keywords] = form.defineField('keywords');

const handleRip = form.handleSubmit((values) => {
    isLoading.value = true;
    router.post(route('fileripper.rip'), form.values, {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => isLoading.value = false,
        onError: (errors) => {
            form.setErrors(errors);
        }
    });
});
</script>


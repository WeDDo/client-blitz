<script setup>
import {router} from "@inertiajs/vue3";
import {usePage} from "@inertiajs/vue3";
import {useForm} from "vee-validate";
import {ref} from "vue";
import * as yup from "yup";
import {route} from "ziggy-js";
import MainInputText from "../../components/main/MainInputText.vue";
import {useTranslation} from "../../composables/useTranslation.js";

const page = usePage();
const { translate } = useTranslation();
const props = defineProps({
    title: {
        type: String,
        default: null,
    },
});

const isLoading = ref(false);
const mainTranslate = ref(page.props.main.translate);

const schema = yup.object({
    email: yup
        .string()
        .required(() => translate('validation.required', { attribute: translate(`${mainTranslate.value}.email`) })),
    password: yup
        .string()
        .required(() => translate('validation.required', { attribute: translate(`${mainTranslate.value}.password`) })),
})

const form = useForm({
    validationSchema: schema,
    initialValues: {
        email: page.props.email,
        password: page.props.password
    },
    initialErrors: page.props.errors ?? {},
});

const [email] = form.defineField('email');
const [password] = form.defineField('password');

const login = form.handleSubmit((values) => {
    isLoading.value = true;
    router.post(route('auth.login'), form.values, {
        preserveState: true,
        onFinish: () => isLoading.value = false,
        onError: (errors) => {
            form.setErrors(errors);
        }
    });
});
</script>

<template>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl">
            {{ translate(`${mainTranslate}.h1`) }}
        </h1>

        <div class="card mt-5">
            <form>
                <div>
                    <MainInputText
                        v-model:value="email"
                        name="email"
                        :label="translate(`${mainTranslate}.email`)"
                        required
                        :errors="form.errors"
                    />
                    <MainInputText
                        v-model:value="password"
                        name="password"
                        :label="translate(`${mainTranslate}.password`)"
                        required
                        :errors="form.errors"
                    />
                </div>
                <Button
                    class="mt-4"
                    fluid
                    :disabled="isLoading"
                    @click="login"
                >
                    {{ translate(`${mainTranslate}.login`) }}
                </Button>
            </form>
        </div>
    </div>
</template>

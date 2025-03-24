<script setup>
import {router} from "@inertiajs/vue3";
import {usePage} from "@inertiajs/vue3";
import {useForm} from "vee-validate";
import {ref} from "vue";
import * as yup from "yup";
import {route} from "ziggy-js";
import MainInputText from "../../components/main/MainInputText.vue";
import MainPassword from "../../components/main/MainPassword.vue";
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
    name: yup
        .string()
        .required(() => translate('validation.required', { attribute: translate(`${mainTranslate.value}.name`) })),
    email: yup
        .string()
        .required(() => translate('validation.required', { attribute: translate(`${mainTranslate.value}.email`) })),
    password: yup
        .string()
        .required(() => translate('validation.required', { attribute: translate(`${mainTranslate.value}.password`) })),
    password_confirmation: yup
        .string()
        .required(() => translate('validation.required', { attribute: translate(`${mainTranslate.value}.password_confirmation`) })),
})

const form = useForm({
    validationSchema: schema,
    initialValues: {
        name: page.props.name,
        email: page.props.email,
        password: page.props.password,
        password_confirmation: null,
    },
    initialErrors: page.props.errors ?? {},
});

const [name] = form.defineField('name');
const [email] = form.defineField('email');
const [password] = form.defineField('password');
const [passwordConfirmation] = form.defineField('password_confirmation');

const login = form.handleSubmit((values) => {
    isLoading.value = true;
    router.post(route('auth.registration'), form.values, {
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
                        v-model:value="name"
                        name="name"
                        :label="translate(`${mainTranslate}.name`)"
                        required
                        :errors="form.errors"
                    />
                    <MainInputText
                        v-model:value="email"
                        name="email"
                        :label="translate(`${mainTranslate}.email`)"
                        required
                        :errors="form.errors"
                    />
                    <MainPassword
                        v-model:value="password"
                        name="password"
                        :label="translate(`${mainTranslate}.password`)"
                        required
                        :errors="form.errors"
                    />
                    <MainPassword
                        v-model:value="passwordConfirmation"
                        name="password_confirmation"
                        :label="translate(`${mainTranslate}.password_confirmation`)"
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
                    {{ translate(`${mainTranslate}.register`) }}
                </Button>
            </form>
        </div>
    </div>
</template>

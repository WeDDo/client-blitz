<script setup>
import {usePage} from "@inertiajs/vue3";
import {useTranslation} from "../../../composables/useTranslation.js";
import MainCheckbox from "../../main/MainCheckbox.vue";
import MainInputText from "../../main/MainInputText.vue";
import MainPassword from "../../main/MainPassword.vue";
import MainSelect from "../../main/MainSelect.vue";

const page = usePage();
const {translate} = useTranslation();

const form = defineModel('form');

const [name] = form.value.defineField('name');
const [type] = form.value.defineField('type');
const [host] = form.value.defineField('host');
const [port] = form.value.defineField('port');
const [encryption] = form.value.defineField('encryption');
// const [validateCert] = form.value.defineField('validate_cert');
const [username] = form.value.defineField('username');
const [password] = form.value.defineField('password');
const [protocol] = form.value.defineField('protocol');
const [active] = form.value.defineField('active');
</script>

<template>
    <div class="card mt-5">
        <form>
            <div class="flex flex-col">
                <MainInputText
                    v-model:value="name"
                    name="name"
                    :label="translate('modules.emailSetting.name')"
                    required
                    :errors="form.errors"
                />
                <MainSelect
                    v-model:value="type"
                    name="type"
                    :label="translate('modules.emailSetting.type')"
                    required
                    :errors="form.errors"
                    :options="page.props.options.types"
                />
                <MainSelect
                    v-model:value="protocol"
                    name="protocol"
                    :label="translate('modules.emailSetting.protocol')"
                    required
                    :errors="form.errors"
                    :options="page.props.options.protocols"
                />
                <MainInputText
                    v-model:value="host"
                    name="host"
                    :label="translate('modules.emailSetting.host')"
                    required
                    :errors="form.errors"
                />
                <MainInputText
                    v-model:value="port"
                    name="port"
                    :label="translate('modules.emailSetting.port')"
                    required
                    :errors="form.errors"
                />
                <MainInputText
                    v-model:value="encryption"
                    name="encryption"
                    :label="translate('modules.emailSetting.encryption')"
                    required
                    :errors="form.errors"
                />
                <MainInputText
                    v-model:value="username"
                    name="username"
                    :label="translate('modules.emailSetting.username')"
                    required
                    :errors="form.errors"
                />
                <MainPassword
                    v-model:value="password"
                    name="password"
                    :label="translate('modules.emailSetting.password')"
                    required
                    :errors="form.errors"
                />
                <MainCheckbox
                    v-model:value="active"
                    name="active"
                    :label="translate('modules.emailSetting.active')"
                    required
                    :errors="form.errors"
                />
            </div>
        </form>
    </div>
</template>

<style scoped>

</style>

<script setup>
import {usePage} from "@inertiajs/vue3";
import {useTranslation} from "../../../composables/useTranslation.js";
import MainCheckbox from "../../main/MainCheckbox.vue";
import MainInputText from "../../main/MainInputText.vue";
import MainSelect from "../../main/MainSelect.vue";

const page = usePage();
const {translate} = useTranslation();

const form = defineModel('form');

const [name] = form.value.defineField('name');
const [readFromInboxName] = form.value.defineField('read_from_inbox_name');
const [autoSetIsSeen] = form.value.defineField('auto_set_is_seen');
const [emailInboxSettingId] = form.value.defineField('email_setting_id');
</script>

<template>
    <div class="card mt-5">
        <form>
            <div class="flex flex-col">
                <MainInputText
                    v-model:value="name"
                    name="name"
                    :label="translate('modules.emailInboxSetting.name')"
                    required
                    :errors="form.errors"
                />
                <MainInputText
                    v-model:value="readFromInboxName"
                    name="read_from_inbox_name"
                    :label="translate('modules.emailInboxSetting.read_from_inbox_name')"
                    required
                    :errors="form.errors"
                />
                <MainSelect
                    v-model:value="emailInboxSettingId"
                    name="email_setting_id"
                    :label="translate('modules.emailSetting.email_setting_id')"
                    required
                    :errors="form.errors"
                    :options="page.props.options.email_settings"
                />
                <MainCheckbox
                    v-model:value="autoSetIsSeen"
                    name="auto_set_is_seen"
                    :label="translate('modules.emailInboxSetting.auto_set_is_seen')"
                    required
                    :errors="form.errors"
                />
<!--                <MainSelect-->
<!--                    v-model:value="protocol"-->
<!--                    name="protocol"-->
<!--                    :label="translate('modules.emailSetting.protocol')"-->
<!--                    required-->
<!--                    :errors="form.errors"-->
<!--                    :options="page.props.options.protocols"-->
<!--                />-->
            </div>
        </form>
    </div>
</template>

<style scoped>

</style>

<script setup>
import {usePage} from "@inertiajs/vue3";
import {useTranslation} from "../../../../composables/useTranslation.js";
import MainSelect from "../../../main/MainSelect.vue";

const page = usePage();
const {translate} = useTranslation();

const form = defineModel('form');

const [folders] = form.value.defineField('folders');
const [imapEmailSettingId] = form.value.defineField('imap_email_setting_id');
</script>

<template>
    <div class="card mt-5">
        <form>
            <div class="flex flex-col">
                <MainSelect
                    v-model:value="imapEmailSettingId"
                    name="imap_email_setting_id"
                    :label="translate('modules.emailSetting.imap_email_setting_id')"
                    required
                    :errors="form.errors"
                    :options="page.props.options"
                />
                <div class="mt-5">
                    <PickList
                        v-model="folders"
                        listStyle="height:342px"
                        dataKey="id"
                        breakpoint="1400px"
                    >
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
        </form>
    </div>
</template>

<style scoped>

</style>

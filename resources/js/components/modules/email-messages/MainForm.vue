<script setup>
import {usePage} from "@inertiajs/vue3";
import {useTranslation} from "../../../composables/useTranslation.js";
import MainCheckbox from "../../main/MainCheckbox.vue";
import MainInputText from "../../main/MainInputText.vue";
import EmailConversation from "./EmailConversation.vue";

const page = usePage();
const {translate} = useTranslation();

const form = defineModel('form');

const [messageId] = form.value.defineField('message_id');
const [subject] = form.value.defineField('subject');
const [from] = form.value.defineField('from');
const [to] = form.value.defineField('to');
const [cc] = form.value.defineField('cc');
const [bcc] = form.value.defineField('bcc');
const [replyTo] = form.value.defineField('reply_to');
const [date] = form.value.defineField('date');
const [bodyText] = form.value.defineField('body_text');
const [bodyHtml] = form.value.defineField('body_html');
const [isSeen] = form.value.defineField('is_seen');
const [isFlagged] = form.value.defineField('is_flagged');
const [isAnswered] = form.value.defineField('is_answered');
const [folder] = form.value.defineField('folder');
const [userId] = form.value.defineField('user_id');
</script>

<template>
    <div class="card mt-5">
        <form>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                <div>
                    <MainInputText
                        v-model:value="subject"
                        name="subject"
                        :label="translate('modules.emailMessage.subject')"
                        :errors="form.errors"
                        disabled
                    />
                    <MainInputText
                        v-model:value="from"
                        name="from"
                        :label="translate('modules.emailMessage.from')"
                        :errors="form.errors"
                        disabled
                    />
                    <MainInputText
                        v-model:value="to"
                        name="to"
                        :label="translate('modules.emailMessage.to')"
                        :errors="form.errors"
                        disabled
                    />
                </div>
                <div>
                    <MainInputText
                        v-model:value="replyTo"
                        name="reply_to"
                        :label="translate('modules.emailMessage.reply_to')"
                        :errors="form.errors"
                        disabled
                    />
                    <MainInputText
                        v-model:value="cc"
                        name="cc"
                        :label="translate('modules.emailMessage.cc')"
                        :errors="form.errors"
                        disabled
                    />
                    <MainInputText
                        v-model:value="bcc"
                        name="bcc"
                        :label="translate('modules.emailMessage.bcc')"
                        :errors="form.errors"
                        disabled
                    />
                </div>
                <div>
                    <MainInputText
                        v-model:value="messageId"
                        name="message_id"
                        :label="translate('modules.emailMessage.message_id')"
                        :errors="form.errors"
                        disabled
                    />
                    <MainInputText
                        v-model:value="date"
                        name="date"
                        :label="translate('modules.emailMessage.date')"
                        :errors="form.errors"
                        disabled
                    />
                    <MainInputText
                        v-model:value="folder"
                        name="folder"
                        :label="translate('modules.emailMessage.folder')"
                        :errors="form.errors"
                        disabled
                    />
                </div>

                <div>
                    <MainCheckbox
                        v-model:value="isSeen"
                        name="is_seen"
                        :label="translate('modules.emailInboxSetting.is_seen')"
                        required
                        :errors="form.errors"
                    />
                    <MainCheckbox
                        v-model:value="isFlagged"
                        name="is_flagged"
                        :label="translate('modules.emailInboxSetting.is_flagged')"
                        required
                        :errors="form.errors"
                    />
                    <MainCheckbox
                        v-model:value="isAnswered"
                        name="is_answered"
                        :label="translate('modules.emailInboxSetting.is_answered')"
                        required
                        :errors="form.errors"
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-2">
                <email-conversation
                    :conversation="page.props.additional.conversation"
                />
            </div>
        </form>
    </div>
</template>

<style scoped>

</style>

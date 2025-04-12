import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import * as yup from "yup";
import {useTranslation} from "../../useTranslation.js";

export function useFormValidation() {
    const page = usePage();
    const {translate} = useTranslation();

    const schema = yup.object({
        name: yup
            .string()
            .required(translate('validation.required', {attribute: translate('modules.emailInboxSetting.name')})),
        read_from_inbox_name: yup
            .string()
            .required(translate('validation.required', {attribute: translate('modules.emailInboxSetting.read_from_inbox_name')})),
        auto_set_is_seen: yup
            .bool()
            .nullable(translate('validation.required', {attribute: translate('modules.emailInboxSetting.auto_set_is_seen')})),
        email_setting_id: yup
            .string()
            .required(translate('validation.required', {attribute: translate('modules.emailInboxSetting.email_setting_id')})),
    });

    const initialValues = {
        name: null,
        read_from_inbox_name: null,
        auto_set_is_seen: false,
        email_setting_id: null,
    };

    return {
        schema,
        initialValues
    };
}

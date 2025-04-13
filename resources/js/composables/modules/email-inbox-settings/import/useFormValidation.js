import { usePage } from '@inertiajs/vue3';
import * as yup from "yup";
import {useTranslation} from "../../../useTranslation.js";

export function useFormValidation() {
    const page = usePage();
    const {translate} = useTranslation();

    const schema = yup.object({
        imap_email_setting_id: yup
            .number()
            .required(translate('validation.required', {attribute: translate('modules.emailInboxSetting.import.imap_email_setting_id')})),

        folders: yup
            .array()
            .required(translate('validation.required', {attribute: translate('modules.emailInboxSetting.read_from_inbox_name')})),
    });

    // const initialValues = {
    //     imap_email_setting_id: null,
    //     folders: [[], []],
    // };

    const initialValues = page.props.data;

    return {
        schema,
        initialValues
    };
}

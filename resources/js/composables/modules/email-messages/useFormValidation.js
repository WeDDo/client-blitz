import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import * as yup from "yup";
import {useTranslation} from "../../useTranslation.js";

export function useFormValidation() {
    const page = usePage();
    const {translate} = useTranslation();

    const schema = yup.object({
        // name: yup
        //     .string()
        //     .required(translate('validation.required', {attribute: translate('modules.emailSetting.name')})),
        // type: yup
        //     .string()
        //     .required(translate('validation.required', {attribute: translate('modules.emailSetting.type')})),
        // host: yup
        //     .string()
        //     .required(translate('validation.required', {attribute: translate('modules.emailSetting.host')})),
        // port: yup
        //     .string()
        //     .required(translate('validation.required', {attribute: translate('modules.emailSetting.port')})),
        // encryption: yup
        //     .string()
        //     .required(translate('validation.required', {attribute: translate('modules.emailSetting.encryption')})),
        // username: yup
        //     .string()
        //     .required(translate('validation.required', {attribute: translate('modules.emailSetting.username')})),
        // password: yup
        //     .string()
        //     .required(translate('validation.required', {attribute: translate('modules.emailSetting.password')})),
        // protocol: yup
        //     .string()
        //     .required(translate('validation.required', {attribute: translate('modules.emailSetting.protocol')})),
        // active: yup
        //     .bool()
        //     .required(translate('validation.required', {attribute: translate('modules.emailSetting.active')})),
    });

    const initialValues = page.props.item;

    return {
        schema,
        initialValues
    };
}

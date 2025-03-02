import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useTranslation() {
    const page = usePage();

    const translations = computed(() => page.props.translations || {});
    const locale = computed(() => page.props.locale || 'en');

    const translate = (key, replacements = {}) => {
        let translatedText = translations.value[key] || key;
        Object.entries(replacements).forEach(([variable, value]) => {
            translatedText = translatedText.replace(`:${variable}`, value);
        });

        return translatedText;
    };

    return {
        translate,
        locale,
    };
}

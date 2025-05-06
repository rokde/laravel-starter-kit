import translations from '@/i18n/translations';
import { createI18n } from 'vue-i18n';

const i18n = createI18n({
    locale: 'en',
    fallbackLocale: 'en',
    messages: translations,
});

export const setLocale = (locale: string): void => {
    if (translations[locale]) {
        i18n.global.locale = locale;
    } else {
        console.error(`Locale ${locale} not found.`);
    }
};

export default i18n;

export function getI18n(): unknown {
    return i18n.global;
}

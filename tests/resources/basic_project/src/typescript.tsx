import React from 'react';
import { useTranslation } from 'react-i18next';

export function MyComponent() {
    const { t, i18n } = useTranslation();
    // or const [t, i18n] = useTranslation();
    let a = t('After you press the "Create campaign" button the campaign will be created and sent to the moderation. You can change it anytime');
    return <p>{t('my translated text')}</p>
}
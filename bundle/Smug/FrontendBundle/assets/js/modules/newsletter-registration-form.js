import { createApp } from 'vue';
import { createI18n } from 'vue-i18n/index';
import de from '../i18n/de.json';
import NewsletterRegistrationForm from '../components/common/Forms/NewsletterRegistrationForm';
import store from '../store';

const i18n = createI18n({
  locale: 'de',
  fallbackLocale: 'de',
  messages: {
    'de': de
  },
})

let app=createApp(NewsletterRegistrationForm)
app.use(i18n);
app.use(store);
app.mount("#newsletter-registration-form");
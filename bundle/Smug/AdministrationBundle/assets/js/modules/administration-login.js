import { createApp } from 'vue';
import { createI18n } from 'vue-i18n/index';
import de from '../i18n/de.json';
import en from '../i18n/en.json';
import Login from '../components/common/Login/Login';
import store from '../store';

const i18n = createI18n({
  locale: window.localStorage.getItem('lang') ?? 'de',
  fallbackLocale: 'de',
  messages: {
    'de': de,
    'en': en
  },
})

const section = document.getElementById('administration-login'); 

let app=createApp(Login)
app.use(i18n);
app.use(store);
app.mount("#administration-login");
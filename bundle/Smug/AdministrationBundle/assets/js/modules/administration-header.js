import { createApp } from 'vue';
import store from '../store';
import Header from '../components/common/Main/Header.vue';
import { createI18n } from 'vue-i18n/index';
import de from '../i18n/de.json';
import en from '../i18n/en.json';
import Popper from 'vue3-popper';

const i18n = createI18n({
  locale: window.localStorage.getItem('lang') ?? 'de',
  fallbackLocale: 'de',
  messages: {
    'de': de,
    'en': en
  },
})

let app=createApp(Header);
app.use(i18n);
app.use(store);
app.provide('store' , store);
app.component('Popper', Popper);
app.mount("#administration-header");
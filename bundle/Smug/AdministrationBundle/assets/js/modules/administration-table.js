import { createApp } from 'vue';
import store from '../store';
import Table from '../components/common/Table/Table';
import { TippyPlugin } from 'tippy.vue';
import { createI18n } from 'vue-i18n/index';
import de from '../i18n/de.json';
import en from '../i18n/en.json';

const i18n = createI18n({
  locale: window.localStorage.getItem('lang') ?? 'de',
  fallbackLocale: 'de',
  messages: {
    'de': de,
    'en': en
  },
})

const section = document.getElementById('administration-table')

let app=createApp(Table);
app.use(store);
app.use(i18n);
app.use(TippyPlugin);
app.provide('store' , store);
app.provide('dataset', section.dataset);
app.mount("#administration-table");
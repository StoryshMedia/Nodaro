import { createApp } from 'vue';
import store from '../store';
import Main from '../components/common/Main/Main';
import { createI18n } from 'vue-i18n/index';
import de from '../i18n/de.json'

const i18n = createI18n({
  locale: 'de',
  fallbackLocale: 'de',
  messages: {
    'de': de
  }
})

let app=createApp(Main);
app.use(store);
app.use(i18n);
app.provide('store' , store);
app.mount("#administration-main");
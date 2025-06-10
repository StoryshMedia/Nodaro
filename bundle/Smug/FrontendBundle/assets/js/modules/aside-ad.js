import { createApp } from 'vue';
import { createI18n } from 'vue-i18n/index';
import de from '../i18n/de.json';
import AsideAd from '../components/common/Content/AsideAd';
import store from '../store';

const i18n = createI18n({
  locale: 'de',
  fallbackLocale: 'de',
  messages: {
    'de': de
  },
})

let app=createApp(AsideAd)
app.use(i18n);
app.use(store);
app.mount("#aside-ad");
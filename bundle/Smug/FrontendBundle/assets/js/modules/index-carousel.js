import { createApp } from 'vue';
import { createI18n } from 'vue-i18n/index';
import de from '../i18n/de.json';
import IndexCarousel from '../components/home/IndexCarousel';

const i18n = createI18n({
  locale: 'de',
  fallbackLocale: 'de',
  messages: {
    'de': de
  },
})

let app=createApp(IndexCarousel)
app.use(i18n);
app.mount("#index-carousel");
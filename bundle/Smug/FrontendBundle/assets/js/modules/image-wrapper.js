import { createApp } from 'vue';
import { createI18n } from 'vue-i18n/index';
import de from '../i18n/de.json';
import ImageWrapper from '../components/common/Image/ImageWrapper';

const i18n = createI18n({
  locale: 'de',
  fallbackLocale: 'de',
  messages: {
    'de': de
  },
})
const section = document.getElementById('image-wrapper'); 

let app=createApp(ImageWrapper)
app.use(i18n);
app.provide('dataset', section.dataset);
app.mount("#image-wrapper");
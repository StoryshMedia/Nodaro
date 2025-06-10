import { createApp } from 'vue';
import { createI18n } from 'vue-i18n/index';
import de from '../i18n/de.json';
import DownloadButton from '../components/common/Content/DownloadButton';
import store from '../store';

const i18n = createI18n({
  locale: 'de',
  fallbackLocale: 'de',
  messages: {
    'de': de
  },
})
const section = document.getElementById('download-button-wrapper'); 

let app=createApp(DownloadButton)
app.use(i18n);
app.use(store);
app.provide('dataset', section.dataset);
app.mount("#download-button-wrapper");
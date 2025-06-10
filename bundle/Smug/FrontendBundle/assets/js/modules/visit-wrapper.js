import { createApp } from 'vue';
import { createI18n } from 'vue-i18n/index';
import de from '../i18n/de.json';
import VisitWrapper from '../components/common/Content/VisitWrapper';

const i18n = createI18n({
  locale: 'de',
  fallbackLocale: 'de',
  messages: {
    'de': de
  },
})

const section = document.getElementById('visit-wrapper'); 

let app=createApp(VisitWrapper)
app.use(i18n);
app.provide('dataset', section.dataset);
app.mount("#visit-wrapper");
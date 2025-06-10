import { createApp } from 'vue';
import { createI18n } from 'vue-i18n/index';
import de from '../i18n/de.json';
import ApiCardWrapper from '../components/common/ItemList/ApiCardWrapper';

const i18n = createI18n({
  locale: 'de',
  fallbackLocale: 'de',
  messages: {
    'de': de
  },
})
const section = document.getElementById('api-card-wrapper'); 

let app=createApp(ApiCardWrapper)
app.use(i18n);
app.provide('dataset', section.dataset);
app.mount("#api-card-wrapper");
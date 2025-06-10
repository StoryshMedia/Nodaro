import { createApp } from 'vue';
import { createI18n } from 'vue-i18n/index';
import de from '../i18n/de.json';
import ItemList from '../components/common/ItemList/ItemList';

const i18n = createI18n({
  locale: 'de',
  fallbackLocale: 'de',
  messages: {
    'de': de
  },
})
const section = document.getElementById('item-list-wrapper'); 

let app=createApp(ItemList)
app.use(i18n);
app.provide('dataset', section.dataset);
app.mount("#item-list-wrapper");
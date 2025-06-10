import { createApp } from 'vue';
import { createI18n } from 'vue-i18n/index';
import de from '../i18n/de.json';
import PaginatedList from '../components/common/ItemList/PaginatedList';
import store from '../store';

const i18n = createI18n({
  locale: 'de',
  fallbackLocale: 'de',
  messages: {
    'de': de
  },
})

const section = document.getElementById('paginated-list'); 

let app=createApp(PaginatedList)
app.use(i18n);
app.use(store);
app.provide('dataset', section.dataset);
app.mount("#paginated-list");
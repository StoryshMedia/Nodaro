import axios from 'axios';
import VueAxios from 'vue-axios';
import vueDebounce from 'vue-debounce';
import { createApp } from 'vue';
import { createI18n } from 'vue-i18n/index';
import de from '../i18n/de.json';
import SearchList from '../pages/SearchList';

const i18n = createI18n({
  locale: 'de',
  fallbackLocale: 'de',
  messages: {
    de: de
  },
})

let app=createApp(SearchList)
app.use(vueDebounce, {
  listenTo: ['input', 'keyup']
})
app.use(i18n);
app.use(VueAxios, axios);
app.use(store);
app.mount("#searchList");
import { createApp } from 'vue';
import store from '../store';
import Tabs from '../components/common/Tabs/Tabs';
import { TippyPlugin } from 'tippy.vue';
import { createI18n } from 'vue-i18n/index';
import de from '../i18n/de.json';
import en from '../i18n/en.json';
import { QuillEditor } from '@vueup/vue-quill/dist/vue-quill.global.prod';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import VueEasyLightbox from 'vue-easy-lightbox';
import PerfectScrollbar from 'vue3-perfect-scrollbar';
import Popper from 'vue3-popper';

const i18n = createI18n({
  locale: window.localStorage.getItem('lang') ?? 'de',
  fallbackLocale: 'de',
  messages: {
    'de': de,
    'en': en
  },
})

const section = document.getElementById('administration-tabs')

let app=createApp(Tabs);
app.use(store);
app.use(i18n);
app.use(TippyPlugin);
app.use(VueEasyLightbox);
app.provide('store' , store);
app.component('QuillEditor', QuillEditor);
app.component('Popper', Popper);
app.use(PerfectScrollbar);
app.provide('dataset', section.dataset);
app.mount("#administration-tabs");
import { createApp } from 'vue';
import { createI18n } from 'vue-i18n/index';
import de from '../i18n/de.json';
import en from '../i18n/en.json';
import AdministrationSidebarNavigation from '../components/common/Navigation/AdministrationSidebarNavigation';
import 'vue3-perfect-scrollbar/dist/vue3-perfect-scrollbar.css';
import PerfectScrollbar from 'vue3-perfect-scrollbar';
import store from '../store';
import vue3JsonExcel from 'vue3-json-excel';
import Popper from 'vue3-popper';
import { TippyPlugin } from 'tippy.vue';

const i18n = createI18n({
  locale: window.localStorage.getItem('lang') ?? 'de',
  fallbackLocale: 'de',
  messages: {
    'de': de,
    'en': en
  },
})

const section = document.getElementById('administration-sidebar-navigation'); 

let app=createApp(AdministrationSidebarNavigation)
app.use(i18n);
app.use(store);
app.use(PerfectScrollbar);
app.use(TippyPlugin);
app.use(vue3JsonExcel);
app.provide('dataset', section.dataset);
app.component('Popper', Popper);
app.mount("#administration-sidebar-navigation");
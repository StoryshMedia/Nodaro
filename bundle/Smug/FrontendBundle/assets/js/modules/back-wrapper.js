import { createApp } from 'vue';
import { createI18n } from 'vue-i18n/index';
import Back from '../components/common/Content/Back';

const section = document.getElementById('back-wrapper'); 

let app=createApp(Back)
app.provide('dataset', section.dataset);
app.mount("#back-wrapper");
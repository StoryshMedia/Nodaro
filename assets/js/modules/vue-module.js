import { createApp } from 'vue';
import store from '../store';
import { createI18n } from 'vue-i18n/index';
import axios from "axios";
import { QuillEditor } from '@vueup/vue-quill';
import { TippyPlugin } from 'tippy.vue';
import VueFinalModal from 'vue-final-modal';
import vueDebounce from 'vue-debounce';
import VueEasyLightbox from 'vue-easy-lightbox';
import PerfectScrollbar from 'vue3-perfect-scrollbar';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import Popper from 'vue3-popper';

class VueModule {
  constructor() {
    this.observers = new Map();
  }

  async mountComponent(el, component, options) {
    if (el.__vue_app__) return;

    const app = createApp(component);
    const lang = localStorage.getItem('lang') ?? 'de';
    const contexts = await require.context('./locales', true, /\.json$/);
    const messages = contexts(`./${lang}.json`);

    const i18n = createI18n({
      locale: lang,
      fallbackLocale: 'de',
      messages: { [lang]: messages }
    });

    app.use(i18n);

    if (options.useStore) app.use(store);
    if (options.provideDataset) app.provide('dataset', el.dataset);
    if (options.useEditor) app.component('QuillEditor', QuillEditor);
    if (options.useTooltip) app.use(TippyPlugin);
    if (options.usePopper) app.component('Popper', Popper);
    if (options.usePerfectScrollbar) app.use(PerfectScrollbar);
    if (options.useLightbox) app.use(VueEasyLightbox);
    if (options.useModal) app.use(VueFinalModal());
    if (options.useDebounce) {
      app.use(vueDebounce, {
        listenTo: ['input', 'keyup']
      });
    }

    app.config.globalProperties.axios = axios.create();
    app.mount(`#${el.id}`);
  }

  async tryMount(identifier, component, options, dynamic) {
    const selector = (dynamic === true) ? `[id^=${identifier}-]` : `[id^=${identifier}]`;
    const elements = document.querySelectorAll(selector);

    for (const el of elements) {
      await this.mountComponent(el, component, options);
    }

    return elements.length > 0;
  }

  observe(identifier, component, options, maxTries = 10, interval = 300, dynamic) {
    if (this.observers.has(identifier)) return;

    let tries = 0;

    const observer = new MutationObserver(async () => {
      const mounted = await this.tryMount(identifier, component, options, dynamic);
      if (mounted || tries >= maxTries) {
        observer.disconnect();
        this.observers.delete(identifier);
      }
      tries++;
    });

    observer.observe(document.body, {
      childList: true,
      subtree: true,
      attributes: false
    });

    this.observers.set(identifier, observer);
  }

  async init(identifier, component, options = {}, dynamic = false) {
    const mounted = await this.tryMount(identifier, component, options, dynamic);
    if (!mounted) {
      this.observe(identifier, component, options, dynamic);
    }
  }
}

export default new VueModule();
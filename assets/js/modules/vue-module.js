import { createApp } from 'vue';
import store from '../store';
import { createI18n } from 'vue-i18n/index';
import axios from "axios";
import VueFinalModal from 'vue-final-modal';
import vueDebounce from 'vue-debounce';
import { QuillEditor } from '@vueup/vue-quill/dist/vue-quill.global.prod';
import { TippyPlugin } from 'tippy.vue';
import VueEasyLightbox from 'vue-easy-lightbox';
import PerfectScrollbar from 'vue3-perfect-scrollbar';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import Popper from 'vue3-popper';

class VueModule {
  async create(module, options) {
    const section = document.getElementById(options.identifier);

    if (section) {
      return this.mount(section, module, options);
    }
  }
  async observeAndMount({ identifier, component, options, maxTries = 10, interval = 300, dynamic = true }) {
    return new Promise((resolve, reject) => {
  
      const observer = new MutationObserver(() => {
        setTimeout(this.tryMount(identifier, component, options, maxTries, interval, dynamic), 100);
      });
  
      const onReady = () => {
        const container = document.body || document.documentElement;
        observer.observe(container, {
          childList: true,
          subtree: true,
          attributes: true,
          characterData: true
        });
  
        resolve(this.tryMount(observer, identifier, component, options, maxTries, interval, dynamic));
      };
  
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', onReady);
      } else {
        onReady();
      }
    });
  }
  async tryMount(observer, identifier, component, options, maxTries = 10, interval = 300, dynamic = true) {
    let tries = 0;
    let resolved = false;
    const selector = (dynamic === true) ? `[id^=${identifier}-]` : `[id^=${identifier}]`;
    const elements = document.querySelectorAll(selector);
    const created = [];
    

    for (let i = 0; i <= elements.length - 1; i++) {
      const el = elements[i];

      if (!el.__vue_app__) {
        const instance = axios.create();
        const app = createApp(component);
        const lang = localStorage.getItem('lang') ?? 'de';
        const contexts = await require.context('./locales', true, /\.json$/);
        const messages = contexts(`./${lang}.json`);

        let languageConfig = {
          locale: lang,
          fallbackLocale: 'de',
          messages: {
          },
        }
        languageConfig.messages[lang] = messages;

        const i18n = createI18n(languageConfig);
        app.use(i18n);
        
        if (options.useStore && options.useStore === true) {
          app.use(store);
        }
        if (options.provideDataset && options.provideDataset === true) {
          app.provide('dataset', el.dataset);
        }
        if (options.useEditor && options.useEditor === true) {
          app.component('QuillEditor', QuillEditor);
        }
        if (options.useTooltip && options.useTooltip === true) {
          app.use(TippyPlugin);
        }
        if (options.usePopper && options.usePopper === true) {
          app.component('Popper', Popper);
        }
        if (options.usePerfectScrollbar && options.usePerfectScrollbar === true) {
          app.use(PerfectScrollbar);
        }
        if (options.useLightbox && options.useLightbox === true) {
          app.use(VueEasyLightbox);
        }
        if (options.useModal && options.useModal === true) {
          app.use(VueFinalModal());
        }
        if (options.useDebounce && options.useDebounce === true) {
          app.use(vueDebounce, {
            listenTo: ['input', 'keyup']
          });
        }

        app.config.globalProperties.axios=instance;
        app.mount('#' + el.id);
        resolved = true;
        observer.disconnect();

        created.push({ app, section: el });

        if (i === elements.length - 1) {
          return created;
        }
      }
    }

    if (!resolved && tries < maxTries) {
      tries++;
      setTimeout(this.tryMount(identifier, component, options, maxTries, interval, dynamic), interval);
    }
  }
  setObserver(module, options) {
    return new Promise((resolve) => {
      let that = this;
      var config = { characterData: true, attributes: true, childList: true, subtree: true };
      var MutationObserver = window.MutationObserver || window.WebKitMutationObserver;
      var observer = new MutationObserver((mutationList) => {
        setTimeout(() => {
          const selector = '[id^=' + options.identifier + '-]';
          const elements = document.querySelectorAll(selector);

          for(let iterator = 0; iterator <= elements.length - 1; iterator++) {
            const section = elements[iterator];
            if (typeof section.__vue_app__ === 'undefined') {
              const app = that.mount(section, module, options);
              resolve({
                app: app,
                section: section
              });
            }
          }
        }, 200);
      });
      const container = document.querySelector('#' + options.identifier + '-wrapper') || document.body;
      observer.observe(container, config);
    });
  }
  mount(section, module, options) {
    const instance = axios.create();
    const i18n = createI18n({
      locale: 'de',
      fallbackLocale: 'de',
      messages: {
        'de': de
      },
    });

    let app=createApp(module)
    app.use(i18n);
    
    if (options.useStore && options.useStore === true) {
      app.use(store);
    }
    if (options.provideDataset && options.provideDataset === true) {
      app.provide('dataset', section.dataset);
    }

    app.config.globalProperties.axios=instance;
    return app;
  }
}
export default new VueModule();
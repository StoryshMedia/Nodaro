import { createApp } from 'vue';
import store from '../store';
import { createI18n } from 'vue-i18n/index';
import de from '../i18n/de.json';
import axios from "axios";
import VueFinalModal from 'vue-final-modal';
import vueDebounce from 'vue-debounce';
import { QuillEditor } from '@vueup/vue-quill/dist/vue-quill.global.prod';

class VueModule {
  async create(module, options) {
    const section = document.getElementById(options.identifier);

    if (section) {
      return this.mount(section, module, options);
    }
  }
  observeAndMount({ identifier, component, options, maxTries = 10, interval = 300 }) {
    return new Promise((resolve, reject) => {
      let tries = 0;
      let resolved = false;
  
      const tryMount = () => {
        const selector = `[id^=${identifier}-]`;
        const elements = document.querySelectorAll(selector);
        const created = [];
        
  
        for (let i = 0; i <= elements.length - 1; i++) {
          const el = elements[i];
  
          if (!el.__vue_app__) {
            const instance = axios.create();
            const i18n = createI18n({
              locale: 'de',
              fallbackLocale: 'de',
              messages: {
                'de': de
              },
            });

            const app = createApp(component)
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
          }

          if (i === elements.length - 1) {
            resolve(created);
            return;
          }
        }
  
        if (!resolved && tries < maxTries) {
          tries++;
          setTimeout(tryMount, interval);
        }
      };
  
      const observer = new MutationObserver(() => {
        setTimeout(tryMount, 100);
      });
  
      const onReady = () => {
        const container = document.body || document.documentElement;
        observer.observe(container, {
          childList: true,
          subtree: true,
          attributes: true,
          characterData: true
        });
  
        tryMount();
      };
  
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', onReady);
      } else {
        onReady();
      }
    });
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
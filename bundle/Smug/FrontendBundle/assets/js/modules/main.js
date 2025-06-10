import Main from '../pages/Main';
import VueModule from './vue-module.js';

const asyncMount = async () => {
  const app = VueModule.create(Main, {useStore: true, provideDataset: true, identifier: 'main'});
  return app;
}

const app = await asyncMount();
app.mount("#main");
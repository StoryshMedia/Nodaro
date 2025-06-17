import Main from '../components/common/Main/Main';
import VueModule from '@core/js/modules/vue-module.js';

VueModule.observeAndMount({
  identifier: 'administration-main',
  component: Main,
  options: {useStore: true, provideDataset: true, identifier: 'administration-main'},
  dynamic: false
}).then(({ app, section }) => {
  console.log('Vue erfolgreich gemountet auf:', section);
}).catch(console.error);
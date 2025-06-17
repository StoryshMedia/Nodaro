import Header from '../components/common/Main/Header.vue';
import VueModule from '@core/js/modules/vue-module.js';

VueModule.observeAndMount({
  identifier: 'administration-header',
  component: Header,
  options: {useStore: true, provideDataset: true, usePopper: true, identifier: 'administration-header'},
  dynamic: false
}).then(({ app, section }) => {
  console.log('Vue erfolgreich gemountet auf:', section);
}).catch(console.error);
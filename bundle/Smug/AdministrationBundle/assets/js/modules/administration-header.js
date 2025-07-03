import Header from '../components/common/Main/Header.vue';
import VueModule from '@core/js/modules/vue-module.js';

VueModule.init(
  'administration-header',
  Header,
  {useStore: true, provideDataset: true, usePopper: true, identifier: 'administration-header'},
  false
);
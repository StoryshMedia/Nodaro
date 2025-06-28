import SimpleList from '../components/elements/plugin/list/SimpleList.vue';
import VueModule from '@core/js/modules/vue-module.js';

VueModule.init(
  'simple-list',
  SimpleList,
  {useStore: true, provideDataset: true, identifier: 'simple-list'}
);
import SimplePaginatedList from '../components/elements/plugin/pagination/SimplePaginatedList.vue';
import VueModule from '@core/js/modules/vue-module.js';

VueModule.init(
  'simple-pagination-list',
  SimplePaginatedList,
  {useStore: true, provideDataset: true, identifier: 'simple-pagination-list'}
);
import StandardPaginatedList from '../components/elements/plugin/pagination/StandardPaginatedList.vue';
import VueModule from '@core/js/modules/vue-module.js';

VueModule.init(
  'standard-pagination-list',
  StandardPaginatedList,
  {useStore: true, provideDataset: true, identifier: 'standard-pagination-list'}
);
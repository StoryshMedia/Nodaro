import BigTilePaginationList from '../components/elements/plugin/pagination/BigTilePaginationList.vue';
import VueModule from '@core/js/modules/vue-module.js';

VueModule.init(
  'big-tile-pagination-list',
  BigTilePaginationList,
  {useStore: true, provideDataset: true, identifier: 'big-tile-pagination-list'}
);
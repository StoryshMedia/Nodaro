import MediumTilePaginationList from '../components/elements/plugin/pagination/MediumTilePaginationList.vue';
import VueModule from '@core/js/modules/vue-module.js';

VueModule.init(
  'medium-tile-pagination-list',
  MediumTilePaginationList,
  {useStore: true, provideDataset: true, identifier: 'medium-tile-pagination-list'}
);
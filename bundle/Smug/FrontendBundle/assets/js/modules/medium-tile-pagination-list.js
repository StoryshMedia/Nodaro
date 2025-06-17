import MediumTilePaginationList from '../components/elements/plugin/pagination/MediumTilePaginationList.vue';
import VueModule from '@core/js/modules/vue-module.js';

VueModule.observeAndMount({
  identifier: 'medium-tile-pagination-list',
  component: MediumTilePaginationList,
  options: {useStore: true, provideDataset: true, identifier: 'medium-tile-pagination-list'}
}).then(({ app, section }) => {
  console.log('Vue erfolgreich gemountet auf:', section);
}).catch(console.error);
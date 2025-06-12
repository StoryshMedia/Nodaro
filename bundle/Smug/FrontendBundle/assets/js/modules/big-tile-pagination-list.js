import BigTilePaginationList from '../components/elements/plugin/pagination/BigTilePaginationList.vue';
import VueModule from './@core/js/modules/vue-module.js';

VueModule.observeAndMount({
  identifier: 'big-tile-pagination-list',
  component: BigTilePaginationList,
  options: {useStore: true, provideDataset: true, identifier: 'big-tile-pagination-list'}
}).then(({ app, section }) => {
  console.log('Vue erfolgreich gemountet auf:', section);
}).catch(console.error);
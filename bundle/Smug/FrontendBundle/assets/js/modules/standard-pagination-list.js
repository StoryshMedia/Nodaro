import StandardPaginatedList from '../components/elements/plugin/pagination/StandardPaginatedList.vue';
import VueModule from './vue-module.js';

VueModule.observeAndMount({
  identifier: 'standard-pagination-list',
  component: StandardPaginatedList,
  options: {useStore: true, provideDataset: true, identifier: 'standard-pagination-list'}
}).then(({ app, section }) => {
  console.log('Vue erfolgreich gemountet auf:', section);
}).catch(console.error);
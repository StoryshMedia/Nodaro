import SimplePaginatedList from '../components/elements/plugin/pagination/SimplePaginatedList.vue';
import VueModule from './vue-module.js';

VueModule.observeAndMount({
  identifier: 'simple-pagination-list',
  component: SimplePaginatedList,
  options: {useStore: true, provideDataset: true, identifier: 'simple-pagination-list'}
}).then(({ app, section }) => {
  console.log('Vue erfolgreich gemountet auf:', section);
}).catch(console.error);
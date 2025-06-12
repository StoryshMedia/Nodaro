
import VueModule from '@core/js/modules/@core/js/modules/vue-module.js';
import NavigationSearch from '../components/common/Search/NavigationSearch.vue';

VueModule.observeAndMount({
  identifier: 'navigation-search',
  component: NavigationSearch,
  options: {useStore: true, provideDataset: true, identifier: 'navigation-search', useDebounce: true}
}).then(({ app, section }) => {
  console.log('Vue erfolgreich gemountet auf:', section);
}).catch(console.error);
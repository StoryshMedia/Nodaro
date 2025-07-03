
import VueModule from '@core/js/modules/vue-module.js';
import NavigationSearch from '../components/common/Search/NavigationSearch.vue';

VueModule.init(
  'navigation-search',
  NavigationSearch,
  {useStore: true, provideDataset: true, identifier: 'navigation-search', useDebounce: true}
);
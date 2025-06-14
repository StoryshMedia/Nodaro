import Tabs from '../components/common/Tabs/Tabs.vue';
import VueModule from '@core/js/modules/vue-module.js';

VueModule.observeAndMount({
  identifier: 'administration-tabs',
  component: Tabs,
  options: {useStore: true, provideDataset: true, identifier: 'administration-tabs', useTooltip: true, usePerfectScrollbar: true},
  dynamic: false
}).then(({ app, section }) => {
  console.log('Vue erfolgreich gemountet auf:', section);
}).catch(console.error);
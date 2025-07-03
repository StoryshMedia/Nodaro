import Tabs from '../components/common/Tabs/Tabs.vue';
import VueModule from '@core/js/modules/vue-module.js';

VueModule.init(
  'administration-tabs',
  Tabs,
  {useStore: true, provideDataset: true, identifier: 'administration-tabs', useTooltip: true, usePerfectScrollbar: true, useEditor: true},
);
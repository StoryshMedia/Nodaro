import Table from '../components/common/Table/Table';
import VueModule from '@core/js/modules/vue-module.js';

VueModule.init(
  'administration-table',
  Table,
  {useStore: true, provideDataset: true, identifier: 'administration-table', useTooltip: true, usePerfectScrollbar: true}
);
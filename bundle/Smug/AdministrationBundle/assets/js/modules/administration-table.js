import Table from '../components/common/Table/Table';
import VueModule from '@core/js/modules/vue-module.js';

VueModule.observeAndMount({
  identifier: 'administration-table',
  component: Table,
  options: {useStore: true, provideDataset: true, identifier: 'administration-table', useTooltip: true, usePerfectScrollbar: true},
  dynamic: false
}).then(({ app, section }) => {
  console.log('Vue erfolgreich gemountet auf:', section);
}).catch(console.error);
import AdministrationSidebarNavigation from '../components/common/Navigation/AdministrationSidebarNavigation';
import VueModule from '@core/js/modules/vue-module.js';

VueModule.observeAndMount({
  identifier: 'administration-sidebar-navigation',
  component: AdministrationSidebarNavigation,
  options: {useStore: true, provideDataset: true, identifier: 'administration-sidebar-navigation', useTooltip: true, usePopper: true, usePerfectScrollbar: true},
  dynamic: false
}).then(({ app, section }) => {
  console.log('Vue erfolgreich gemountet auf:', section);
}).catch(console.error);
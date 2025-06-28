import AdministrationSidebarNavigation from '../components/common/Navigation/AdministrationSidebarNavigation';
import VueModule from '@core/js/modules/vue-module.js';

VueModule.init(
  'administration-sidebar-navigation',
  AdministrationSidebarNavigation,
  {useStore: true, provideDataset: true, identifier: 'administration-sidebar-navigation', useTooltip: true, usePopper: true, usePerfectScrollbar: true}
);
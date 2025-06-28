import Main from '../components/common/Main/Main';
import VueModule from '@core/js/modules/vue-module.js';

VueModule.init(
  'administration-main',
  Main,
  {useStore: true, provideDataset: true, identifier: 'administration-main'}
);
import Login from '../components/common/Login/Login.vue';
import VueModule from '@core/js/modules/vue-module.js';

VueModule.init(
  'administration-login',
  Login,
  {useStore: true, provideDataset: true, identifier: 'administration-login'}
);
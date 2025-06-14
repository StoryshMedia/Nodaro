import Login from '../components/common/Login/Login';
import VueModule from '@core/js/modules/vue-module.js';

VueModule.observeAndMount({
  identifier: 'administration-login',
  component: Login,
  options: {useStore: true, provideDataset: true, identifier: 'administration-login'},
  dynamic: false
}).then(({ app, section }) => {
  console.log('Vue erfolgreich gemountet auf:', section);
}).catch(console.error);
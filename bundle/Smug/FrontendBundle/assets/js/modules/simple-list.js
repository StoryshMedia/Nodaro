import SimpleList from '../components/elements/plugin/list/SimpleList.vue';
import VueModule from './@core/js/modules/vue-module.js';

VueModule.observeAndMount({
  identifier: 'simple-list',
  component: SimpleList,
  options: {useStore: true, provideDataset: true, identifier: 'simple-list'}
}).then(result => {
  console.log('Vue erfolgreich gemountet auf:', result);
}).catch(console.error);
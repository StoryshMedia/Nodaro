import TagList from '../components/common/Content/TagList';
import VueModule from './vue-module.js';

VueModule.observeAndMount({
  identifier: 'tag-list',
  component: TagList,
  options: {useStore: true, provideDataset: true, identifier: 'tag-list'}
}).then(({ app, section }) => {
  console.log('Vue erfolgreich gemountet auf:', section);
}).catch(console.error);
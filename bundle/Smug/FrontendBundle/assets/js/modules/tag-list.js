import TagList from '../components/common/Content/TagList';
import VueModule from '@core/js/modules/vue-module.js';

VueModule.init(
  'tag-list',
  TagList,
  {useStore: true, provideDataset: true, identifier: 'tag-list'}
);
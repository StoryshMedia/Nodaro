<template>
  <article>
    <section v-if="listItem.slug && item.hidden === false">
      <a
        :href="item.detailLink + listItem.slug"
        class="group w-full flex items-center"
      >
        <div
          class="transition-all mr-6 shadow-md group-hover:shadow-2xl w-20 h-20 rounded-full background"
          style="flex: 0 0 auto;"
          :style="{ 'background-image': 'url(' + getImageSrc(listItem) + ')'}"
        />
        <div class="flex-shrink">
          <h6 class="text-black line-clamp-1 font-sans group-hover:text-primary transition-colors">{{ listItem.title }}</h6>
          <p class="text-dark font-medium text-sm flex items-center">
            <span
              v-html="getTeaser(listItem.teaser ?? '')"
            />
          </p>
        </div>
      </a>
    </section>
    <loading v-if="!listItem.slug" />
  </article>
</template>
    
<script>
import { defineAsyncComponent } from "vue";
import ApiService from '../../../../../../../AdministrationBundle/assets/js/services/api/api.service';
import ImageService from '../../../../../../../AdministrationBundle/assets/js/services/image/image.service';
import TextService from '../../../../../../../AdministrationBundle/assets/js/services/text/text.service';
const Loading = defineAsyncComponent(() =>
  import("../../../../../../../FrontendBundle/assets/js/components/common/Content/Loading.vue" /* webpackChunkName: "loading" */)
);
    
export default {
  name: "ListItem",
  components: {
    Loading
  },
  props: {
    item:{
      type: Object,
      required: true
    }
  },
  data() {
    return {
      expanded: false,
      itemData: {},
      listItem: {}
    }
  },
  mounted() {
    this.itemData = JSON.parse(this.item.itemData ?? '');
    this.getData();
  },
  methods: {
    getTeaser(teaser) {
      return TextService.getOutput(teaser, 144);
    },
    getImageSrc(item) {
      return ImageService.getImageFromItem(item);
    },
    getData() {
      ApiService.post('/fe/api/dynamic/data', this.itemData, false).then(result => {
        this.listItem = result;
      });
    }
  }
}
</script>
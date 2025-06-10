<template>
  <div class="w-full block xl:flex xl:items-center xl:justify-between">
    <div class="block lg:flex md:items-center space-x-0 xl:space-x-8 lg:justify-between">
      <div class="tags-wrapper md:flex md:items-center text-sm lg:text-base">
        <p
          v-for="(item, itemIndex) in items"
          :key="itemIndex"
          class="flex flex-wrap my-3 md:my-0"
          :title="item.title"
        >
          <span class="mr-4 ml-5 text-primary"><icon-tag /></span>
          {{ item.title }}
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import { defineAsyncComponent } from "vue";
const IconTag = defineAsyncComponent(() =>
  import("@core/js/icons/icons/IconTag" /* webpackChunkName: "icon-tag" */)
);

export default {
  name: "TagList",
  components: {
    IconTag
  },
  inject: ['dataset'],
  data() {
    return {
      items: [],
      mode: ''
    }
  },
  async created() {
    await this.setProps();
  },
  methods: {
    setProps() {
      this.items = JSON.parse(this.dataset.items);
      this.mode = this.dataset.mode;
    }
  }
};
</script>

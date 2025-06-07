<template>
  <section class="h-full w-full items-center flex justify-center">
    <button
      id="search-button"
      aria-label="Search Button"
      class="fill-current hover:text-primary transition-all search-btn"
      @click="onShowSearch()"
    >
      <div class="my-auto flex flex-wrap align-middle">
        <icon
          :icon-string="'IconSearch'"
          :class="'w-6 h-6 text-primary'"
        /> <span class="pl-2 hidden xl:block">{{ $t('SEARCH') }}</span>
      </div>
    </button>
  
    <search
      v-if="showSearch === true"
      :show-search="showSearch"
      :window-data="windowData"
      @showSearch="onShowSearch()"
    />
  </section>
</template>
  
<script>
import { defineAsyncComponent } from "vue";
const Icon = defineAsyncComponent(() =>
  import("../../../../../../FrontendBundle/assets/js/icons/Icon" /* webpackChunkName: "icon" */)
);
const Search = defineAsyncComponent(() =>
  import("./Search" /* webpackChunkName: "search" */)
);
  
export default {
  name: "NavigationSearch",
  components: {
    Icon,
    Search
  },
  inject: ['dataset'],
  model: {
    showSearch: false
  },
  data() {
    return {
      windowData: {},
      showSearch: false
    };
  },
  async created() {
    await this.setProps();
  },
  methods: {
    setProps() {
      this.windowData = JSON.parse(this.dataset.props);
    },
    onShowSearch() {
      this.open = false;
      this.showSearch = !this.showSearch;
    }
  }
}
</script>
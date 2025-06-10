<template>
  <section>
    <div
      v-if="listItems.length > 0"
      class="relative py-5 content-center items-center justify-center px-4 mx-2 post-loop-wrapper grid gap-5 lg:gap-10 grid-cols-12"
    >
      <div
        v-for="(item, index) in listItems"
        :key="index"
        class="h-100 col-start-2 md:col-start-0 col-span-10 sm:col-span-6 md:col-span-4 lg:col-span-4 xl:col-span-4 2xl:col-span-3"
      >
        <pagination-card
          :key="index"
          :slug="getLink(item)"
          :item="item"
          :class="'h-100'"
        />
      </div>
    </div>
    <div
      v-else
      class="relative py-5 content-center items-center justify-center px-4 mx-2 post-loop-wrapper grid gap-5 lg:gap-10 grid-cols-12"
    >
      <div
        v-for="(item, index) in configuration.limit"
        :key="index"
        class="h-100 col-start-2 md:col-start-0 col-span-10 sm:col-span-6 md:col-span-4 lg:col-span-4 xl:col-span-4 2xl:col-span-3"
      >
        <loading-tile />
      </div>
    </div>
  </section>
</template>

<script>
import { defineAsyncComponent } from "vue";
import ApiService from '@SmugAdministration/js/services/api/api.service';
const PaginationCard = defineAsyncComponent(() =>
  import("../additional/Pagination/PaginationCard" /* webpackChunkName: "pagination-pagination-card" */)
);
const LoadingTile = defineAsyncComponent(() =>
  import("../../../common/Content/LoadingTile" /* webpackChunkName: "frontend-loading-tile" */)
);

export default {
  name: "SimpleList",
  components: {
    LoadingTile,
    PaginationCard
  },
  inject: ['dataset'],
  props: {
    givenSelectedFilters: {
      type: Array,
      required: false,
      default: null
    }
  },
  data() {
    return {
      showList: true,
      listItems: [],
      configuration: {}
    };
  },
  created() {
    this.start(true);
  },
  methods: {
    async start(init = false) {
      this.showList = false;
      if (init === true) {
        await this.setProps();
      }

      this.getItems();
    },
    async setProps() {
      const props = JSON.parse(this.dataset.props);
      const filter = (props.filter) ? props.filter : {};
      const configuration = {
        limit: (props.itemLimit) ? parseInt(props.itemLimit) : 10,
        detailPage: props.detailPage ?? '/',
        core: (props.core) ? props.core : ''
      }

      this.configuration = {...filter, ...configuration};
    },
    async getItems() {
      await this.getData();
      this.showList = true;
    },
    async getData() {
      await ApiService.post(
        '/fe/api/list/search',
        this.configuration,
        false
      ).then(result => {
        this.listItems = result.results;
      }).catch(function (error) {
      });
    },
    getLink(item) {
      if (this.configuration.detailPage.slice(-1) === '/') {
        return this.configuration.detailPage + item.slug ?? '';
      }

      return this.configuration.detailPage + '/' + item.slug ?? '';
    }
  }
}
</script>

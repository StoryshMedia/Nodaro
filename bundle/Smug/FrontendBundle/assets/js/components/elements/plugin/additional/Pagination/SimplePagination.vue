<template>
  <div
    class="w-full"
  >
    <div
      class="flex flex-wrap"
    >
      <div
        v-if="isLoaded === true"
        class="w-full relative"
      >
        <pagination-items-wrapper
          v-if="wrapperItems && wrapperItems.length > 0"
          :key="paginationKey"
          :list-items="wrapperItems"
          :detail-page="detailPage"
        />

        <div
          v-if="wrapperItems && wrapperItems.length === 0"
          class="w-full"
        >
          <div
            class="rounded text-dark px-4 py-3 mx-3 mt-4"
            role="alert"
          >
            <div class="flex">
              <div class="py-1">
                <svg
                  class="fill-current h-6 w-6 text-dark mr-4"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                ><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z" /></svg>
              </div>
              <div>
                <p class="font-bold">
                  {{ $t('NO_ITEMS_HEADLINE') }}
                </p>
                <p class="text-sm">
                  {{ $t('NO_ITEMS_TEXT') }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div
        v-if="wrapperPages && wrapperPages.mainSteps.length > 0"
        class="w-full px-4"
      >
        <pagination
          :pages="wrapperPages"
          :page="page"
          :last-page="lastPage"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { defineAsyncComponent } from "vue";
const PaginationItemsWrapper = defineAsyncComponent(() =>
  import("./PaginationItemsWrapper.vue" /* webpackChunkName: "pagination-items-wrapper" */)
);
const Pagination = defineAsyncComponent(() =>
  import("./Pagination" /* webpackChunkName: "paginated-list-pagination" */)
);

export default {
  name: "SimplePagination",
  components: {
    Pagination,
    PaginationItemsWrapper
  },
  props: {
    listItems: {
      type: Array,
      required: false,
      default: () => []
    },
    pages: {
      type: Object,
      required: false,
      default: () => {}
    },
    detailPage: {
      type: String,
      required: true
    },
    page: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      isLoaded: false,
      paginationKey: 0
    };
  },
  computed: {
    wrapperItems (props) {
      return props.listItems;
    },
    wrapperPages (props) {
      return props.pages;
    }
  },
  watch: {
    items: function(newVal, oldVal) {
      this.paginationKey += 1;
    }
  },
  mounted() {
    this.isLoaded = true;
    this.paginationKey += 1;
  },
  unmounted() {
    this.isLoaded = false;
  }
}
</script>

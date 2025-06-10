<template>
  <section
    v-if="showList === true && (items && items.length > 0)"
    :key="wrapperKey"
    class="grid gap-5 lg:gap-10 grid-cols-12"
  >
    <div 
      v-for="(item, itemIndex) in items"
      :key="item.id"
      :class="getGridClass()"
    >
      <medium-tile-pagination-card
        :key="itemIndex"
        :slug="getLink(item)"
        :item="item"
      />
    </div>
  </section>
</template>

<script>
import { defineAsyncComponent, ref } from "vue";
const MediumTilePaginationCard = defineAsyncComponent(() =>
  import("./MediumTilePaginationCard" /* webpackChunkName: "medium-tile-pagination-card" */)
);

export default {
  name: "MediumTilePaginationWrapper",
  components: {
    MediumTilePaginationCard
  },
  props: {
    listItems: {
      type: Array,
      required: false,
      default: () => []
    },
    detailPage: {
      type: String,
      required: true
    }
  },
  setup() {
    const showList = ref(false);
    
    return { showList }
  },
  data() {
    return {
      wrapperKey: 0,
      items: []
    };
  },
  computed: {
    wrapperItems (props) {
      return props.listItems;
    }
  },
  async mounted() {
    await this.getItems();
    this.showList = true;
    this.wrapperKey++;
  },
  beforeCreate() {
    this.items = [];
  },
  methods: {
    async getItems() {
      this.items = await this.getItemArray();
    },
    async getItemArray() {
      const res = [];

      for (let i = 0; i <= this.wrapperItems.length - 1; i++) {
        res.push(this.wrapperItems[i]);

        if (i === this.wrapperItems.length - 1) {
          return res;
        }
      }
    },
    getGridClass() {
      return 'col-start-3 md:col-start-0 col-span-8 md:col-span-6 lg:col-span-6 xl:col-span-4 2xl:col-span-3';
    },
    getLink(item) {
      if (this.detailPage.slice(-1) === '/') {
        return this.detailPage + item.slug ?? '';
      }

      return this.detailPage + '/' + item.slug ?? '';
    }
  }
}
</script>

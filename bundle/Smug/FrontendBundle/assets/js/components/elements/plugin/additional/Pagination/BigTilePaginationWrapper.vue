<template>
  <section
    v-if="showList === true && (items && items.length > 0)"
    :key="wrapperKey"
    class="container flex flex-wrap gap-y-3"
  >
    <div 
      v-for="(item, itemIndex) in items"
      :key="item.id"
      class="relative px-2 post-loop-wrapper h-72 h-extra-big-tile"
      :class="(itemIndex === 0 || itemIndex === 1 || itemIndex === 5 || itemIndex === 6 || itemIndex === 10 || itemIndex === 11) ? 'w-full md:w-1/2' : 'w-full md:w-1/2 lg:w-1/3'"
    >
      <pagination-card
        :key="itemIndex"
        :slug="getLink(item)"
        :item="item"
        :class="'h-100'"
      />
    </div>
  </section>
</template>

<script>
import { defineAsyncComponent, ref } from "vue";
const PaginationCard = defineAsyncComponent(() =>
  import("./PaginationCard" /* webpackChunkName: "pagination-card" */)
);

export default {
  name: "BigTilePaginationWrapper",
  components: {
    PaginationCard
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
    getLink(item) {
      if (this.detailPage.slice(-1) === '/') {
        return this.detailPage + item.slug ?? '';
      }

      return this.detailPage + '/' + item.slug ?? '';
    }
  }
}
</script>

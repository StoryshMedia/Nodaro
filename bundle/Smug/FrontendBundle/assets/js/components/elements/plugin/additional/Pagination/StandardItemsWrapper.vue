<template>
  <section
    v-if="showList === true && (items && items.length > 0)"
    :key="wrapperKey"
  >
    <div 
      v-for="(item, itemIndex) in items"
      :key="item.id"
    >
      <standard-list-item
        :key="itemIndex"
        :slug="getLink(item)"
        :item="item"
      />
    </div>
  </section>
</template>

<script>
import { defineAsyncComponent, ref } from "vue";
const StandardListItem = defineAsyncComponent(() =>
  import("./StandardListItem" /* webpackChunkName: "standard-list-item" */)
);

export default {
  name: "StandardItemsWrapper",
  components: {
    StandardListItem
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

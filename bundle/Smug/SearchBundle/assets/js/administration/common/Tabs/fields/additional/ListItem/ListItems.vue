<template>
  <section>
    <div
      v-for="(item, itemIndex) of listItems"
      :key="itemIndex"
      class="mt-2"
    >
      <list-item
        :item="item"
        :base-id="baseId"
        :edit-allowed="editAllowed"
        @deleted="deleteItem($event, itemIndex)"
      />
    </div>
    <div class="w-full mt-8 mb-4">
      <div
        class="text-center cursor-pointer md:col-span-1 text-white rounded-lg bg-dark bg-opacity-50 py-2"
        @click="addItem()"
      >
        {{ $t('ADD_ITEM') }}
      </div>
    </div>
  </section>
</template>
  
<script>
import { defineAsyncComponent } from "vue";
import ApiService from '../../../../../../../../../AdministrationBundle/assets/js/services/api/api.service';
const ListItem = defineAsyncComponent(() =>
  import("./ListItem.vue" /* webpackChunkName: "search-window-list-item" */)
);
  
export default {
  name: "ListItems",
  components: {
    ListItem
  },
  props: {
    editAllowed:{
      type: Boolean,
      required: true
    },
    baseId:{
      type: String,
      required: true
    },
    searchWindowId:{
      type: String,
      required: true
    },
    items:{
      type: Object,
      required: false,
      default: () => ({})
    }
  },
  data() {
    return {
      listItems: [],
      isLoading: false
    }
  },
  mounted() {
    this.listItems = this.items;
  },
  methods: {
    setContent(content, index, key) {
      this.listItems[index][key] = content;
    },
    addItem() {
      ApiService.post('/be/api/smug/search/listItem/add', {
        searchWindow: {id: this.searchWindowId},
        detailLink: '',
        hidden: '',
        itemData: {
          item: '',
          section: ''
        }
      }).then(result => {
        this.listItems.push(result);
      });
    },
    deleteItem(event, itemIndex) {
      if (event === true) {
        this.listItems.splice(itemIndex, 1);
      }
    },
    isDisabled() {
      if (this.isLoading === true) {
        return true;
      }
      if (this.editAllowed === false) {
        return true;
      }
      if (this.fieldConfig.disabled && this.fieldConfig.disabled === true) {
        return true;
      }
    }
  }
}
</script>
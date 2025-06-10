<template>
  <section>
    <div v-if="selections.length > 0">
      <select
        class="form-select text-dark"
        @change="selectItem($event)"
      >
        <option value="">
          {{ $t('PLEASE_CHOOSE') }}
        </option>
        <option
          v-for="(selection, selectionindex) in selections"
          :key="selectionindex"
          :value="getValue(selection)"
        >
          {{ selection.title }}
        </option>
      </select>
    </div>
    <div
      v-if="selectedItems.length > 0"
      class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5"
    >
      <div
        v-for="(item, selecteditemindex) in selectedItems"
        :key="selecteditemindex"
        class="flex justify-between p-3.5 rounded text-white bg-success"
      >
        <span class="px-2">{{ item.title }}</span>
        <button
          type="button"
          class="hover:opacity-80"
          @click="removeItem(item)"
        >
          <icon :icon-string="'IconX'" />
        </button>
      </div>
    </div>
  </section>
</template>

<script>
import ApiService from '@SmugAdministration/js/services/api/api.service';
import { defineAsyncComponent } from "vue";
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);

export default {
  name: "SelectList",
  components: {
    Icon
  },
  props: {
    editAllowed:{
      type: Boolean,
      required: true
    },
    fieldValue:{
      type: String,
      required: false,
      default: ''
    },
    baseId:{
      type: String,
      required: false,
      default: null
    },
    fieldConfig:{
      type: Object,
      required: false,
      default: () => ({})
    },
    fieldPlaceholder:{
      type: String,
      required: false,
      default: 'IMAGE_GALLERY_PLACEHOLDER'
    }
  },
  data() {
    return {
      selections: [],
      selectedItems: []
    };
  },
  mounted() {
    this.getData();
  },
  methods: {
    removeItem(item) {
      const index = this.selectedItems.findIndex(x => x.id === item.id);
      if (index > -1) {
        this.selectedItems.splice(index, 1);
        this.$emit('updateValue', this.selectedItems);
      }
    },
    selectItem(item) {
      if (item.target.value !== '') {
        const itemObject = JSON.parse(item.target.value);
        const index = this.selectedItems.findIndex(x => x.id === itemObject.id);
        if (index > -1) {
          this.removeItem(itemObject);
        } else {
          this.selectedItems.push(itemObject);
          this.$emit('updateValue', this.selectedItems);
        }
        item.target.value = '';
      }
    },
    getValue(value) {
      return JSON.stringify(value);
    },
    getData() {
      this.isLoading = true;
      const config = {
        headers: { Authorization: `Bearer ${window.localStorage.getItem('be-token', '')}` }
      };

      if (this.fieldConfig.getCall) {
        if (this.fieldConfig.id) {
          ApiService.get(this.fieldConfig.model.getCall, this.fieldConfig.id)
            .then(result =>  {
              this.selectedItems = result;
              this.isLoading = false;
            })
            .catch(error => {
              this.isLoading = false;
            })
            .then(function () {
            });
        }
      } else {
        this.selectedItems = this.fieldValue;
      }

      ApiService.get(this.fieldConfig.selections.getCall)
        .then(result =>  {
          this.selections = result;
        })
        .catch(error => {
          this.isLoading = false;
        })
        .then(function () {
        });
    }
  }
}
</script>
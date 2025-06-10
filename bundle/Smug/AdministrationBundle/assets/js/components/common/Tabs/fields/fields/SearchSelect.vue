<template>
  <section>
    <div class="w-full">
      <multiselect
        v-model="items"
        class="custom-multiselect w-full relative"
        :options="searchItems"
        :name="'title'"
        :label="'title'"
        :internal-search="false"
        :clear-on-select="true"
        :close-on-select="true"
        :options-limit="25"
        :max-height="600"
        :show-no-results="true"
        :hide-selected="true"
        :reset-after="true"
        @select="setItem($event)"
        @search-change="search($event)"
      >
        <template #tag="{}">
          <span />
        </template>
        <template #noResult>
          <span />
        </template>
        <template #placeholder>
          <span />
        </template>
        <template #singleLabel="props">
          <span class="option__desc">
            <span
              class="option__title"
            >{{ getLabel(props.option) }}</span>
          </span>
        </template>
        <template #option="props">
          <span class="option__desc">
            <span
              class="option__title"
            >{{ getLabel(props.option) }}</span>
          </span>
        </template>
        <template #noOptions>
          <div
            class="mx-2 p-1 rounded text-white"
            style="background: rgb(29, 59, 70); background: linear-gradient(135deg, rgba(29, 59, 70, 1) 0%, rgba(70, 148, 90, 1) 100%)"
          >
            <div 
              class="flex items-center p-2 rounded bg-white text-dark"
            >
              <span class="text-dark w-6 h-6">
                <icon :icon-string="'IconPencil'" />
              </span>
              <span class="px-2">{{ $t('SEARCH_SELECT_HINT') }}</span>
            </div>
          </div>
        </template>
      </multiselect>
    </div>
    <div v-if="fieldConfig.multiple === true">
      <alert
        v-for="(item, selecteditemindex) in selectedItems"
        :key="selecteditemindex"
        :edit-allowed="editAllowed"
        :label="getAlertLabel(item)"
        :header="fieldConfig.identifier"
        @remove="removeArrayItem(selecteditemindex)"
      />
    </div>
    <div v-if="(typeof selectedItem === 'object' && Object.keys(selectedItem).length > 0) || (typeof selectedItem === 'string' && selectedItem !== '')">
      <alert
        :edit-allowed="editAllowed"
        :label="getAlertLabel(selectedItem)"
        :header="fieldConfig.identifier"
        @remove="removeSingleItem()"
      />
    </div>
  </section>
</template>

<script>
import ApiService from 'SmugAdministration/js/services/api/api.service';
import { defineAsyncComponent } from "vue";
import { debounce } from "vue-debounce";
import Multiselect from 'vue-multiselect';
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);
const Alert = defineAsyncComponent(() =>
  import("../../../Elements/Alert.vue" /* webpackChunkName: "administration-alert" */)
);

export default {
  name: "SearchSelect",
  components: {
    Multiselect,
    Icon,
    Alert
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
    objectValues:{
      type: Object,
      required: false,
      default: () => ({})
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
      default: 'SEARCH_SELECT_PLACEHOLDER'
    }
  },
  data() {
    return {
      term: "",
      items: [],
      selectedItems: [],
      selectedItem: {},
      searchItems: []
    };
  },
  mounted() {
    if (this.fieldValue !== '') {
      if (typeof this.fieldValue === 'string' && this.fieldConfig.getCall) {
        if (JSON.parse(this.fieldValue)) {
          this.selectedItem = JSON.parse(this.fieldValue);
        } else {
          this.getData();
        }
      } else {
        if (this.fieldConfig.multiple === true) {
          this.selectedItems.push(this.fieldValue);
        } else {
          if (JSON.parse(this.fieldValue)) {
            this.selectedItem = JSON.parse(this.fieldValue);
          } else {
            this.getData();
          }
        }
      }
    }
  },
  methods: {
    removeSingleItem() {
      this.selectedItem = '';
      this.$emit('updateValue', this.selectedItem);
    },
    getData() {
      this.isLoading = true;
      const config = {
        headers: { Authorization: `Bearer ${window.localStorage.getItem('be-token', '')}` }
      };

      ApiService.get(this.fieldConfig.getCall, this.fieldValue)
        .then(result =>  {
          if (this.fieldConfig.multiple === true) {
            this.selectedItems.push(result);
          } else {
            this.selectedItem = result;
          }
        })
        .catch(error => {
          this.isLoading = false;
        })
        .then(function () {
        });
    },
    getAlertLabel(item) {
      if (this.fieldConfig.setModel && this.fieldConfig.setModel === true) {
        const label = this.getLabel(item);
        return label;
      } else {
        return item;
      }
    },
    getLabel(option) {
      const labelIdentifier = (this.fieldConfig.title) ? this.fieldConfig.title : 'title';

      if (labelIdentifier.includes(',')) {
        const fields = labelIdentifier.split(',');
        let label = '';

        for (let i = 0; i <= fields.length - 1; i++) {
          label += ' ' + option[fields[i]];

          if (i === fields.length - 1) {
            return label;
          }
        }
      } else {
        return option[labelIdentifier];
      }
    },
    removeArrayItem(index) {
      this.selectedItems.splice(index, 1);
      this.$emit('updateValue', this.selectedItems);
    },
    async setItem(item) {
      const value = await this.getItemValue(item);
      if (this.fieldConfig.multiple === true) {
        this.selectedItems.push(value);
        this.$emit('updateValue', this.selectedItems);
      } else {
        this.selectedItem = value;
        this.$emit('updateValue', this.selectedItem);
      }
    },
    async getItemValue(item) {
      if ((this.fieldConfig.setModel && this.fieldConfig.setModel === true)) {
        return item;
      }

      return item[this.fieldConfig['setIdentifier']];
    },
    search(queryString) {
      this.term = queryString;
      this.performSearch();
    },
    async getSearchCall() {
      if (this.fieldConfig.additionalParameter) {
        return this.fieldConfig.searchCall + this.fieldConfig.additionalParameter;
      }

      if (this.fieldConfig.additionalParameterIdentifier) {
        return this.fieldConfig.searchCall + this.objectValues[this.fieldConfig.additionalParameterIdentifier] ?? ''
      }

      return this.fieldConfig.searchCall;
    },
    performSearch: debounce(async function () {
      const searchCall = await this.getSearchCall();

      if (this.term === "") {
        this.searchItems = [];
        return;
      }
      if (this.term.length < 3) {
        return;
      }
      ApiService.post(searchCall, {'queryString': this.term})
        .then(result =>  {
          this.searchItems = result;
          console.log(this.searchItems);
        })
        .catch(error => {
        })
        .then(function () {
        });
    }, 600)
  }
}
</script>
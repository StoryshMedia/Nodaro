<template>
  <section>
    <div
      v-if="windowData.id"
      class="px-5"
    >
      <div class="grid grid-cols-1 md:grid-cols-2 md:gap-5 mt-5">
        <div>
          <p
            class="mb-3 pl-2 text-xs"
          >
            {{ $t('SEARCH_DETAIL_LINK') }}
          </p>
          <field
            :edit-allowed="!isDisabled()"
            :field-string="'LinkField'"
            :base-id="baseId"
            :item-value="windowData.searchDetailLink"
            :field-config="{fromDomain: true}"
            :field-placeholder="'SEARCH_DETAIL_LINK'"
            @updateValue="setContent($event, 'searchDetailLink')"
          />
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 md:gap-5 mt-5">
        <div
          v-for="(core, coreIndex) in cores"
          :key="coreIndex"
        >
          <div>
            <p
              class="mb-3 pl-2 text-xs"
            >
              {{ $t(core.title) }}
            </p>
            <field
              :edit-allowed="!isDisabled()"
              :field-string="'LinkField'"
              :base-id="baseId"
              :item-value="getDetailPageValue(core.value)"
              :field-config="{fromDomain: true}"
              :field-placeholder="'LINK_' + core.title"
              @updateValue="setDetailPageContent($event, core.value)"
            />
          </div>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 md:gap-5 mt-5">
        <div>
          <p
            class="mb-3 pl-2 text-xs"
          >
            {{ $t('MARKETING_ITEMS') }}
          </p>
          <marketing-items
            :edit-allowed="!isDisabled()"
            :items="windowData.marketingItems"
            :base-id="baseId"
            :search-window-id="windowData.id"
            @updateValue="setContent($event, 'marketingItems')"
          />
        </div>
        <div>
          <p
            class="mb-3 pl-2 text-xs"
          >
            {{ $t('LIST_ITEMS') }}
          </p>
          <list-items
            :edit-allowed="!isDisabled()"
            :items="windowData.listItems"
            :base-id="baseId"
            :search-window-id="windowData.id"
            @updateValue="setContent($event, 'listItems')"
          />
        </div>
      </div>
    </div>
    <div
      v-if="!isDisabled()"
      class="flex-none"
    >
      <div class="flex justify-end items-center py-2 pr-3">
        <button
          type="button"
          class="btn btn-primary ml-4"
          @click="save()"
        >
          {{ $t('SAVE') }}
        </button>
      </div>
    </div>
  </section>
</template>

<script>
import { defineAsyncComponent } from "vue";
import ApiService from '../../../../../../../AdministrationBundle/assets/js/services/api/api.service';
const ListItems = defineAsyncComponent(() =>
  import("./additional/ListItem/ListItems.vue" /* webpackChunkName: "search-window-list-items" */)
);
const MarketingItems = defineAsyncComponent(() =>
  import("./additional/MarketingItem/MarketingItems.vue" /* webpackChunkName: "search-window-marketing-items" */)
);
const Field = defineAsyncComponent(() =>
  import("../../../../../../../AdministrationBundle/assets/js/components/common/Tabs/fields/Field.vue" /* webpackChunkName: "field" */)
);

export default {
  name: "SearchWindowData",
  components: {
    Field,
    MarketingItems,
    ListItems
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
      default: 'TEXT_PLACEHOLDER'
    }
  },
  data() {
    return {
      windowData: {},
      cores: {},
      isLoading: false
    }
  },
  mounted() {
    this.getData();
    this.getCores();
  },
  methods: {
    getData() {
      ApiService.get(this.fieldConfig.getCall, this.baseId).then(result =>  {
        this.windowData = result;
        this.windowData.detailPages = JSON.parse(this.windowData.detailPages);
        this.isLoading = false;
      });
    },
    getCores() {
      ApiService.get('/be/api/custom/core/list').then(result =>  {
        this.cores = result[0].items;
      });
    },
    save() {
      this.isLoading = true;
      ApiService.put(this.fieldConfig.saveCall, this.windowData).then(result =>  {
        this.isLoading = false;
      });
    },
    getDetailPageValue(core) {
      if (typeof this.windowData.detailPages[core] === 'undefined') {
        this.windowData.detailPages[core] = '';

        return this.windowData.detailPages[core];
      } else {
        return this.windowData.detailPages[core];
      }
    },
    getValue(value) {
      return value;
    },
    setDetailPageContent(content, key) {
      this.windowData.detailPages[key] = content;
    },
    setContent(content, key) {
      this.windowData[key] = content;
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
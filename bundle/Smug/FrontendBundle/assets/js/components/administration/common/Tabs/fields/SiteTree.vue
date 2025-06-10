<template>
  <section>
    <div
      v-if="editAllowed === true"
      class="flex-none bg-dark rounded-md mb-3"
    >
      <div class="flex justify-end items-center py-2 pr-3">
        <button
          type="button"
          class="btn btn-primary ml-4"
          @click="setAddSite()"
        >
          {{ $t('ADD') }}
        </button>
      </div>
    </div>

    <NestedTree
      v-if="loaded === true"
      :field-config="fieldConfig"
      :base-id="baseId"
      :sites="sites"
      @orderChanged="saveOrder()"
    />

    <add-site
      v-if="showAddData === true"
      :field-config="{domain: {id: baseId}}"
      :headline="'ADD_SITE'"
      @editReaction="handleEditClick($event)"
    />

    <div 
      v-if="showSuccess === true"
      class="fixed top-2 right-2 z-50"
    >
      <alert
        v-if="showSuccess === true"
        :type="'green'"
        :header="'SUCCESS'"
        :label="'SUCCESS_DATA_SAVED'"
        :edit-allowed="true"
        @remove="removeSuccess()"
      />
    </div>
  </section>
</template>

<script>
import { defineAsyncComponent } from "vue";
import ApiService from '@SmugAdministration/js/services/api/api.service';
const NestedTree = defineAsyncComponent(() =>
  import("./additional/tree/NestedTree.vue" /* webpackChunkName: "administration-nested-tree" */)
);
const AddSite = defineAsyncComponent(() =>
  import("./additional/modal/AddSite.vue" /* webpackChunkName: "administration-content-add-site" */)
);
const Alert = defineAsyncComponent(() =>
  import("@SmugAdministration/js/components/common/Elements/Alert.vue" /* webpackChunkName: "administration-component-content" */)
);

export default {
  name: "SiteTree",
  components: {
    NestedTree,
    Alert,
    AddSite
  },
  props: {
    editAllowed:{
      type: Boolean,
      required: true
    },
    baseId:{
      type: String,
      required: false,
      default: null
    },
    fieldValue:{
      type: String,
      required: false,
      default: ''
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
      loaded: false,
      showAddData: false,
      showSuccess: false,
      sites: []
    };
  },
  mounted() {
    this.sites = this.fieldValue;
    this.loaded = true;
  },
  methods: {
    setAddSite() {
      this.showAddData = true
    },
    removeSuccess() {
      this.showSuccess = false;
    },
    handleEditClick(event) {
      if (event === false) {
        this.showAddData = false;
      } else {
        ApiService.post('/be/api/smug/frontend/site/add', event.item).then(result => {
          if (result.success === true) {
            this.showAddData = false;
            this.showSuccess = true;
            this.getData();
          }
        });
      }
    },
    saveOrder() {
      ApiService.put('/be/api/custom/site/domain/sites/tree', this.sites).then(result => {
        if (result.success === true) {
          this.showSuccess = true;
        }
      });
    },
    getData() {
      ApiService.get('/be/api/smug/frontend/domain/', this.baseId).then(result => {
        this.sites = result.sites;
      });
    }
  }
}
</script>
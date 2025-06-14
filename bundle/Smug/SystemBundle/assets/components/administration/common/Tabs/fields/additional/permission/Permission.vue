<template>
  <div class="border border-gray-400  rounded">
    <button
      type="button"
      class="p-4 w-full flex items-center"
      :class="{ 'text-primary': expanded === true, 'text-dark': expanded === false }"
      @click="handleAccordionClick()"
    >
      {{ bundle[0].type }}
      <div
        class="ml-auto"
      >
        <icon
          class="transform transition duration-300"
          :class="'IconHome'"
          :icon-string="'IconCaretDown'"
        /> 
      </div>
    </button>
    <vue-collapsible :is-open="expanded === true">
      <div class="p-4 border-t border-gray-400 ">
        <div class="grid grid-cols-4 mt-3 border-b-4 border-dark">
          <div />
          <div>
            <h5 class="pb-2">
              {{ $t('READ_ACCESS') }}
            </h5>
          </div>
          <div>
            <h5 class="pb-2">
              {{ $t('WRITE_ACCESS') }}
            </h5>
          </div>
          <div />

          <div>
            <h4 class="text-dark m-0">
              {{ $t('TOGGLE_ALL') }}
            </h4>
          </div>
          <div>
            <div class="flex items-start">
              <input
                :id="id"
                type="checkbox"
                :checked="toggleAll.canRead === true"
                :model="toggleAll.canRead"
              >
              <label
                class="checkbox-label"
                :class="{ active: toggleAll.canRead === true }"
                :for="id"
                @click="toggleAllClicked('canRead')"
              >
                <span
                  v-if="toggleAll.canRead === true"
                  class="ms-3 text-sm font-medium text-gray-900"
                >{{ $t('true') }}</span>
                <span
                  v-else
                  class="ms-3 text-sm font-medium text-gray-900"
                >{{ $t('false') }}</span>
              </label>
            </div>
          </div>
          <div>
            <div class="flex items-start">
              <input
                :id="id"
                type="checkbox"
                :checked="toggleAll.canWrite === true"
                :model="toggleAll.canWrite"
              >
              <label
                class="checkbox-label"
                :class="{ active: toggleAll.canWrite === true }"
                :for="id"
                @click="toggleAllClicked('canWrite')"
              >
                <span
                  v-if="toggleAll.canWrite === true"
                  class="ms-3 text-sm font-medium text-gray-900"
                >{{ $t('true') }}</span>
                <span
                  v-else
                  class="ms-3 text-sm font-medium text-gray-900"
                >{{ $t('false') }}</span>
              </label>
            </div>
          </div>
        </div>
        
        <div 
          v-for="(model, modelindex) in bundle"
          :key="modelindex"
          class="my-5"
        >
          <div class="grid grid-cols-4 mt-3">
            <div>
              <h4 class="text-dark m-0">
                {{ model.model }}
              </h4>
            </div>
            <div>
              <div class="flex items-start">
                <input
                  :id="id"
                  type="checkbox"
                  :checked="model.canRead === true"
                  :model="model.canRead"
                >
                <label
                  class="checkbox-label"
                  :class="{ active: model.canRead === true }"
                  :for="id"
                  @click="setRight('canRead', modelindex)"
                >
                  <span
                    v-if="model.canRead === true"
                    class="ms-3 text-sm font-medium text-gray-900"
                  >{{ $t('true') }}</span>
                  <span
                    v-else
                    class="ms-3 text-sm font-medium text-gray-900"
                  >{{ $t('false') }}</span>
                </label>
              </div>  
            </div>
            
            <div>
              <div class="flex items-start">
                <input
                  :id="id"
                  type="checkbox"
                  :checked="model.canWrite === true"
                  :model="model.canWrite"
                >
                <label
                  class="checkbox-label"
                  :class="{ active: model.canWrite === true }"
                  :for="id"
                  @click="setRight('canWrite', modelindex)"
                >
                  <span
                    v-if="model.canWrite === true"
                    class="ms-3 text-sm font-medium text-gray-900"
                  >{{ $t('true') }}</span>
                  <span
                    v-else
                    class="ms-3 text-sm font-medium text-gray-900"
                  >{{ $t('false') }}</span>
                </label>
              </div>
            </div>

            <div>
              <button
                type="button"
                class="btn w-full btn-outline-dark"
                @click="showDetailsClick(modelindex)"
              >
                {{ $t('RESTRICTIONS') }}
              </button>
            </div>
          </div>
          <div
            v-if="showHint(model)"
            class="flex items-center border p-3.5 mt-3 rounded text-dark border-dark"
          >
            <span> <strong class="mr-2">
              {{ $t('ATTENTION') }}!
            </strong>{{ $t('MODEL_PERMISSION_FIELD_RESTRICTIONS') }}
            </span>
          </div>
        </div>
      </div>
    </vue-collapsible>
    <permission-details
      v-if="showDetails === true"
      :item-data="getDetailData()"
      @edit-reaction="handleAction($event)"
    />
  </div>
</template>

<script>
import { defineAsyncComponent } from "vue";
import VueCollapsible from 'vue-height-collapsible/vue3';
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);
const PermissionDetails = defineAsyncComponent(() =>
  import("./PermissionDetails.vue" /* webpackChunkName: "permission-details" */)
);

export default {
  name: "Permission",
  components: {
    PermissionDetails,
    VueCollapsible,
    Icon 
  },
  props: {
    fieldConfig:{
      type: Object,
      required: false,
      default: () => ({})
    },
    bundle:{
      type: Array,
      required: true
    }
  },
  data() {
    return {
      expanded: false,
      showDetails: false,
      detailModel: {},
      toggleAll: {
        canWrite: false,
        canRead: false
      },
      detailmodelindex: 999999,
      reload: 0
    };
  },
  methods: {
    handleAccordionClick() {
      this.expanded = !this.expanded;
    },
    toggleAllClicked(right) {
      this.toggleAll[right] = !this.toggleAll[right];

      for (let count = 0; count <= this.bundle.length - 1; count++) {
        this.bundle[count][right] = this.toggleAll[right];

        if (this.bundle.length - 1 === count) {
          this.$emit('updateValue', this.bundle);
        }
      }
    },
    showDetailsClick(index) {
      this.detailmodelindex = index;
      this.detailModel = this.bundle[index];
      this.showDetails = !this.showDetails;
    },
    getDetailData() {
      return this.bundle[this.detailmodelindex];
    },
    showHint(model) {
      return (model.hiddenFields !== '' && model.hiddenFields.split(',').length !== model.fields.length) || (model.disallowedFields !== '' && model.disallowedFields.split(',').length !== model.fields.length);
    },
    setRight(right, index) {
      let fields = (right === 'canRead') ? 'hiddenFields' : 'disallowedFields';
      this.bundle[index][right] = !this.bundle[index][right];

      this.bundle[index][fields] = (this.bundle[index][right] === true) ? '' : this.bundle[index].fields.toString();
      this.$emit('updateValue', this.bundle);
    },
    handleAction(event) {
      this.bundle[this.detailmodelindex] = event.item;
      this.showDetails = false;
      this.$emit('updateValue', this.bundle);
    },
    getIconClass() {
      return (this.expanded === true) ? 'rotate-180' : '';
    }
  }
}
</script>
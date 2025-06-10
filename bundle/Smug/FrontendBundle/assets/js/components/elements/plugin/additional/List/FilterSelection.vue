<template>
  <TransitionRoot
    :show="true"
    :appear="true"
  >
    <Dialog
      as="div"
      class="relative z-50"
      @close="emitReaction(false)"
    >
      <TransitionChild
        as="template"
        enter="duration-300 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-200 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <DialogOverlay class="fixed inset-0 bg-black bg-opacity-60" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div class="flex min-h-full justify-end">
          <TransitionChild
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0"
            enter-to="opacity-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100"
            leave-to="opacity-0"
          >
            <DialogPanel
              class="panel border-0 p-0 rounded-l-lg overflow-hidden bg-white text-black animate__animated animate__fadeInRight w-full md:w-1/3"
            >
              <button
                type="button"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-800 outline-none"
                @click="emitReaction(false)"
              >
                <icon
                  :icon-string="'IconX'"
                /> 
              </button>
              <div class="text-lg font-bold bg-gray bg-opacity-40 pl-5 py-3 pr-24">
                {{ $t('FILTER_SELECTION') }}
              </div>
              <div class="p-5 flex flex-col">
                <perfect-scrollbar
                  :options="{
                    swipeEasing: true,
                    wheelPropagation: false,
                  }"
                  class="grow relative z-50 data-modal-content"
                >
                  <div
                    v-if="configuration.allFilters"
                  >
                    <div
                      v-for="(listFilter, index) in configuration.allFilters"
                      :key="index"
                      class="mx-1"
                    >
                      <button
                        type="button"
                        class="w-full border-b-2"
                        :class="{
                          'border-primary hover:border-dark': openFilters.includes(index),
                          'border-dark hover:border-primary': !openFilters.includes(index)
                        }"
                        @click="handleCollapsibleClick(index)"
                      >
                        <h3
                          class="text-base font-normal flex justify-between w-full leading-normal mt-2 mb-2 pl-2 text-gray-800"
                        >
                          <span>
                            {{ $t(listFilter.label) }}
                          </span>

                          <icon
                            class="transform transition duration-300 mr-3"
                            :class="'w-5 h-5 flex-none'"
                            :icon-string="'IconCaretDown'"
                          /> 
                        </h3>
                      </button>
                      <vue-collapsible
                        class="pt-3"
                        :is-open="openFilters.includes(index)"
                      >
                        <div class="relative w-full mb-3 py-3">
                          <type
                            :type="listFilter.outputType"
                            :options="listFilter.values"
                            :active-filters="getActiveFacetFilters(listFilter.mode)"
                            @setTypeFilter="setFilter($event, listFilter.mode)"
                          />
                        </div>
                      </vue-collapsible>
                    </div>
                  </div>

                  <div
                    v-else
                    class="border border-blue-300 shadow rounded-md p-4 max-w-sm w-full mx-auto"
                  >
                    <app-loading />
                  </div>
                  <div
                    class="mx-1"
                  >
                    <h3
                      class="text-base font-normal leading-normal mt-2 mb-2 pl-2 text-dark"
                    >
                      {{ $t('SORTING') }}
                    </h3>

                    <div class="relative w-full mb-3 py-3">
                      <select-box
                        :items="sortings"
                        :option-label-identifier="'label'"
                        :translate-label="true"
                        @option-selected="setSorting($event)"
                      />
                    </div>

                    <h3
                      v-if="selectedSorting !== ''"
                      class="text-base font-normal leading-normal mt-2 mb-2 pl-2 text-dark"
                    >
                      {{ $t('SORT_DIRECTION') }}
                    </h3>

                    <div
                      v-if="selectedSorting !== ''"
                      class="relative w-full mb-3 py-3"
                    >
                      <select-box
                        :items="sortDirections"
                        :option-label-identifier="'label'"
                        :translate-label="true"
                        @option-selected="setSortingDirection($event)"
                      />
                    </div>

                    <div class="text-right">
                      <button
                        class="bg-primary rounded-md py-2 px-3 mr-3 text-white hover:bg-dark"
                        @click="emitReaction(false)"
                      >
                        {{ $t("TO_LIST") }}
                      </button>
                    </div>
                  </div>
                </perfect-scrollbar>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script>
import { defineAsyncComponent } from "vue";

import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
import VueCollapsible from 'vue-height-collapsible/vue3';
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);
const SelectBox = defineAsyncComponent(() =>
  import("../../../../common/Input/SelectBox" /* webpackChunkName: "select-box" */)
);
const Type = defineAsyncComponent(() =>
  import("./Type/Type" /* webpackChunkName: "frontend-list-filter-type" */)
);

export default {
  name: "FilterSelection",
  components: {
    VueCollapsible,
    Icon,
    SelectBox,
    TransitionRoot,
    TransitionChild,
    Dialog,
    Type,
    DialogOverlay,
    DialogPanel
  },
  props: {
    config: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      loaded: false,
      sortDirections: [
        {
          label: 'ASC',
          value: 'ASC'
        },
        {
          label: 'DESC',
          value: 'DESC'
        }
      ],
      configuration: {},
      allSortings: [],
      filter: null,
      openFilters: [],
      filters: [],
      selected: []
    }
  },
  mounted() {
    this.configuration = this.config;

    if (typeof this.configuration.facets === 'undefined') {
      this.configuration.facets = [];
    }
  },
  methods: {
    emitReaction(value) {
      this.$emit('changeVisibility', value);
    },
    apply() {
      this.$emit('apply', true);
    },
    setSorting(sorting) {
      this.$emit('setSorting', sorting);
    },
    handleCollapsibleClick(index) {
      if (this.openFilters.indexOf(index) < 0) {
        this.openFilters.push(index);
      } else {
        this.openFilters.splice(this.openFilters.indexOf(index), 1);
      }
    },
    getActiveFacetFilters(mode) {
      if (this.configuration.facets.length === 0) {
        return [];
      }
      for (let i = 0; i <= this.configuration.facets.length - 1; i++) {
        if (this.configuration.facets[i].type === mode) {
          return this.configuration.facets[i].values;
        }
      }
    },
    setSortingDirection(direction) {
      this.$emit('setSortingDirection', direction);
    },
    setFilter(listFilter, mode) {
      this.$emit('setListfilter', {
        value: listFilter,
        mode: mode
      });
    }
  }
}
</script>
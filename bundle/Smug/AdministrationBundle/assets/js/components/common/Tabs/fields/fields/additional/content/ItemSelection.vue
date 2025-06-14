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
              class="bg-white border-0 p-0 flex flex-col rounded-l-lg overflow-hidden text-black animate__animated animate__fadeInRight w-full h-screen md:w-3/4"
            >
              <div class="grow-0">
                <button
                  type="button"
                  class="absolute top-4 right-4 text-gray-400 hover:text-gray-800 outline-none"
                  @click="emitReaction(false)"
                >
                  <icon :icon-string="'IconX'" />
                </button>
                <div class="text-lg font-bold bg-gray-400  bg-opacity-40 pl-5 py-3 pr-24">
                  {{ $t('ITEM_SELECTION') }}
                </div>
              </div>
              <div class="p-5 flex grow flex-1">
                <TabGroup as="div">
                  <div class="grid grid-cols-12 max-h-full">
                    <div class="col-span-2">
                      <TabList>
                        <div 
                          v-for="(elementCategory, elementCategoryindex) in elements"
                          :key="elementCategoryindex"
                        >
                          <h2 class="flex flex-row flex-nowrap items-center mb-5 mt-8">
                            <span class="flex-grow block border-t border-dark" />
                            <span class="flex-none block mx-2 px-4 py-2.5 rounded leading-none bg-dark text-white">
                              {{ $t(elementCategoryindex) }}
                            </span>
                            <span class="flex-grow block border-t border-dark" />
                          </h2>
                          <Tab
                            v-for="moduleCategory in elementCategory"
                            :key="moduleCategory[0].category"
                            as="template"
                          >
                            <a
                              href="javascript:;"
                              class="p-3.5 py-2 flex -mb-1px block border border-transparent hover:text-primary !outline-none"
                              :class="{ '!border-white-light !border-b-white text-primary': selectedTabIndex === moduleCategory[0].category }"
                              @click="setSelectedElementCategory(moduleCategory, moduleCategory[0].category)"
                            >
                              {{ $t(moduleCategory[0].category) }}
                            </a>
                          </Tab>
                        </div>
                      </TabList>
                    </div>
                    <div
                      class="col-span-10 px-5 overflow-hidden"
                      style="max-height: 80vh;"
                    >
                      <perfect-scrollbar
                        :options="{
                          swipeEasing: true,
                          wheelPropagation: false,
                        }"
                        :class="'h-full'"
                      >
                        <div
                          v-if="selectedElementCategory.length > 0"
                          class="grid grid-cols-3 gap-2"
                        >
                          <div
                            v-for="(module, moduleindex) in selectedElementCategory"
                            :key="moduleindex"
                            class="py-4 font-medium cursor-pointer border-2 border-transparent hover:border-primary rounded-md"
                            :class="{ 'border-primary': selectedItem.identifier === module.identifier }"
                            @click="setItem(module)"
                          >
                            <div
                              class="relative"
                            >
                              <img
                                :src="getPreviewImage(module)"
                                :alt="module.title"
                              >
                              <h4 class="text-left absolute bottom-0 bg-white right-0 uppercase text-xs py-2 px-2 text-primary">
                                {{ module.title }}
                              </h4>
                            </div>
                          </div>
                        </div>
                      </perfect-scrollbar>
                    </div>
                  </div>
                </TabGroup>
              </div>
              <div class="flex grow-0 h-16 justify-end items-center">
                <button
                  type="button"
                  class="btn btn-outline-danger"
                  @click="emitReaction(false)"
                >
                  {{ $t('ABORT') }}
                </button>
                <button
                  type="button"
                  class="btn btn-primary ml-4"
                  @click="emitReaction(true)"
                >
                  {{ $t('SAVE') }}
                </button>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
<script>
import ApiService from '@SmugAdministration/js/services/api/api.service';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay, TabGroup, TabList, Tab } from '@headlessui/vue';
import { defineComponent, defineAsyncComponent } from 'vue';
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);

export default defineComponent({
  name: 'ItemSelection',
  components: {
    Icon,
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogOverlay,
    DialogPanel,
    TabGroup,
    TabList,
    Tab
  },
  props: {
    position:{
      type: Number,
      required: false,
      default: 0
    },
    column:{
      type: Number,
      required: false,
      default: 0
    },
    isMultiItemSelection:{
      type: Boolean,
      required: false,
      default: false
    },
    fieldConfig:{
      type: Object,
      required: false,
      default: () => ({})
    }
  },
  data() {
    return {
      selectedItem: {},
      selectedElementCategory: [],
      selectedTabIndex: null,
      elements: {
        contentElements: [],
        plugins: [],
        structureItems: [],
      }
    }
  },
  mounted() {
    this.getData()
  },
  methods: {
    getData() {
      ApiService.get(
        this.fieldConfig.modules.getCall
      ).then(result =>  {
        let contentElements = [];
        let plugins = [];
        let structureItems = [];

        for (let count = 0; count <= result.length - 1; count++) {
          if (result[count].installed === true && result[count].active === true) {
            if (result[count].type === 'contentElement') {
              if (typeof contentElements[result[count].category] === 'undefined') {
                contentElements[result[count].category] = [];
              }

              contentElements[result[count].category].push(result[count]);
            }

            if (result[count].type === 'plugin') {
              if (typeof plugins[result[count].category] === 'undefined') {
                plugins[result[count].category] = [];
              }

              plugins[result[count].category].push(result[count]);
            }

            if (result[count].type === 'structure') {
              if (typeof structureItems[result[count].category] === 'undefined') {
                structureItems[result[count].category] = [];
              }

              structureItems[result[count].category].push(result[count]);
            }
          }

          if (count === result.length - 1) {
            for (let contentElementCount = 0; contentElementCount <= Object.keys(contentElements).length - 1; contentElementCount++) {
              this.elements.contentElements.push(contentElements[Object.keys(contentElements)[contentElementCount]]);
            }
            for (let pluginCount = 0; pluginCount <= Object.keys(plugins).length - 1; pluginCount++) {
              this.elements.plugins.push(plugins[Object.keys(plugins)[pluginCount]]);
            }
            for (let structureItemCount = 0; structureItemCount <= Object.keys(structureItems).length - 1; structureItemCount++) {
              this.elements.structureItems.push(structureItems[Object.keys(structureItems)[structureItemCount]]);
            }
          }
        }
      })
    },
    setSelectedElementCategory(elementCategory, modulecategoryindex) {
      this.selectedElementCategory = elementCategory;
      this.selectedTabIndex = modulecategoryindex;
    },
    getPreviewImage(module) {
      return process.env.frontendURL + module.assets.preview;
    },
    emitReaction(event) {
      this.$emit('selectItem', {
        isMultiItemSelection: this.isMultiItemSelection,
        position: this.position,
        column: this.column,
        item: (event === true) ? this.selectedItem : null
      });
    },
    setItem(event) {
      this.selectedItem = event;
    }
  },
})
</script>
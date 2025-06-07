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
              class="panel border-0 p-0 rounded-l-lg overflow-hidden text-black animate__animated animate__fadeInRight"
              :class="getModalWidth()"
            >
              <button
                type="button"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-800 outline-none"
                @click="emitReaction(false)"
              >
                <icon :icon-string="'IconX'" />
              </button>
              <div class="text-lg font-bold bg-gray bg-opacity-40 pl-5 py-3 pr-24">
                {{ $t(headline) }}
              </div>
              <div class="p-5 flex flex-col">
                <perfect-scrollbar
                  :options="{
                    swipeEasing: true,
                    wheelPropagation: false,
                  }"
                  class="grow relative z-50 data--modal-content"
                >
                  <TabGroup as="div">
                    <TabList class="flex flex-wrap mt-3 border-b border-white-light">
                      <Tab
                        v-for="(tab, tabindex) in template.tabs"
                        :key="tabindex"
                        v-slot="{ selected }"
                        as="template"
                      >
                        <a
                          href="javascript:;"
                          class="p-3.5 py-2 flex -mb-1px block border border-transparent hover:text-primary !outline-none"
                          :class="{ '!border-white-light !border-b-white text-primary': selected }"
                        >
                          <icon
                            :icon-string="(tab.icon) ? (tab.icon) : 'IconHome'"
                            :class="'w-5 h-5 flex-none mr-2'"
                          /> {{ $t(tab.headline) }}
                        </a>
                      </Tab>
                    </TabList>
                    <TabPanels class="pt-5 flex-1 text-sm">
                      <TabPanel
                        v-for="(tabConfig, tabindex) in template.tabs"
                        :key="tabindex"
                      >
                        <tab-content
                          v-if="loaded === true"
                          :tab-config="tabConfig"
                          :item="item"
                          :edit-allowed="true"
                          :disallowed-fields="disallowedFields"
                          :hidden-fields="hiddenFields"
                          @update-value="handleUpdate($event)"
                        />
                      </TabPanel>
                    </TabPanels>
                  </TabGroup>
                </perfect-scrollbar>
                <div class="flex h-16 justify-end items-center">
                  <button
                    type="button"
                    class="btn btn-outline-danger"
                    @click="emitReaction(false)"
                  >
                    {{ $t(abortButtonText) }}
                  </button>
                  <button
                    v-if="typeof config.disableSave === 'undefined'"
                    type="button"
                    class="btn btn-primary ml-4"
                    @click="emitReaction(true)"
                  >
                    {{ $t(saveButtonText) }}
                  </button>
                </div>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script>
import ApiService from '../../../services/api/api.service';
import { defineAsyncComponent } from "vue";

import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay, TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
const Icon = defineAsyncComponent(() =>
  import("../../../../../../FrontendBundle/assets/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);
const TabContent = defineAsyncComponent(() =>
  import("../Tabs/TabContent.vue" /* webpackChunkName: "administration-tab-content" */)
);

export default {
  name: "DataModal",
  components: {
    Icon,
    TabContent,
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogOverlay,
    DialogPanel,
    TabGroup,
    TabList,
    Tab,
    TabPanels,
    TabPanel
  },
  props: {
    itemData: {
      type: Object,
      required: true
    },
    template: {
      type: Object,
      required: true
    },
    config: {
      type: Object,
      required: false,
      default: () => ({})
    },
    headline: {
      type: String,
      required: false,
      default: 'MODAL_HEADLINE_BACKUP'
    },
    modalHeadline: {
      type: String,
      required: false,
      default: 'CONFIRM_HEADLINE'
    },
    saveButtonText: {
      type: String,
      required: false,
      default: 'SAVE'
    },
    abortButtonText: {
      type: String,
      required: false,
      default: 'ABORT'
    },
    modalWidth: {
      type: String,
      required: false,
      default: 'w-full md:w-1/3'
    }
  },
  data() {
    return {
      item: {},
      loaded: false,
      disallowedFields: [],
      hiddenFields: []
    }
  },
  mounted() {
    window.addEventListener('keydown', (e) => {
      if (e.key == 'Escape') {
        this.emitReaction(false);
      }
    });
    this.item = this.itemData;
    this.getPermissions();
  },
  methods: {
    getModalWidth() {
      return this.modalWidth;
    },
    handleUpdate(value) {
      this.item = value;
    },
    emitReaction(value) {
      if (value === false) {
        this.$emit('editReaction', false);
      } else {
        this.$emit('editReaction', {event: value, item: this.item});
      }
    },
    getPermissions() {
      ApiService.post(
        '/be/api/allowed',
        this.config
      )
        .then(result =>  {
          if (result.read === false) {
            this.loaded = true;
            this.notAllowed = true;
          } else {
            this.editAllowed = result.write;
            this.disallowedFields = result.disallowedFields;
            this.hiddenFields = result.hiddenFields;

            if (this.config.url.api.get) {
              let url = this.config.url.api.get;

              if (this.config.id) {
                url += this.config.id;
              }

              ApiService.get(url)
                .then(res =>  {
                  this.detailData = res;
                  this.loaded = true;
                })
                .catch(error => {
                  this.loaded = true;
                })
                .then(function () {
                });
            } else {
              this.loaded = true;
            }
          }
        })
        .catch(error => {
          this.loaded = true;
        })
        .then(function () {
        });
    }
  }
}
</script>
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
              class="bg-white border-0 p-0 flex flex-col rounded-l-lg overflow-hidden text-black animate__animated animate__fadeInRight w-full h-screen md:w-1/4"
            >
              <div class="grow-0">
                <button
                  type="button"
                  class="absolute top-4 right-4 text-gray-400 hover:text-gray-800 outline-none"
                  @click="emitReaction(false)"
                >
                  <icon :icon-string="'IconX'" />
                </button>
                <div class="text-lg font-bold bg-gray bg-opacity-40 pl-5 py-3 pr-24">
                  {{ $t('COPY_CONTENT_ELEMENT_HEADLINE') }} - {{ item.title }}
                </div>
              </div>
              <div class="p-5 flex grow flex-1 w-full item--configuration">
                <perfect-scrollbar
                  :options="{
                    swipeEasing: true,
                    wheelPropagation: false,
                  }"
                  class="w-full"
                >
                  <div
                    v-for="(site, siteIndex) in sites"
                    :key="siteIndex"
                  >
                    <div
                      :v-if="site.id !== parentId"
                      class="py-1"
                    >
                      <label class="inline-flex cursor-pointer items-center px-2 py-2 text-sm text-gray-700">
                        <input
                          :id="'site-select-' + site.id"
                          type="checkbox"
                          class="h-5 w-5 text-gray-600 basic--checkbox"
                          :checked="isSelected(site.id)"
                          @click="handleSiteSelect(site.id)"
                        >
                        <span class="ml-2">{{ site.title }}</span>
                        <span
                          v-if="hasSiteElement(site)"
                          class="ml-2 text-xs text-danger"
                        >{{ $t('CONTENT_ELEMENT_ALREADY_EXSIST') }}</span>
                      </label>
                    </div>
                    <div
                      v-if="isSelected(site.id)"
                      class="w-full mb-2 px-2"
                    >
                      <select
                        class="form-select text-dark"
                        @change="setPosition($event, site.id)"
                      >
                        <option :value="0">
                          {{ $t('CONTENT_ITEM_COPY_TOP') }}
                        </option>
                        <option :value="site.contentItems.length + 1">
                          {{ $t('CONTENT_ITEM_COPY_BOTTOM') }}
                        </option>
                        <option
                          v-for="(itemValue, itemIndex) in site.contentItems"
                          :key="itemIndex"
                          :value="itemValue.position"
                        >
                          <span>
                            {{ $t('CONTENT_ITEM_COPY_AFTER') }} {{ $t(itemValue.module.module.title) }}
                          </span>
                        </option>
                      </select>
                    </div>
                  </div>
                </perfect-scrollbar>
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
                  @click="save()"
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
import ApiService from '../../../../../../../services/api/api.service';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
import { defineComponent, defineAsyncComponent } from 'vue';
const Icon = defineAsyncComponent(() =>
  import("../../../../../../../../../../FrontendBundle/assets/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);

export default defineComponent({
  name: 'CopyContentElement',
  components: {
    Icon,
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogOverlay,
    DialogPanel
  },
  props: {
    item:{
      type: Object,
      required: false,
      default: () => ({})
    },
    parentId:{
      type: String,
      required: true
    }
  },
  data() {
    return {
      sites: [],
      selectedSites: []
    }
  },
  created() {
    this.getData()
  },
  methods: {
    hasSiteElement(site) {
      for (let i = 0; i <= site.contentItems.length - 1; i++) {
        if (site.contentItems[i].module.id === this.item.module.id) {
          return true;
        }

        if (i === site.contentItems.length - 1) {
          return false;
        }
      }
    },
    getSelectedPosition(id) {
      return this.selectedSites.findIndex(x => x.id === id);
    },
    isSelected(id) {
      const index = this.getSelectedPosition(id);
      return (index > - 1);
    },
    handleSiteSelect(id) {
      const index = this.getSelectedPosition(id);

      if (index > - 1) {
        this.selectedSites.splice(index, 1);
      } else {
        this.selectedSites.push({
          id: id,
          position: 0
        });
      }
    },
    setPosition(position, id) {
      const index = this.getSelectedPosition(id);

      if (index > - 1) {
        this.selectedSites[index].position = position.target.value;
      }
    },
    getData() {
      this.isLoading = true;
      ApiService.get('/be/api/custom/site/domain/sites/', this.parentId).then(result => {
        this.isLoading = false;
        this.sites = result;
      });
    },
    emitReaction(value) {
      this.$emit('copyItem', value);
    },
    save() {
      this.isLoading = true;
      ApiService.put('/be/api/custom/content/item/adopt', {sites: this.selectedSites, item: this.item}).then(result => {
        this.isLoading = false;
        this.emitReaction(true);
      });
    }
  },
})
</script>
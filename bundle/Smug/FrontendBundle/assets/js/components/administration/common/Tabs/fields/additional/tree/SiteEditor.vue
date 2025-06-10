<template>
  <TransitionRoot
    :show="true"
    :appear="true"
  >
    <Dialog
      as="div"
      class="relative z-50"
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
  
      <div 
        v-if="showSuccess === true"
        class="fixed top-2 right-2 z-50"
      >
        <alert
          :type="'green'"
          :header="'SUCCESS'"
          :label="'SUCCESS_DATA_SAVED'"
          :edit-allowed="true"
          @remove="removeSuccess()"
        />
      </div>
      <div
        v-if="loaded === true"
        class="fixed inset-0 overflow-y-auto"
      >
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
              class="panel border-0 p-0 max-h-screen overflow-hidden text-black animate__animated animate__fadeInRight"
              :class="getModalWidth()"
            >
              <div class="flex flex-col h-full">
                <div class="flex-none h-13">
                  <button
                    type="button"
                    class="absolute top-4 right-4 text-white outline-none"
                    @click="emitReaction(false)"
                  >
                    <icon :icon-string="'IconX'" />
                  </button>
                  <div class="bg-dark text-white pl-5 py-3 pr-24">
                    {{ $t(headline) }}
                  </div>
                </div>
                <div class="grow">
                  <content-field
                    :key="reload"
                    :edit-allowed="true"
                    :field-value="item.contentItems"
                    :parent-data="item"
                    :base-id="item.id"
                    :field-config="config"
                    @update-parent="handleUpdateParent($event)"
                    @update-value="handleUpdate($event, 'contentItems')"
                  />
                </div>
                <div class="flex-none h-16 bg-dark">
                  <div class="flex justify-end items-center pt-2 pr-3">
                    <button
                      type="button"
                      class="btn btn-danger"
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
import ApiService from '@SmugAdministration/js/services/api/api.service';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
const ContentField = defineAsyncComponent(() =>
  import("@SmugAdministration/js/components/common/Tabs/fields/fields/Content.vue" /* webpackChunkName: "administration-component-content" */)
);
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);
const Alert = defineAsyncComponent(() =>
  import("@SmugAdministration/js/components/common/Elements/Alert.vue" /* webpackChunkName: "administration-component-content" */)
);
  
export default {
  name: "SiteEditor",
  components: {
    Icon,
    TransitionRoot,
    TransitionChild,
    ContentField,
    Dialog,
    DialogOverlay,
    Alert,
    DialogPanel
  },
  props: {
    itemId: {
      type: String,
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
    baseId:{
      type: String,
      required: false,
      default: null
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
      default: 'w-full'
    }
  },
  data() {
    return {
      id: '',
      reload: 0,
      item: {},
      loaded: false,
      showSuccess: false,
      disallowedFields: [],
      hiddenFields: []
    }
  },
  created() {
    this.id = this.itemId;
    this.getData();
  },
  methods: {
    getModalWidth() {
      return this.modalWidth;
    },
    handleUpdateParent(event) {
      this.item[event.property] = event.value;
    },
    handleUpdate(value, identifier) {
      this.item[identifier] = value;
      this.reload++;
    },
    emitReaction(value) {
      if (value === false) {
        this.$emit('editReaction', false);
      } else {
        ApiService.put(this.config.sites.save, this.item).then(result => {
          if (result.success === true) {
            this.showSuccess = true;
            this.$emit('editReaction', {event: value, item: this.item});
            setTimeout(() => {
              this.showSuccess = false;
            }, 5000);
          }
        });
      }
    },
    removeSuccess() {
      this.showSuccess = false;
    },
    getData() {
      ApiService.get(this.config.siteGetCall, this.id).then(result => {
        this.item = result;
        this.getPermissions();
      });
    },
    getPermissions() {
      ApiService.post('/be/api/allowed', this.config).then(result => {
        if (result.read === false) {
          this.loaded = true;
          this.notAllowed = true;
        } else {
          this.editAllowed = result.write;
          this.disallowedFields = result.disallowedFields;
          this.hiddenFields = result.hiddenFields;
          this.loaded = true;
        }
      });
    }
  }
}
</script>
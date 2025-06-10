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
                >
                  <div class="h-full w-full p-5">
                    <h2 class="flex flex-row flex-nowrap items-center mb-5 mt-8">
                      <span class="flex-grow block border-t border-dark" />
                      <span class="flex-none block mx-2 px-4 py-2.5 rounded leading-none bg-dark text-white">
                        {{ $t('BASE_SETTINGS') }}
                      </span>
                      <span class="flex-grow block border-t border-dark" />
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 my-5">
                      <div>
                        <h5
                          class="font-semibold text-lg pb-2"
                        >
                          {{ $t('TITLE') }}
                        </h5>
                        <field
                          :field-string="'Text'"
                          :item-value="site.title"
                          :field-placeholder="'TITLE'"
                          :edit-allowed="editAllowed"
                          @update-value="setTitle($event)"
                        />
                      </div>
                      <div>
                        <h5
                          class="font-semibold text-lg pb-2"
                        >
                          {{ $t('PARENT_SITE') }}
                        </h5>
                        <field
                          :field-string="'Selectbox'"
                          :item-value="site.parentId"
                          :field-placeholder="'PARENT_SITE'"
                          :edit-allowed="editAllowed"
                          :field-config="{getCall: '/be/api/smug/frontend/domain/sites/' + fieldConfig.domain.id}"
                          @update-value="setParent($event)"
                        />
                      </div>
                      <div v-if="site.slug !== ''">
                        <h5
                          class="font-semibold text-lg pb-2"
                        >
                          {{ $t('SLUG') }}
                        </h5>
                        <field
                          :field-string="'Text'"
                          :item-value="site.slug"
                          :field-placeholder="'SLUG'"
                          :edit-allowed="editAllowed"
                          @update-value="setValue($event, 'slug')"
                        />
                      </div>
                    </div>
                  </div>
                  <div class="h-full w-full p-5">
                    <h2 class="flex flex-row flex-nowrap items-center mb-5 mt-8">
                      <span class="flex-grow block border-t border-dark" />
                      <span class="flex-none block mx-2 px-4 py-2.5 rounded leading-none bg-dark text-white">
                        {{ $t('VISIBILITY') }}
                      </span>
                      <span class="flex-grow block border-t border-dark" />
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 my-5">
                      <div>
                        <h5
                          class="font-semibold text-lg pb-2"
                        >
                          {{ $t('ROOT_PAGE') }}
                        </h5>
                        <field
                          :field-string="'Checkbox'"
                          :item-value="site.rootPage"
                          :field-placeholder="'ROOT_PAGE'"
                          :edit-allowed="editAllowed"
                          @update-value="setValue($event, 'rootPage')"
                        />
                      </div>
                      <div>
                        <h5
                          class="font-semibold text-lg pb-2"
                        >
                          {{ $t('HIDDEN') }}
                        </h5>
                        <field
                          :field-string="'Checkbox'"
                          :item-value="site.hidden"
                          :field-placeholder="'HIDDEN'"
                          :edit-allowed="editAllowed"
                          @update-value="setValue($event, 'hidden')"
                        />
                      </div>
                      <div>
                        <h5
                          class="font-semibold text-lg pb-2"
                        >
                          {{ $t('HIDDEN_IN_MENU') }}
                        </h5>
                        <field
                          :field-string="'Checkbox'"
                          :item-value="site.hiddenInMenu"
                          :field-placeholder="'HIDDEN_IN_MENU'"
                          :edit-allowed="editAllowed"
                          @update-value="setValue($event, 'hiddenInMenu')"
                        />
                      </div>
                    </div>
                  </div>
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
import { defineAsyncComponent } from "vue";
import slugify from '@sindresorhus/slugify';

import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);
const Field = defineAsyncComponent(() =>
  import("@SmugAdministration/js/components/common/Tabs/fields/Field.vue" /* webpackChunkName: "field" */)
);

export default {
  name: "AddSite",
  components: {
    Icon,
    Field,
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogOverlay,
    DialogPanel
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
    fieldConfig: {
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
      site: {
        title: '',
        rootPage: false,
        hidden: true,
        hiddenInMenu: false,
        parentId: '',
        domain: {
          id: this.fieldConfig.domain.id
        },
        slug: ''
      },
      selectedParentSlug: '',
      loaded: false
    }
  },
  methods: {
    getModalWidth() {
      return this.modalWidth;
    },
    setValue(value, property) {
      this.site[property] = value;
    },
    setParent(value) {
      this.site['parentId'] = value.id;
      if (value.slug === '/') {
        this.selectedParentSlug = value.slug;
      } else {
        this.selectedParentSlug = value.slug + '/';
      }

      if (this.site.slug === '') {
        this.site.slug = this.selectedParentSlug;
      } else {
        this.site.slug = this.selectedParentSlug + slugify(this.site.title);
      }
    },
    setTitle(value) {
      this.site.title = value;
      
      if (this.selectedParentSlug === '') {
        this.selectedParentSlug = '/';
      }

      this.site.slug = this.selectedParentSlug + slugify(this.site.title);
    },
    setSlug(value) {
      this.site.title = value;
      this.site.slug = this.selectedParentSlug + slugify(this.site.title);
    },
    emitReaction(value) {
      if (value === false) {
        this.$emit('editReaction', false);
      } else {
        this.$emit('editReaction', {event: value, item: this.site});
      }
    }
  }
}
</script>
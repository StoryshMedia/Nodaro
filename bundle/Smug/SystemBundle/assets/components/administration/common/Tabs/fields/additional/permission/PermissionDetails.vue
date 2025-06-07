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
            class="bg-white md:w-1/3"
            enter="duration-300 ease-out"
            enter-from="opacity-0"
            enter-to="opacity-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100"
            leave-to="opacity-0"
          >
            <DialogPanel class="panel border-0 p-0 rounded-l-lg overflow-hidden w-full text-black animate__animated animate__fadeInRight">
              <button
                type="button"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-800 outline-none"
                @click="emitReaction(false)"
              >
                <icon :icon-string="'IconX'" />
              </button>
              <div class="text-lg font-bold bg-gray bg-opacity-40 pl-5 py-3 pr-24">
                {{ $t('FIELDS') }}
              </div>
              <div class="py-5 h-90vh">
                <perfect-scrollbar
                  :options="{
                    swipeEasing: true,
                    wheelPropagation: false,
                  }"
                  class="h-full relative z-50 data-modal-content"
                >
                  <div class="grid grid-cols-2 mt-3 px-3">
                    <div>
                      <h5 class="pb-2">
                        {{ $t('DISALLOWED_FIELDS') }}
                      </h5>
                    </div>
                    <div>
                      <h5 class="pb-2">
                        {{ $t('HIDDEN_FIELDS') }}
                      </h5>
                    </div>

                    <div>
                      <div class="flex items-start">
                        <input
                          type="checkbox"
                          :checked="toggleAll.disallowedFields === true"
                          :model="toggleAll.disallowedFields"
                        >
                        <label
                          class="checkbox-label"
                          :class="{ active: toggleAll.disallowedFields === true }"
                          :for="id"
                          @click="toggleAllClicked('disallowedFields')"
                        >
                          <span
                            v-if="toggleAll.disallowedFields === true"
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
                          type="checkbox"
                          :checked="toggleAll.hiddenFields === true"
                          :model="toggleAll.hiddenFields"
                        >
                        <label
                          class="checkbox-label"
                          :class="{ active: toggleAll.hiddenFields === true }"
                          :for="id"
                          @click="toggleAllClicked('hiddenFields')"
                        >
                          <span
                            v-if="toggleAll.hiddenFields === true"
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
                    v-for="(field, fieldindex) in item.fields"
                    :key="fieldindex"
                    class="my-3"
                  >
                    <div class="text-lg font-bold bg-gray bg-opacity-40 pl-5 py-3 pr-24">
                      {{ $t(field.toUpperCase()) }}
                    </div>
                    
                    <div class="grid grid-cols-2 mt-3 px-3">
                      <div>
                        <div class="flex items-start">
                          <input
                            type="checkbox"
                            :checked="item.disallowedFields.includes(field)"
                          >
                          <label
                            class="checkbox-label"
                            :class="{ active: item.disallowedFields.includes(field) }"
                            :for="id"
                            @click="setDisallowed(field)"
                          >
                            <span
                              v-if="item.disallowedFields.includes(field)"
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
                            type="checkbox"
                            :checked="item.hiddenFields.includes(field)"
                          >
                          <label
                            class="checkbox-label"
                            :class="{ active: item.hiddenFields.includes(field) }"
                            :for="id"
                            @click="setHidden(field)"
                          >
                            <span
                              v-if="item.hiddenFields.includes(field)"
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
                  </div>
                </perfect-scrollbar>
                <div class="flex justify-end items-center mt-8 pr-3">
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
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script>
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';

export default {
  name: "PermissionDetails",
  components: {
    TransitionRoot,
    Dialog
  },
  props: {
    itemData: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      item: {},
      toggleAll: {
        hiddenFields: false,
        disallowedFields: false
      }
    }
  },
  mounted() {
    window.addEventListener('keydown', (e) => {
      if (e.key == 'Escape') {
        this.emitReaction(false);
      }
    });
    this.itemData.hiddenFields = this.itemData.hiddenFields.split(',');
    this.itemData.disallowedFields = this.itemData.disallowedFields.split(',');
    this.item = this.itemData;
  },
  methods: {
    setDisallowed(field) {
      if (this.item.disallowedFields.includes(field)) {
        this.item.disallowedFields.splice(this.item.disallowedFields.indexOf(field), 1)
        return;
      }
      this.item.disallowedFields.push(field);
    },
    setHidden(field) {
      if (this.item.hiddenFields.includes(field)) {
        this.item.hiddenFields.splice(this.item.hiddenFields.indexOf(field), 1)
        return;
      }
      this.item.hiddenFields.push(field);
    },
    toggleAllClicked(fields) {
      this.toggleAll[fields] = !this.toggleAll[fields];

      for (let count = 0; count <= this.itemData.fields.length - 1; count++) {
        if (fields === 'hiddenFields') {
          this.setHidden(this.itemData.fields[count]);
          continue;
        } else {
          this.setDisallowed(this.itemData.fields[count]);
        }
      }
    },
    emitReaction(value) {
      const result = { ...this.item };
      result.canRead = (result.hiddenFields.filter(n => n).length < result.fields.length && result.hiddenFields.filter(n => n).length > 0);
      result.canWrite = (result.disallowedFields.filter(n => n).length < result.fields.length && result.disallowedFields.filter(n => n).length > 0);
      result.hiddenFields = result.hiddenFields.filter(n => n).toString();
      result.disallowedFields = result.disallowedFields.filter(n => n).toString();
      this.$emit('editReaction', {event: value, item: result});
    }
  }
}
</script>
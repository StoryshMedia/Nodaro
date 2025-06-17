<template>
  <TransitionRoot
    :show="true"
    :appear="true"
  >
    <Dialog
      as="div"
      class="relative z-50"
      @close="emitReaction()"
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
        <div class="flex min-h-full items-center justify-center px-4 py-8">
          <TransitionChild
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
          >
            <DialogPanel class="panel border-0 p-0 rounded-lg overflow-hidden w-full max-w-sm text-black">
              <button
                type="button"
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-800 outline-none"
                @click="emitReaction()"
              >
                <icon :icon-string="'IconX'" />
              </button>
              <div class="text-lg font-bold bg-gray-400  bg-opacity-40 pl-5 py-3 pr-24">
                {{ $t('FILE_SELECTION') }}
              </div>
              <div class="p-5">
                <media-center
                  :is-selection="true"
                  @selectFile="selectFile($event)"
                />
                
                <div class="flex justify-end items-center mt-8">
                  <button
                    type="button"
                    class="btn btn-success ml-4"
                    @click="emitReaction()"
                  >
                    {{ $t('CLOSE') }}
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
import { defineComponent, defineAsyncComponent } from 'vue';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
const MediaCenter = defineAsyncComponent(() =>
  import("../../MediaCenter.vue" /* webpackChunkName: "administration-field-media-center" */)
);
  
export default defineComponent({
  name: 'FileSelection',
  components: {
    MediaCenter,
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogOverlay,
    DialogPanel
  },
  methods: {
    emitReaction(file) {
      this.$emit('cancel', true);
    },
    selectFile(file) {
      this.$emit('selectFile', file);
    }
  },
})
</script>
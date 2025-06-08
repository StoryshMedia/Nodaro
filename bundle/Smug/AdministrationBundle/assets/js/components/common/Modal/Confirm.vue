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
                @click="emitReaction(false)"
              >
                <icon :icon-string="'IconX'" />
              </button>
              <div class="text-lg font-bold bg-gray bg-opacity-40 pl-5 py-3 pr-24">
                {{ $t(modalHeadline) }}
              </div>
              <div class="p-5">
                <div v-html="$t(text)" />

                <div class="flex justify-end items-center mt-8">
                  <button
                    type="button"
                    class="btn btn-outline-danger"
                    @click="emitReaction(false)"
                  >
                    {{ $t(discardButtonText) }}
                  </button>
                  <button
                    type="button"
                    class="btn btn-success ml-4"
                    @click="emitReaction(true)"
                  >
                    {{ $t(confirmButtonText) }}
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

import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);

export default {
  name: "Confirm",
  components: {
    Icon,
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogOverlay,
    DialogPanel
  },
  props: {
    text: {
      type: String,
      required: false,
      default: 'CONFIRM_DEFAULT_TEXT'
    },
    modalHeadline: {
      type: String,
      required: false,
      default: 'CONFIRM_HEADLINE'
    },
    confirmButtonText: {
      type: String,
      required: false,
      default: 'CONFIRM_BUTTON_TEXT'
    },
    discardButtonText: {
      type: String,
      required: false,
      default: 'DISCARD'
    }
  },
  mounted() {
    window.addEventListener('keydown', (e) => {
      if (e.key == 'Escape') {
        this.emitReaction(false);
      }
    });
  },
  methods: {
    emitReaction(value) {
      this.$emit('confirmReaction', value);
    }
  }
}
</script>
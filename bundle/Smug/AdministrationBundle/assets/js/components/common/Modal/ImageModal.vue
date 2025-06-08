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
              <div class="text-lg font-bold bg-gray bg-opacity-40 pl-5 py-3 pr-24">
                {{ getImageName() }}
              </div>
              <div class="p-5">
                <img :src="getImageSource()">

                <div class="flex justify-end items-center mt-8">
                  <button
                    v-if="showDelete === true"
                    type="button"
                    class="btn btn-danger ml-4"
                    @click="showConfirmation = true"
                  >
                    {{ $t('DELETE') }}
                  </button>
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
      <confirm
        v-if="showConfirmation === true"
        :text="'DELETE_CONFIRMATION_TEXT'"
        :confirm-button-text="'YES'"
        :discard-button-text="'NO'"
        :modal-headline="'DELETE_CONFIRMATION_HEADLINE'"
        @confirm-reaction="handleConfirmValue($event)"
      />
    </Dialog>
  </TransitionRoot>
</template>

<script>
import { defineAsyncComponent } from "vue";
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);
const Confirm = defineAsyncComponent(() =>
  import("./Confirm.vue" /* webpackChunkName: "administration-modal-confirm" */)
);
import ApiService from '@SmugAdministrationServices/api/api.service';
import ImageService from '@SmugAdministrationServices/image/image.service';

export default {
  name: "ImageModal",
  components: {
    Icon,
    TransitionRoot,
    TransitionChild,
    Dialog,
    Confirm,
    DialogOverlay,
    DialogPanel
  },
  props: {
    showDelete: {
      type: Boolean,
      required: false,
      default: false
    },
    imageData:{
      type: Object,
      required: false,
      default: () => ({})
    }
  },
  data() {
    return{
      showConfirmation: false
    }
  },
  mounted() {
    window.addEventListener('keydown', (e) => {
      if (e.key == 'Escape') {
        this.emitReaction();
      }
    });
  },
  methods: {
    emitReaction() {
      this.$emit('reaction', false);
    },
    getImageName() {
      if (!this.imageData.media) {
        return ImageService.getFileName(this.imageData);
      }
      return ImageService.getFileName(this.imageData.media);
    },
    deleteImage() {
      const image = (typeof this.imageData.media !== 'undefined') ? this.imageData.media : this.imageData;
      ApiService.put('/be/api/custom/media/delete', image)
        .then(result =>  {
          this.showConfirmation = false;
          this.$emit('reaction', false);
        })
        .catch(error => {
          this.isLoading = false;
        })
        .then(function () {
        });
    },
    handleConfirmValue(value) {
      if (value === true) {
        this.deleteImage();
      } else {
        this.showConfirmation = false;
      }
    },
    getImageSource() {
      if (!this.imageData.media) {
        return ImageService.getImagePathFromMedia(this.imageData);
      }
      return ImageService.getImagePathFromMedia(this.imageData.media);
    }
  }
}
</script>
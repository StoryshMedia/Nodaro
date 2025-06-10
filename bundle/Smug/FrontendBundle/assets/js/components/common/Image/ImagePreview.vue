<template>
  <div>
    <button
      ref="showPreviewImageModal"
      class="hidden"
      type="button"
      data-modal-toggle="image-preview-modal"
    />
    <div
      id="image-preview-modal"
      ref="imagePreviewModal"
      tabindex="-1"
      aria-hidden="true"
      class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full"
    >
      <div
        class="fixed z-10 inset-0 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
      >
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div
            class="fixed inset-0 bg-overlay transition-opacity"
            aria-hidden="true"
          />

          <!-- This element is to trick the browser into centering the modal contents. -->
          <span
            class="hidden sm:inline-block sm:align-middle sm:h-screen"
            aria-hidden="true"
          >&#8203;</span>

          <div
            class="inline-block align-bottom rounded-lg max-w-full md:max-w-3/4 mx-auto text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle"
          >
            <div
              class="absolute flex justify-end top-0 w-full opacity-75 text-xl text-kelp-700 text-center bg-kelp-50"
            >
              <div
                class="cursor-pointer h-8 px-2 pt-2 pb-8"
                @click="toggleButton()"
              >
                <icon-close />
              </div>
            </div>
            <img
              :src="url"
              alt="Previewbild"
            >
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineAsyncComponent } from "vue";
const IconClose = defineAsyncComponent(() =>
  import("@core/js/icons/icons/IconClose" /* webpackChunkName: "icon-close" */)
);

export default {
  name: "ImagePreview",
  components: {
    IconClose
  },
  props: {
    url: {
      type: String,
      required: true
    },
    showPreviewImage: {
      type: Boolean,
      required: true
    }
  },
  mounted() {
    setTimeout(() => this.toggleButton(), 100);
    window.addEventListener('keydown', (e) => {
      if (e.key == 'Escape') {
        this.toggleButton();
      }
    });
  },
  methods: {
    toggleButton() {
      if (this.$refs.imagePreviewModal !== null) {
        if (this.$refs.imagePreviewModal.classList.contains('hidden')) {
          this.$refs.imagePreviewModal.classList.remove('hidden');
        } else {
          this.$emit('showPreviewImage', false);
          this.$refs.imagePreviewModal.classList.add('hidden');
        }
      } else {
        this.$refs.imagePreviewModal.classList.add('hidden');
      }
    }
  }
}
</script>
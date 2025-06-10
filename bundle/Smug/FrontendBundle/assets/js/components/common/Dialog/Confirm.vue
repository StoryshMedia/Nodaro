<template>
  <div>
    <button
      ref="showConfirmModal"
      class="hidden"
      type="button"
      data-modal-toggle="confirm-modal"
    />
    <div
      id="confirm-modal"
      tabindex="-1"
      class="overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-1/2 mx-auto md:inset-0 h-modal md:h-full"
    >
      <div
        class="fixed z-10 inset-0 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
      >
        <div class="flex items-center md:items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            aria-hidden="true"
          />

          <!-- This element is to trick the browser into centering the modal contents. -->
          <span
            class="hidden sm:inline-block sm:align-middle sm:h-screen"
            aria-hidden="true"
          >&#8203;</span>

          <div
            class="inline-block align-bottom rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle md:w-8/12 sm:w-full"
          >
            <div class="bg-white  ">
              <div
                class="flex-auto p-5 lg:p-10"
              >
                <h3
                  class="text-xl text-center pb-5 font-semibold text-dark"
                  v-html="$t(question)"
                />
                <div class="flex flex-wrap justify-center text-center mt-6">
                  <button
                    type="button"
                    class="rounded-full p-2.5 h-12 w-12 block border-2 border-dark text-dark text-sm font-bold hover:bg-dark hover:text-white outline-none focus:outline-none mr-1 mb-1"
                    data-modal-toggle="detail-review-modal"
                    @click="emit('cancel')"
                  >
                    <icon-close />
                  </button>
                  <button
                    class="rounded-full p-2.5 h-12 w-12 block border-2 border-primary bg-primary text-white text-sm font-bold hover:bg-white hover:text-primary outline-none focus:outline-none mr-1 mb-1"
                    type="submit"
                    style="transition: all 0.15s ease 0s;"
                    @click="emit('confirm')"
                  >
                    <span><icon-check /></span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineAsyncComponent } from "vue";
const IconClose = defineAsyncComponent(() =>
  import("@core/js/icons/icons/IconClose" /* webpackChunkName: "image-close" */)
);
const IconCheck = defineAsyncComponent(() =>
  import("@core/js/icons/icons/IconCheck" /* webpackChunkName: "image-check" */)
);

export default {
  name: "Confirm",
  components: {
    IconClose,
    IconCheck
  },
  props: {
    question: {
      type: String,
      required: true
    },
    mode: {
      type: String,
      required: true
    }
  },
  mounted() {
    setTimeout(() => this.toggleButton(), 100);
    window.addEventListener('keydown', (e) => {
      if (e.key == 'Escape') {
        this.emit('cancel');
      }
    });
  },
  methods: {
    toggleButton() {
      this.$refs.showConfirmModal.click();
    },
    emit(mode) {
      this.$emit(mode, this.mode);
    }
  }
}
</script>
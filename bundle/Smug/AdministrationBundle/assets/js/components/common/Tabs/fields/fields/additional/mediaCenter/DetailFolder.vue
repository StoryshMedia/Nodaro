<template>
  <TransitionRoot
    :show="true"
    :appear="true"
  >
    <Dialog
      as="div"
      class="relative z-50"
      style="max-height: 90hv;"
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
            <DialogPanel class="panel border-0 p-0 rounded-lg overflow-hidden w-full text-black">
              <section class="p-3 item--configuration">
                <perfect-scrollbar
                  :options="{
                    swipeEasing: true,
                    wheelPropagation: false,
                  }"
                  class="h-full mb-16 py-3 mx-5"
                >
                  <div
                    class="text-right pr-2 pt-2"
                  >
                    <button
                      class="text-dark hover:text-primary hover:border-b-2 hover:border-primary mx-auto"
                      style="transition: all 0.15s ease 0s;"
                      @click="emitReaction()"
                    >
                      {{ $t('CLOSE') }}
                    </button>
                  </div>

                  <h2 class="flex flex-row flex-nowrap items-center mb-5">
                    <span class="flex-grow block border-t border-dark" />
                    <span class="flex-none block mx-2 px-4 py-2.5 rounded leading-none bg-dark text-white">
                      {{ $t('FILES_IN_FOLDER') }}: {{ detailFolderTitle }}
                    </span>
                    <span class="flex-grow block border-t border-dark" />
                  </h2>

                  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-4 mx-4">
                    <div
                      v-for="(file, fileIndex) of files"
                      :key="fileIndex"
                    >
                      <img
                        class="object-cover object-center w-full h-40 max-w-full rounded-lg cursor-pointer"
                        :src="getImageSrc(file)"
                        :alt="file.file + '.' + file.extension"
                        @click="showDetailImage(file)"
                      >

                      <div
                        v-if="getIsSelection()"
                        :title="$t('SELECT')"
                        class="text-center cursor-pointer text-white bg-dark rounded-lg py-1 my-1"
                        @click="selectFile(file)"
                      >
                        {{ $t('SELECT') }}
                      </div>
                    </div>
                  </div>
                </perfect-scrollbar>
              </section>
              <div class="bh-pagination bh-py-5 flex items-end pr-5">
                <nav
                  class="bh-pagination-number sm:bh-ml-auto bh-inline-flex bh-items-center bh-space-x-1"
                  aria-label="Pagination"
                >
                  <button
                    type="button"
                    class="bh-page-item w-auto first-page"
                    :class="{ disabled: page === pages.start }"
                    @click="setPage(1)"
                  >
                    <span>
                      <svg
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4.5"
                      >
                        <path
                          d="M13 19L7 12L13 5"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                        <path
                          opacity="0.5"
                          d="M16.9998 19L10.9998 12L16.9998 5"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                    </span>
                  </button>
                  <button
                    type="button"
                    :class="{ disabled: page === pages.start }"
                    class="bh-page-item w-auto previous-page"
                    @click="decreasePage()"
                  >
                    <span>
                      <svg
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4.5"
                      >
                        <path
                          d="M15 5L9 12L15 19"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                    </span>
                  </button>
                  <button 
                    class="bh-page-item w-auto"
                    :class="{
                      'disabled bh-active': pages.start == page
                    }"
                    @click="setPage(pages.start)"
                  >
                    {{ pages.start }}
                  </button>
                  <button 
                    v-for="(paginationPage, index) in pages.preSteps"
                    :key="index"
                    class="bh-page-item w-auto"
                    :class="{
                      'disabled bh-active': paginationPage == page
                    }"
                    @click="setPage(paginationPage)"
                  >
                    {{ paginationPage }}
                  </button>
                  <button
                    v-for="(paginationPage) in pages.mainSteps"
                    :key="paginationPage"
                    :class="{
                      'disabled bh-active': paginationPage == page
                    }"
                    type="button"
                    class="bh-page-item w-auto"
                    @click="setPage(paginationPage)"
                  >
                    {{ paginationPage }}
                  </button>
                  <button 
                    v-for="(paginationPage, index) in pages.postSteps"
                    :key="index"
                    class="bh-page-item w-auto"
                    :class="{
                      'disabled bh-active': paginationPage == page
                    }"
                    @click="setPage(paginationPage)"
                  >
                    {{ paginationPage }}
                  </button>
                  <button
                    type="button"
                    class="bh-page-item w-auto next-page"
                    :class="{ disabled: page === pages.end }"
                    @click="increasePage()"
                  >
                    <span>
                      <svg
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4.5"
                      >
                        <path
                          d="M9 5L15 12L9 19"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                    </span>
                  </button>
                  <button
                    type="button"
                    class="bh-page-item w-auto last-page"
                    :class="{ disabled: page === pages.end }"
                    @click="lastPage()"
                  >
                    <span>
                      <svg
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4.5"
                      >
                        <path
                          d="M11 19L17 12L11 5"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                        <path
                          opacity="0.5"
                          d="M6.99976 19L12.9998 12L6.99976 5"
                          stroke="currentColor"
                          stroke-width="1.5"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                        />
                      </svg>
                    </span>
                  </button>
                </nav>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
      <image-modal
        v-if="showImageModal === true"
        :image-data="modalImage"
        :show-delete="true"
        @reaction="hideImagePreview()"
      />
    </Dialog>
  </TransitionRoot>
</template>

<script>
import { defineAsyncComponent } from 'vue';
import ApiService from 'SmugAdministration/js/services/api/api.service';
import ImageService from 'SmugAdministration/js/services/image/image.service';
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogOverlay } from '@headlessui/vue';
const ImageModal = defineAsyncComponent(() =>
  import("../../../../../Modal/ImageModal.vue" /* webpackChunkName: "administration-image-modal" */)
);

export default {
  name: "DetailFolder",
  components: {
    TransitionRoot,
    TransitionChild,
    Dialog,
    ImageModal,
    DialogOverlay,
    DialogPanel
  },
  props: {
    detailFolderTitle: {
      type: String,
      required: true
    },
    isSelection:{
      type: Boolean,
      required: false,
      default: false
    },
    detailFolder: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      files: [],
      expanded: '',
      showImageModal: false,
      modalImage: {},
      pages: [],
      range: [],
      absolute: 1,
      page: 1,
      filterData: {
        limit: 12
      }
    };
  },
  mounted() {
    this.getData();

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
    getIsSelection() {
      console.log('DetailFolder: ' + this.isSelection);
      return this.isSelection;
    },
    hideImagePreview() {
      this.showImageModal = false;
      this.modalImage = {};
      this.getData();
    },
    showDetailImage(image) {
      this.showImageModal = true;
      this.modalImage = image;
    },
    selectFile(file) {
      this.$emit('selectFile', file);
    },
    getImageSrc(image) {
      return ImageService.getImagePathFromMedia(image);
    },
    decreasePage() {
      this.page = this.page - 1;
      this.getData();
    },
    increasePage() {
      this.page = this.page + 1;
      this.getData();
    },
    lastPage() {
      this.page = this.pages.end;
      this.getData();
    },
    setPage(page) {
      this.page = page;
      this.getData();
    },
    getData() {
      ApiService.post('/be/api/custom/media/folder/files', {folder: this.detailFolder, page: this.page})
        .then(result =>  {
          this.files = result['files'];
          this.pages = result.pages;
          this.absolute = result.absolute;
          this.range = result.range;
        })
        .catch(error => {
          this.isLoading = false;
        })
        .then(function () {
        });
    }
  }
}
</script>
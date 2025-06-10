<template>
  <section name="imageUpload">
    <article
      v-cloak
      v-if="showUploadImages === true"
      aria-label="File Upload Modal"
      class="relative flex flex-col rounded-md mt-5"
      @drop.prevent="addNewImage()"
      @dragover.prevent
    >
      <!-- scroll area -->
      <section class="h-full overflow-auto p-8 w-full flex flex-col">
        <header class="border-dashed border-2 border-primary py-12 flex flex-col justify-center items-center">
          <p class="mb-3 font-semibold text-gray-900 flex flex-wrap justify-center">
            <span>{{ $t('DRAG_AND_DROP_TEXT') }}</span>
          </p>
          <input
            id="fileUploadHidden-input"
            type="file"
            class="hidden"
          >
          <button
            id="addFileButton"
            class="outline-link outline-link-primary loading-spinner-outline-link ml-10"
            @click="onClickAddFileButton()"
          >
            {{ $t('UPLOAD') }}
          </button>
        </header>

        <ul
          id="addImageGallery"
          class="flex flex-1 flex-wrap -m-1 mt-5"
        >
          <li
            v-if="uploadedImages.length === 0"
            id="addStoryEmpty"
            class="h-full w-full text-center flex flex-col items-center justify-center items-center"
          >
            <span class="text-small text-kelp-500">{{ $t('NO_FILES_TEXT') }}</span>
          </li>
          <li
            v-for="(image, index) in uploadedImages"
            :key="index"
            class="block p-1 w-1/2 sm:w-1/3 md:w-1/4 lg:w-1/6 xl:w-1/8 h-24"
          >
            <article
              tabindex="0"
              class="group hasImage w-full h-full rounded-md focus:outline-none focus:shadow-outline relative text-transparent hover:text-white shadow-sm"
            >
              <div class="flex flex-row pb-5 mb-5">
                <img
                  :src="image.tempUrl"
                  alt="upload preview"
                  loading="lazy"
                  class="img-preview w-full h-full max-h-24 sticky object-cover rounded-md bg-fixed"
                >

                <section class="flex flex-col rounded-md text-xs break-words w-full h-full z-20 absolute top-0">
                  <div class="flex text-kelp-700 py-2 px-3 transparent-bright">
                    <span
                      class="p-1 cursor-pointer"
                      @click="showImagePreview(image.tempUrl)"
                    >
                      <i>
                        <svg
                          class="fill-current w-4 h-4 ml-auto pt-"
                          xmlns="http://www.w3.org/2000/svg"
                          width="24"
                          height="24"
                          viewBox="0 0 24 24"
                        >
                          <path d="M5 8.5c0-.828.672-1.5 1.5-1.5s1.5.672 1.5 1.5c0 .829-.672 1.5-1.5 1.5s-1.5-.671-1.5-1.5zm9 .5l-2.519 4-2.481-1.96-4 5.96h14l-5-8zm8-4v14h-20v-14h20zm2-2h-24v18h24v-18z" />
                        </svg>
                      </i>
                    </span>

                    <p class="p-1 size text-xs" />
                    <button
                      class="delete ml-auto focus:outline-none p-1 rounded-md cursor-pointer"
                      @click="removeUploadedImage(index)"
                    >
                      <svg
                        class="pointer-events-none fill-current w-4 h-4 ml-auto"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                      >
                        <path
                          class="pointer-events-none"
                          d="M3 6l3 18h12l3-18h-18zm19-4v2h-20v-2h5.711c.9 0 1.631-1.099 1.631-2h5.316c0 .901.73 2 1.631 2h5.711z"
                        />
                      </svg>
                    </button>
                  </div>
                </section>
              </div>
            </article>
          </li>
        </ul>
        <div class="mt-4">
          <button
            class="outline-link outline-link-dark loading-spinner-outline-link mt-5 mb-3"
            style="transition: all 0.15s ease 0s;"
            @click="uploadedImages = []; showUploadImages = false"
          >
            {{ $t('ABORT') }}
          </button>
          <button
            v-if="uploadedImages.length > 0"
            class="outline-link outline-link-primary loading-spinner-outline-link ml-10"
            style="transition: all 0.15s ease 0s;"
            @click="uploadImage()"
          >
            <span v-if="isLoading === true">
              <svg
                role="status"
                class="inline w-4 h-4 mr-2 text-corduroy-200 animate-spin"
                viewBox="0 0 100 101"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                  fill="#cd9144"
                />
                <path
                  d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                  fill="#faf4ec"
                />
              </svg>
            </span>
            <span v-else>
              {{ $t('UPLOAD_IMAGES') }}
            </span>
          </button>
        </div>
        <div class="text-dark">
          <span class="text-sm">
            {{ $t('UPLOAD_IMAGE_INFO') }}
          </span>
        </div>
      </section>
    </article>
    <div
      v-else
      class="text-center"
    >
      <ul
        id="addMarketGallery"
        class="flex flex-1 flex-wrap -m-1 mt-5"
      >
        <li
          v-for="(image, index) in images"
          :key="index"
          class="block p-1 w-1/2 sm:w-1/3 md:w-1/4 lg:w-1/6 xl:w-1/8 h-24"
        >
          <article
            tabindex="0"
            class="group hasImage w-full h-full rounded-md focus:outline-none focus:shadow-outline bg-gray-100 cursor-pointer relative text-transparent hover:text-white shadow-sm"
          >
            <div class="flex flex-row pb-5 mb-5">
              <img
                :src="image.src"
                alt="upload preview"
                loading="lazy"
                class="img-preview w-full h-full max-h-24 sticky object-cover rounded-md bg-fixed"
              >

              <section class="flex flex-col rounded-md text-xs break-words w-full h-full z-20 absolute top-0">
                <div class="flex text-kelp-700 py-2 px-3 transparent-bright">
                  <span
                    class="p-1 cursor-pointer"
                    @click="showImagePreview(image.src)"
                  >
                    <i>
                      <svg
                        class="fill-current w-4 h-4 ml-auto pt-"
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                      >
                        <path d="M5 8.5c0-.828.672-1.5 1.5-1.5s1.5.672 1.5 1.5c0 .829-.672 1.5-1.5 1.5s-1.5-.671-1.5-1.5zm9 .5l-2.519 4-2.481-1.96-4 5.96h14l-5-8zm8-4v14h-20v-14h20zm2-2h-24v18h24v-18z" />
                      </svg>
                    </i>
                  </span>

                  <p class="p-1 size text-xs" />
                  <button
                    class="delete ml-auto focus:outline-none p-1 rounded-md cursor-pointer"
                    @click="removeImage(index)"
                  >
                    <svg
                      class="pointer-events-none fill-current w-4 h-4 ml-auto"
                      xmlns="http://www.w3.org/2000/svg"
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                    >
                      <path
                        class="pointer-events-none"
                        d="M3 6l3 18h12l3-18h-18zm19-4v2h-20v-2h5.711c.9 0 1.631-1.099 1.631-2h5.316c0 .901.73 2 1.631 2h5.711z"
                      />
                    </svg>
                  </button>
                </div>
              </section>
            </div>
          </article>
        </li>
      </ul>
      <div class="mt-4">
        <button
          class="outline-link loading-spinner-outline-link mt-5 mb-3"
          style="transition: all 0.15s ease 0s;"
          @click="uploadedImages = []; images = []; showUploadImages = true"
        >
          {{ $t('CLEAR') }}
        </button>
        <button
          class="outline-link outline-link-primary loading-spinner-outline-link ml-10"
          style="transition: all 0.15s ease 0s;"
          @click="uploadedImages = []; showUploadImages = true"
        >
          <span v-if="isLoading === true">
            <svg
              role="status"
              class="inline w-4 h-4 mr-2 text-corduroy-200 animate-spin"
              viewBox="0 0 100 101"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                fill="#cd9144"
              />
              <path
                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                fill="#faf4ec"
              />
            </svg>
          </span>
          <span v-else>
            {{ $t('UPLOAD_IMAGES') }}
          </span>
        </button>
      </div>
    </div>
    <div
      v-if="fileErrors.length > 0"
      class="w-full pb-5 mb-5 mt-5"
    >
      <div
        v-for="(fileError, index) in fileErrors"
        :key="index"
        class="flex items-center bg-espresso-500 text-white text-sm font-bold px-4 py-3 mb-5"
        role="alert"
      >
        <svg
          class="fill-current w-4 h-4 mr-2"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 20 20"
        ><path d="M12.432 0c1.34 0 2.01.912 2.01 1.957 0 1.305-1.164 2.512-2.679 2.512-1.269 0-2.009-.75-1.974-1.99C9.789 1.436 10.67 0 12.432 0zM8.309 20c-1.058 0-1.833-.652-1.093-3.524l1.214-5.092c.211-.814.246-1.141 0-1.141-.317 0-1.689.562-2.502 1.117l-.528-.88c2.572-2.186 5.531-3.467 6.801-3.467 1.057 0 1.233 1.273.705 3.23l-1.391 5.352c-.246.945-.141 1.271.106 1.271.317 0 1.357-.392 2.379-1.207l.6.814C12.098 19.02 9.365 20 8.309 20z" /></svg>
        <p>{{ fileError.fileName }}: {{ fileError.details }}</p>
        <span
          class="absolute right-20 cursor-pointer"
          @click="removeFileError(index)"
        >
          <span><icon-close /></span>
        </span>
      </div>
    </div>
  </section>
</template>

<script>
import axios from "axios";
import { defineAsyncComponent } from "vue";
const IconClose = defineAsyncComponent(() =>
  import("@core/js/icons/icons/IconClose" /* webpackChunkName: "image-close" */)
);

export default {
  name: "ImageUpload",
  components: {
    IconClose
  },
  props: {
    uploadCall: {
      type: String,
      required: true
    },
    images: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      previewImageUrl: '',
      showPreviewImage: false,
      showUploadImages: false,
      isLoading: false,
      uploadedImages: [],
      fileErrors: []
    };
  },
  methods: {
    removeFileError(error) {
      let fileErrorLength = this.fileErrors.length,
        i = 0;

      for (i; i <= fileErrorLength - 1; i++) {
        if (this.fileErrors[i].fileName === error.fileName) {
          this.$delete(this.fileErrors, i);
        }
      }
    },
    showImagePreview(url) {
      this.$emit('showImage', url);
    },
    removeImage(index) {
      this.$emit('deleteImage', index);
    },
    removeUploadedImage(index) {
      this.uploadedImages.splice(index, 1);
    },
    addImage(e) {
      let droppedFiles = e.dataTransfer.files;
      if(!droppedFiles) return;
      ([...droppedFiles]).forEach(f => {
        let image = f;
        image.tempUrl = URL.createObjectURL(f);
        if ((image.size / 1024) < 4000) {
          this.uploadedImages.push(image);
        } else {
          this.fileErrors.push({
            'message': 'FILE_SIZE_ERROR',
            'size': image.size,
            'fileName': image.name
          });
        }
      });
    },
    uploadImage() {
      if (this.isLoading === false) {
        const config = {
          headers: {
            Authorization: `Bearer ${this.$store.state.auth.token}`,
            'Content-Type': 'multipart/form-data'
          }
        };
        this.isLoading = true;
        let formData = new FormData();
        for (let x = 0; x < this.uploadedImages.length; x++) {
          formData.append("images[]", this.uploadedImages[x]);

          if (x === this.uploadedImages.length - 1) {
            axios.post(process.env.apiURL + this.uploadCall,
              formData, config
            ).then((response) => {
              let imageLength = 0;

              for (imageLength; imageLength <= response.data.length - 1; imageLength++) {
                if (typeof response.data[imageLength].id !== "undefined") {
                  this.images.push(response.data[imageLength])
                } else {
                  this.fileErrors.push(response.data[imageLength])
                }

                if (imageLength === response.data.length - 1) {
                  this.isLoading = false;
                  this.$forceUpdate();
                  this.$emit('imagesAdded', this.images);
                  this.uploadedImages = [];
                  this.showUploadImages = false;
                }
              }
            }).catch((error) => {
              if (typeof error.response !== 'undefined') {
                if (error.response.status === 401) {
                  window.localStorage.removeItem('user-token')
                  window.location.replace("/");
                }
              } else {
                this.fileErrors.push({
                  'fileName': 'GENERAL_UPLOAD_ERROR',
                  'details': (typeof error.response !== 'undefined') ? error.response.message : 'GENERAL_UPLOAD_ERROR_TEXT'
                });
              }
            });
          }
        }
      }
    },
    onClickAddFileButton() {
      if (typeof this.uploadedImages === 'undefined') {
        this.uploadedImages = [];
      }
      const hidden = document.getElementById("fileUploadHidden-input");
      hidden.click();
      hidden.onchange = (e) => {
        for (const file of e.target.files) {
          let image = file;
          image.tempUrl = URL.createObjectURL(file);
          if ((image.size / 1024) < 4000) {
            this.uploadedImages.push(image)
          } else {
            this.fileErrors.push({
              'message': 'FILE_SIZE_ERROR',
              'size': image.size,
              'fileName': image.name
            })
          }

          this.$forceUpdate();
        }
      };
    },
  }
}
</script>

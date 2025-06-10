<template>
  <section>
    <div v-if="editAllowed === true">
      <div
        v-if="isAdmin === true"
      >
        <div
          :title="$t('SELECT_FILE')"
          class="text-center cursor-pointer text-white bg-dark rounded-lg py-2 my-2"
          @click="showFileSelection()"
        >
          {{ $t('SELECT_FILE') }}
        </div>
      </div>
      <article
        class="relative flex flex-col rounded-md mt-5"
        @drop.prevent="addNewImage()"
        @dragover.prevent
      >
        <!-- scroll area -->
        <section
          class="h-full w-full flex flex-col"
          :class="{ 'p-8': !fieldConfig.mini, 'p-0': fieldConfig.mini}"
        >
          <div
            class="border-dashed border-2 border-primary text-center flex flex-col justify-center items-center z-0"
            :class="{'text-xs py-2': fieldConfig.mini, 'py-12': !fieldConfig.mini}"
          >
            <p
              class="mb-3 font-semibold text-gray-900 flex flex-wrap justify-center"
              :class="{'px-2': fieldConfig.mini}"
            >
              <span v-if="!fieldConfig.mini">{{ $t('DRAG_AND_DROP_TEXT') }}</span>
              <span v-else>{{ $t('DRAG_AND_DROP_TEXT_SHORT') }}</span>
            </p>
            <input
              id="fileUploadHidden-input"
              type="file"
              class="hidden"
            >
            <button
              id="addFileButton"
              class="btn btn-primary"
              @click="onClickAddFileButton()"
            >
              {{ $t('UPLOAD') }}
            </button>
          </div>

          <ul
            class="flex flex-1 flex-wrap -m-1 mt-5"
          >
            <li
              v-if="uploadedImages.length === 0"
              id="addStoryEmpty"
              class="h-full w-full text-center flex flex-col items-center justify-center items-center"
              :class="{'text-xs': fieldConfig.mini}"
            >
              <span class="text-small text-kelp-500">{{ $t('NO_FILES_TEXT') }}</span>
            </li>
            <li
              v-for="(image, index) in uploadedImages"
              :key="index"
              :class="{'block p-1 w-1/2 sm:w-1/3 md:w-1/4 lg:w-1/6 xl:w-1/8 h-16': !fieldConfig.mini, 'w-full h-16': fieldConfig.mini}"
            >
              <uploaded-image
                :field-config="fieldConfig"
                :field-value="image"
                @removeImage="removeUploadedImage(index)"
              />
            </li>
          </ul>
          <div class="mt-4">
            <button
              v-if="uploadedImages.length > 0"
              class="btn btn-primary my-3 w-42"
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

          <ul
            class="flex flex-1 flex-wrap -m-1 mt-5"
          >
            <li
              v-for="(image, index) in itemImages"
              :key="index"
              :class="{'block p-1 w-1/2 sm:w-1/3 md:w-1/4 lg:w-1/6 xl:w-1/8 h-16': !fieldConfig.mini, 'w-full h-16': fieldConfig.mini}"
            >
              <item-image
                :field-config="fieldConfig"
                :field-value="image"
                @removeImage="removeImage(index)"
              />
            </li>
          </ul>
        </section>
      </article>
    </div>
    <div
      v-else
      class="flex items-center p-3.5 rounded text-white bg-warning"
    >
      <span class="pr-2">{{ $t('NO_UPLOAD_FILE_RIGHTS') }}</span>
    </div>
    <file-selection
      v-if="showFileSelectionModal === true"
      @selectFile="setExistingFile($event)"
      @cancel="showFileSelectionModal = false"
    />
  </section>
</template>

<script>
import { defineAsyncComponent } from "vue";
import ApiService from '@SmugAdministration/js/services/api/api.service';
const UploadedImage = defineAsyncComponent(() =>
  import("./additional/fileUpload/UploadedImage.vue" /* webpackChunkName: "administration-file-upload-preview-image" */)
);
const ItemImage = defineAsyncComponent(() =>
  import("./additional/fileUpload/ItemImage.vue" /* webpackChunkName: "administration-file-upload-item-image" */)
);
const FileSelection = defineAsyncComponent(() =>
  import("./additional/fileUpload/FileSelection.vue" /* webpackChunkName: "administration-file-upload-file-selection" */)
);

export default {
  name: "FileUpload",
  components: {
    FileSelection,
    UploadedImage,
    ItemImage
  },
  props: {
    editAllowed:{
      type: Boolean,
      required: true
    },
    baseId:{
      type: String,
      required: false,
      default: null
    },
    fieldValue:{
      type: [String, Object, Array],
      required: false,
      default: ''
    },
    fieldConfig:{
      type: Object,
      required: false,
      default: () => ({})
    },
    fieldPlaceholder:{
      type: String,
      required: false,
      default: 'TEXT_PLACEHOLDER'
    }
  },
  data() {
    return {
      previewImageUrl: '',
      showFileSelectionModal: false,
      showPreviewImage: false,
      showUploadImages: false,
      isLoading: false,
      isAdmin: false,
      itemImages: [],
      uploadedImages: [],
      fileErrors: []
    };
  },
  mounted() {
    this.itemImages = (this.fieldValue.id) ? [this.fieldValue] : this.fieldValue;
    if (typeof this.itemImages === 'string') {
      this.itemImages = [];
    }
    const pathArray = window.location.pathname.split('/');
    this.isAdmin = pathArray[1] === 'admin';
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
    showFileSelection() {
      this.showFileSelectionModal = true;
    },
    showImagePreview(url) {
      this.$emit('showImage', url);
    },
    setExistingFile(file) {
      this.itemImages.push({media: file});
      this.showFileSelectionModal = false;
      this.$emit('updateValue', this.itemImages);
    },
    removeImage(index) {
      ApiService.put(this.fieldConfig.deleteCall, this.itemImages[index]).then(result => {
        this.itemImages.splice(index, 1);
        this.$emit('updateValue', this.itemImages);
      });
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
        this.isLoading = true;
        let formData = new FormData();
        for (let x = 0; x < this.uploadedImages.length; x++) {
          formData.append("files[]", this.uploadedImages[x]);

          if (x === this.uploadedImages.length - 1) {
            ApiService.post(
              this.fieldConfig.uploadCall + '/' + this.fieldConfig.assignAlbum,
              formData,
              true,
              {
                'Content-Type': 'multipart/form-data'
              }
            ).then(result => {
              if (this.baseId && this.fieldConfig.assignCall) {
                ApiService.put( 
                  this.fieldConfig.assignCall,
                  {
                    model: {
                      id: this.baseId
                    },
                    item: result
                  }
                ).then((res) => {
                  this.$emit('refreshData', true);
                  this.$emit('updateValue', this.itemImages);
                  this.isLoading = false;
                  this.uploadedImages = [];
                  this.showUploadImages = false;
                });
              } else {
                this.$emit('refreshData', true);
                this.isLoading = false;
                for (let uploadCount = 0; uploadCount <= result.length - 1; uploadCount++) {
                  if (this.fieldConfig.multiple === true) {
                    this.itemImages.push(result[uploadCount]);
                    
                    if (uploadCount === result.length - 1) {
                      this.showUploadImages = false;
                      this.uploadedImages = [];
                      this.$emit('updateValue', this.itemImages);
                    }
                  } else {
                    this.itemImages.splice(0, this.itemImages.length);
                    this.itemImages.push(result[uploadCount]);
                    this.$emit('updateValue', this.itemImages);
                  }
                }
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
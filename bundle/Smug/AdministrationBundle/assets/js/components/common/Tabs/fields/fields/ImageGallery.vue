<template>
  <div>
    <div v-if="images.length === 0">
      <div class="flex items-center p-3.5 rounded text-white bg-success">
        <span class="pr-2">{{ $t('NO_IMAGES_UPLOADED_YET') }}</span>
      </div>
    </div>
    <div
      v-else
      class="grid grid-cols-2 md:grid-cols-4 gap-4"
    >
      <div 
        v-for="(imageCol, imageColkey) in images"
        :key="imageColkey"
        class="grid gap-4"
      >
        <div
          v-for="(image, imagekey) in imageCol"
          :key="imagekey"
          class="cursor-pointer"
        >
          <button-controls
            v-if="fieldConfig.controls && fieldConfig.controls.length > 0"
            :key="reload"
            :controls="fieldConfig.controls"
            :item="image"
            @called="getData()"
          />
          <div
            @click="showImg(image)"
          >
            <img
              :src="getImage(image)"
              :alt="image.media.file"
              class="rounded-lg w-full mb-3"
              :class="getActiveClass(image)"
            >
          </div>
          <span class="text-sm italic">({{ image.media.file }}.{{ image.media.extension }})</span>
        </div>
      </div>
      <vue-easy-lightbox
        :visible="lightboxVisible"
        :imgs="imageSources"
        :index="lightboxIndex"
        @hide="onHide()"
      />
    </div>
  </div>
</template>

<script>
import ApiService from '../../../../../services/api/api.service';
import { defineAsyncComponent } from "vue";
const ButtonControls = defineAsyncComponent(() =>
  import("./additional/button/ButtonControls.vue" /* webpackChunkName: "administration-button-controls" */)
);
import ImageService from '../../../../../services/image/image.service';

export default {
  name: "ImageGallery",
  components: {
    ButtonControls
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
      type: String,
      required: false,
      default: '',
      immediate: true
    },
    fieldConfig:{
      type: Object,
      required: false,
      default: () => ({})
    },
    fieldPlaceholder:{
      type: String,
      required: false,
      default: 'IMAGE_GALLERY_PLACEHOLDER'
    }
  },
  data() {
    return {
      lightboxIndex: 0,
      reload: 0,
      lightboxVisible: false,
      images: [],
      handleImage: {},
      handleCall: '',
      imageSources: []
    };
  },
  mounted() {
    if (this.fieldConfig.getCall) {
      this.getData();
    } else {
      this.processImages(this.fieldValue);
    }
  },
  methods: {
    getImage(image) {
      return ImageService.getImagePathFromMedia(image.media);
    },
    showImg(image) {
      const src = ImageService.getImagePathFromMedia(image.media);
      this.lightboxIndex = this.imageSources.indexOf(src);
      this.lightboxVisible = true;
    },
    onHide() {
      this.lightboxVisible = false;
      this.lightboxIndex = 0;
    },
    getActiveClass(image) {
      return (image.main && image.main === true) ? 'border-4 border-primary shadow-lg' : '';
    },
    getData() {
      if (this.fieldValue.length > 0) {
        let images = {
          col_1: [],
          col_2: [],
          col_3: [],
          col_4: []
        };
        let colCount = 1;
        for (let imageCount = 0; imageCount <= this.fieldValue.length - 1; imageCount++) {
          this.imageSources.push(ImageService.getImagePathFromMedia(this.fieldValue[imageCount].media));
          images['col_' + colCount].push(this.fieldValue[imageCount]);
          colCount++;

          if (colCount === 5) {
            colCount = 1;
          }
          if (imageCount === this.fieldValue.length - 1) {
            this.images = images;
            this.reload++;
            this.isLoading = false;
          }
        }
      } else {
        this.isLoading = true;
        
        ApiService.get(this.fieldConfig.getCall, this.baseId)
          .then(result =>  {
            this.processImages(result);
          })
          .catch(error => {
            this.isLoading = false;
          })
          .then(function () {
          });
      }
    },
    processImages(imageData) {
      let images = {
        col_1: [],
        col_2: [],
        col_3: [],
        col_4: []
      };
      let colCount = 1;
      for (let imageCount = 0; imageCount <= imageData.length - 1; imageCount++) {
        this.imageSources.push(ImageService.getImagePathFromMedia(imageData[imageCount].media));
        images['col_' + colCount].push(imageData[imageCount]);
        colCount++;

        if (colCount === 5) {
          colCount = 1;
        }
        if (imageCount === imageData.length - 1) {
          this.images = images;
          this.reload++;
          this.isLoading = false;
        }
      }
    }
  }
}
</script>
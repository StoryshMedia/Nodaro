<template>
  <section>
    <div
      class="w-full lazy object-cover transition-all duration-500 ease-in-out transform group-hover:scale-110"
      :class="height"
      style="background-repeat: no-repeat; background-position: center;"
      :style="{ 'background-size': '100% 100%' }"
      :data-bg="getViewportImage()"
      :data-bg-hidpi="getViewportImage()"
      @click="showImage()"
    />
    <image-preview
      v-if="imageUrl !== ''"
      :show-preview-image="showImageModal"
      :url="imageUrl"
      @showPreviewImage="toggleImageModal()"
    />
  </section>
</template>

<script>
import { defineAsyncComponent } from "vue";
const ImagePreview = defineAsyncComponent(() =>
  import("./ImagePreview" /* webpackChunkName: "image-preview" */)
);

export default {
  name: "DetailImage",
  components: {
    ImagePreview
  },
  props: {
    headline: {
      type: String,
      required: true
    },
    height: {
      type: String,
      required: false,
      default: 'h-paginatedListItem'
    },
    disableModal: {
      type: Boolean,
      required: false,
      default: false
    },
    image: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      deviceWidth: 0,
      showImageModal: false,
      imageUrl: ''
    }
  },
  created()  {
    this.deviceWidth = window.innerWidth;
  },
  methods: {
    getViewportImage() {
      if (this.deviceWidth > 480) {
        setTimeout(() => {
          window.LL.update();
        }, "100");
        return this.getImageSrc(this.image, 'desktop');
      } else {
        setTimeout(() => {
          window.LL.update();
        }, "100");
        return this.getImageSrc(this.image, 'mobile');
      }
    },
    showImage() {
      if (this.disableModal === false) {
        this.imageUrl = this.getViewportImage(this.image);
      }
    },
    toggleImageModal() {
      this.imageUrl = '';
      this.showImageModal = false;
    },
    getImageSrc(image, viewport) {
      if (typeof image === 'string' || image instanceof String) {
        return image;
      }
      if (image.media) {
        if (typeof image.media.thumbnails[viewport] === 'undefined') {
          return image.media.src;
        } else {
          return image.media.thumbnails[viewport]['list'].img;
        }
      }
    },
    getFullSizeImage(image) {
      if (typeof image === 'string' || image instanceof String) {
        return image;
      }
      if (image.media) {
        return image.media.src;
      }
    },
  }
}
</script>
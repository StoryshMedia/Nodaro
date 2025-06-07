<template>
  <div class="pl-8 grid grid-cols-3 sm:grid-cols-5 lg:grid-cols-8 gap-3 my-6">
    <div
      v-for="(file, fileindex) in files"
      :key="fileindex"
      class=""
    >
      <img
        v-if="isImage(file)"
        :src="getImage(file)"
        class="w-14 h-14 object-cover cursor-pointer rounded-md shadow-lg relative top-0 transition-all duration-300 hover:-top-0.5 hover:shadow-none"
        @click="showImg(file)"
      >
      <div 
        v-else
        @click="showFile(file)"
      >
        <span class="flex justify-center items-center w-14 h-14 object-cover cursor-pointer rounded-md shadow-lg transition-all duration-300 hover:-top-0.5 hover:shadow-none text-center rounded-lg object-cover bg-info text-lg">{{ file.media.extension.toUpperCase() }}</span>
      </div>
    </div>
    <vue-easy-lightbox
      :visible="lightboxVisible"
      :imgs="imageSources"
      :index="lightboxIndex"
      @hide="onHide()"
    />
  </div>
</template>

<script>
import ImageService from '../../../../../../../services/image/image.service';

export default {
  name: "Files",
  components: {
  },
  props: {
    files: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      lightboxIndex: 0,
      lightboxVisible: false,
      imageSources: []
    };
  },
  mounted() {
    this.setImageSources();
  },
  methods: {
    getImage(item) {
      return ImageService.getImagePathFromMedia(item.media);
    },
    setImageSources() {
      for (let imageCount = 0; imageCount <= this.files.length - 1; imageCount++) {
        this.imageSources.push(this.getImage(this.files[imageCount]));
      }
    },
    onHide() {
      this.lightboxVisible = false;
      this.lightboxIndex = 0;
    },
    showImg(image) {
      const src = this.getImage(image);
      this.lightboxIndex = this.imageSources.indexOf(src);
      this.lightboxVisible = true;
    },
    showFile(file) {
      window.open(ImageService.getFilePath(file.media), '_blank')
    },
    isImage(item) {
      return ImageService.isImage(item);
    }
  }
}
</script>
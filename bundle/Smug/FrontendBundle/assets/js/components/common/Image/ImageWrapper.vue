<template>
  <section class="overflow-y-auto max-h:100vh">
    <div v-if="images.length === 1">
      <div
        v-for="(img, idx) in images"
        :key="idx"
        class="py-2 group overflow-hidden mx-auto h-mobilePaginatedListItem md:h-tabletPaginatedListItem xl:h-paginatedListItem cursor-pointer"
        @click="showImage(img)"
      >
        <div
          class="group lazy overlay overflow-hidden transition-all relative flex-initial rounded-md h-full background overlay-black" 
          :data-bg="getImageSrc(item, idx, 'desktop')"
          :data-bg-hidpi="getImageSrc(item, idx, 'desktop')"
        >
          <div class="h-full w-full backdrop-filter backdrop-blur-sm transition-all opacity-0 group-hover:opacity-100" />
        </div>
      </div>
    </div>
    <div v-else>
      <div class="cursor-move overflow-hidden h-full w-full">
        <div class="select-none h-full flex">
          <div
            v-for="(img, idx) in images"
            :key="idx"
            class="index-carousel-slide flex w-full flex-full pt-24 h-mobilePaginatedListItem md:h-tabletPaginatedListItem xl:h-paginatedListItem overlay items-center background overlay-gray-400 overflow-hidden"
            :class="(idx === currentIndex) ? '' : 'hidden'"
            @click="showImage(img)"
          >
            <div class="w-full">
              <div class="absolute top-0 right-0 left-0 bottom-0 w-full text-center lg:text-left">
                <div
                  class="group lazy overlay overflow-hidden transition-all relative flex-initial rounded-md h-full background overlay-black" 
                  :data-bg="getImageSrc(item, idx, 'desktop')"
                  :data-bg-hidpi="getImageSrc(item, idx, 'desktop')"
                >
                  <div class="h-full w-full backdrop-filter backdrop-blur-sm transition-all opacity-0 group-hover:opacity-100" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div
        v-if="showNavigation === true"
        class="absolute z-20 bottom-8 left-0 right-0"
      >
        <div class="container relative px-4 sm:px-8 mx-auto">
          <div class="absolute flex right-32 pr-8 bottom-0">
            <button
              id="embla__prev"
              aria-label="Prev Post"
              class="embla__prev group flex items-center justify-center flex-row"
              type="button"
              @click="prev()"
            >
              <span class="leading-none mr-0 lg:mr-6 font-sans transition-all font-semibold text-base text-white hidden lg:block group-hover:text-primary">{{ $t("PREV") }}</span>
              <span class="svg-round-btn transition-all group-hover:text-primary group-hover:border-primary"><icon-left /></span>
            </button>
          </div>
          <div class="absolute flex right-0 pr-8 bottom-0">
            <button
              id="embla__next"
              aria-label="Next Post"
              class="embla__next group flex items-center justify-center flex-row"
              type="button"
              @click="next()"
            >
              <span class="svg-round-btn mr-0 lg:mr-6 transition-all group-hover:text-primary group-hover:border-primary"><icon-right /></span>
              <span class="leading-none font-sans transition-all font-semibold text-base text-white hidden lg:block group-hover:text-primary">{{ $t("NEXT") }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>
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
import ImageService from '@SmugAdministration/js/services/image/image.service';
const ImagePreview = defineAsyncComponent(() =>
  import("./ImagePreview" /* webpackChunkName: "image-preview" */)
);
const IconRight = defineAsyncComponent(() =>
  import("@core/js/icons/icons/IconRight" /* webpackChunkName: "icon-right" */)
);
const IconLeft = defineAsyncComponent(() =>
  import("@core/js/icons/icons/IconLeft" /* webpackChunkName: "icon-left" */)
);

export default {
  name: "ImageWrapper",
  components: {
    ImagePreview,
    IconRight,
    IconLeft
  },
  inject: ['dataset'],
  data() {
    return {
      showImageModal: false,
      showNavigation: false,
      imageUrl: '',
      currentIndex: 0,
      images: [],
      item:  {}
    }
  },
  async created() {
    await this.setProps();
  },
  methods: {
    setProps() {
      this.images = JSON.parse(this.dataset.images);
      this.item = JSON.parse(this.dataset.item);
      this.showNavigation = (this.dataset.showNavigation !== 'false');
    },
    showImage(image) {
      this.imageUrl = this.getViewportImage(image);
    },
    getViewportImage(image) {
      if (window.innerWidth > 480) {
        return this.getSingleImageSrc(image, 'desktop');
      } else {
        return this.getSingleImageSrc(image, 'mobile');
      }
    },
    getSingleImageSrc(image, viewport) {
      if (typeof image === 'string' || image instanceof String) {
        return image;
      }
      if (image.media) {
        if (typeof image.media.thumbnails === 'undefined' || typeof image.media.thumbnails[viewport] === 'undefined') {
          return image.src;
        } else {
          return image.media.thumbnails[viewport]['list'].img;
        }
      }
    },
    toggleImageModal() {
      this.imageUrl = '';
      this.showImageModal = false;
    },
    next: function() {
      if (this.currentIndex === this.images.length - 1) {
        this.currentIndex = 0;
      } else {
        this.currentIndex += 1;
      }
    }, 
    prev: function() {
      if (this.currentIndex === 0) {
        this.currentIndex = this.images.length - 1;
      } else {
        this.currentIndex -= 1;
      }
    },
    getImageSrc(item) {
      return ImageService.getImageFromItem(item);
    }
  }
}
</script>
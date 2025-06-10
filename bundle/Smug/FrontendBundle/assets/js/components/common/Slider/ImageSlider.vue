<template>
  <div
    id="controls-carousel"
    class="relative"
    data-carousel="slide"
  >
    <!-- Carousel wrapper -->
    <div class="overflow-hidden relative rounded-lg">
      <div
        v-for="(i, index) in images"
        :key="i"
        class="duration-700 ease-in-out h-mobilePaginatedListItem md:h-tabletPaginatedListItem xl:h-paginatedListItem"
        data-carousel-item
        :class="(index === currentIndex) ? '' : 'hidden'"
      >
        <detail-image
          :headline="title"
          :image="i"
          :height="'h-mobilePaginatedListItem md:h-tabletPaginatedListItem xl:h-paginatedListItem'"
        />
      </div>
    </div>
    <!-- Slider controls -->
    <button
      type="button"
      class="flex absolute top-0 left-0 z-30 justify-center items-center px-4 h-full cursor-pointer group focus:outline-none"
      data-carousel-prev
      @click="prev()"
    >
      <span class="inline-flex justify-center items-center w-10 h-10 rounded-full bg-white group-hover:bg-white/50 group-focus:ring-4 group-focus:ring-white group-focus:outline-none">
        <svg
          class="w-6 h-6 text-white"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg"
        ><path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M15 19l-7-7 7-7"
        /></svg>
        <span class="hidden">Previous</span>
      </span>
    </button>
    <button
      type="button"
      class="flex absolute top-0 right-0 z-30 justify-center items-center px-4 h-full cursor-pointer group focus:outline-none"
      data-carousel-next
      @click="next()"
    >
      <span class="inline-flex justify-center items-center w-10 h-10 rounded-full bg-white group-hover:bg-white/50 group-focus:ring-4 group-focus:ring-white group-focus:outline-none">
        <svg
          class="w-6 h-6 text-white"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg"
        ><path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M9 5l7 7-7 7"
        /></svg>
        <span class="hidden">Next</span>
      </span>
    </button>
  </div>
</template>

<script>
import { defineAsyncComponent } from "vue";
const DetailImage = defineAsyncComponent(() =>
  import("../Image/DetailImage" /* webpackChunkName: "detail-image" */)
);

export default {
  name: "ImageSlider",
  components: {
    DetailImage
  },
  props: {
    images: {
      type: Array,
      required: true
    },
    title: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      currentIndex: 0
    };
  },
  methods: {
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
    }
  }
};
</script>
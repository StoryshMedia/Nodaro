<template>
  <article v-if="getData === false">
    <a
      :href="slug"
      :title="headline"
    >
      <div
        class="mx-2 my-2 h-auto overflow-hidden group"
      >
        <span
          class="absolute top-6 left-1/2 transform -translate-x-1/2 z-20 text-center"
        >
          <rating
            v-if="rating !== ''"
            :value="rating"
            :tile="true"
          />
        </span>
        <div class="aspect-[4/3] flex items-center w-full h-full max-h-68 min-h-68 overflow-hidden">
          <picture
            class="transition-all object-none inline-block align-middle mx-auto duration-500 ease-in-out transform group-hover:scale-110 h-full w-full"
          >
            <source
              :srcset="getImageSrc(viewItem, 'desktop')"
              :title="viewItem.title"
              class="w-full"
              media="(min-width: 601px)"
            >
            <source
              :srcset="getImageSrc(viewItem, 'mobile')"
              :title="viewItem.title"
              class="w-full"
              media="(min-width: 480px)"
            >
            <img
              :alt="viewItem.title"
              :title="viewItem.title"
              class="w-full min-h-68"
              loading="lazy"
              :src="getImageSrc(viewItem, 'mobile')"
            >
          </picture>
        </div>
        <span
          class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 uppercase text-2xl text-white font-semibold z-10 text-center"
        >
          {{ headline }}
        </span>
      </div>
    </a>
  </article>
  <article v-else>
    <a
      v-if="viewItem !== null"
      :href="mode + viewItem.slug"
      :title="headline"
    >
      <div
        class="mx-2 my-2 h-64 lg:h-80 xl:h-96 overflow-hidden group"
      >
        <span
          class="absolute top-6 left-1/2 transform -translate-x-1/2 z-20 text-center"
        >
          <rating
            v-if="rating !== ''"
            :value="rating"
            :tile="true"
          />
        </span>
        <detail-image
          :headline="headline"
          :height="'h-64 lg:h-80 xl:h-96'"
          :image="getImageSrc(viewItem, 'desktop')"
        />
        <span
          class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 uppercase text-2xl text-white font-semibold z-10 text-center"
        >
          {{ viewItem.title }}
        </span>
      </div>
    </a>
    <div
      v-else
      class="block relative lg:mb-0 mb-5 group"
    >
      <div
        class="max-w-sm overflow-hidden px-1"
      >
        <div class="lg:h-48 rounded-lg bg-gray-400  md:h-36 w-full object-cover object-center" />
      </div>
      <div class="px-3 py-4">
        <p class="leading-relaxed mb-3 rounded-md w-full h-3 animate-pulse bg-gray-400 " />
        <p class="leading-relaxed mb-3 rounded-md w-2/3 h-3 animate-pulse bg-gray-400 " />
        <p class="leading-relaxed mb-3 rounded-md w-1/2 h-3 animate-pulse bg-gray-400 " />
      </div>
    </div>
  </article>
</template>

<script>
import axios from "axios";
import {defineAsyncComponent} from "vue";

const DetailImage = defineAsyncComponent(() => import("../Image/DetailImage" /* webpackChunkName: "detail-image" */));
const Rating = defineAsyncComponent(() => import("../Slider/Rating" /* webpackChunkName: "rating" */));

export default {
  name: "Tile",
  components: {
    Rating,
    DetailImage
  },
  props: {
    getData: {
      type: Boolean,
      required: false,
      default: false
    },
    getCall: {
      type: String,
      required: false,
      default: ''
    },
    mode: {
      type: String,
      required: false,
      default: ''
    },
    headline: {
      type: String,
      required: false,
      default: ''
    },
    image: {
      type: String,
      required: false,
      default: ''
    },
    slug: {
      type: String,
      required: true
    },
    rating: {
      type: String,
      required: false,
      default: ''
    },
    item: {
      type: Object,
      required: false,
      default: null
    }
  },
  data() {
    return {
      viewItem: null
    }
  },
  created() {
    if (this.getData === true) {
      this.getItem();
    } else {
      this.viewItem = this.item;
    }
  },
  methods: {
    getItem() {
      axios.get(process.env.apiURL + this.getCall + this.slug)
        .then(response =>  {
          this.viewItem = response.data;
        })
        .catch(function (error) {
        })
        .then(function () {
        });
    },
    getImageSrc(item, viewport) {
      if (typeof item.images === "undefined") {
        return item.image;
      }

      if (item.images.length === 0) {
        return item.image;
      }

      let imageCount = item.images.length,
        count = 0,
        hasMain = false;

      if (imageCount === 1) {
        return item.images[0].media.thumbnails[viewport]['list'].img;
      }

      for (count; count <= imageCount - 1; count++) {
        if (item.images[count].main === true) {
          hasMain = true;
          return item.images[count].media.thumbnails[viewport]['list'].img;
        }

        if (count === imageCount - 1 && hasMain === false) {
          return item.images[0].media.thumbnails[viewport]['list'].img;
        }
      }
    },
    getImageHeightClass() {
      return (this.showFilters === true) ? 'h-96 md:h-paginatedListItem' : 'h-96';
    },
  }
}
</script>

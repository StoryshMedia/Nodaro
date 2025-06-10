<template>
  <a
    :href="getSlug()"
    :title="headline"
    class="block relative lg:mb-0 mb-5 group"
  >
    <div
      class="overflow-hidden"
    >
      <span
        v-if="rating !== null"
        class="absolute top-6 transform z-20 text-center"
      >
        <rating
          :value="rating"
          :tile="true"
        />
      </span>
      <detail-image
        :headline="headline"
        :disable-modal="true"
        :height="height"
        :image="image"
      />
    </div>
    <div class="px-3 py-4">
      <div class="text-lg text-left">
        <span>{{ truncate(headline, 35, '...') }}</span>
      </div>
      <div class="text-lg text-left pt-3">
        <span class="uppercase underline hover:no-underline">{{ $t('READ') }}</span>
      </div>
    </div>
  </a>
</template>

<script>
import {defineAsyncComponent} from "vue";

const DetailImage = defineAsyncComponent(() => import("../Image/DetailImage" /* webpackChunkName: "detail-image" */));
const Rating = defineAsyncComponent(() => import("../Slider/Rating" /* webpackChunkName: "rating" */));

export default {
  name: "Item",
  components:  {
    Rating,
    DetailImage
  },
  props: {
    headline: {
      type: String,
      required: true
    },
    image: {
      type: Object,
      required: true
    },
    height: {
      type: String,
      required: false,
      default: 'h-mobilePaginatedListItem md:h-tabletPaginatedListItem xl:h-paginatedListItem'
    },
    rating: {
      type: Number,
      required: false,
      default: null
    },
    slug: {
      type: String,
      required: true
    }
  },
  methods: {
    getSlug() {
      return this.slug;
    },
    truncate: function (text, length, suffix) {
      if (typeof text === 'undefined') {
        return '';
      }
      if (text.length > length) {
        return text.substring(0, length) + suffix;
      } else {
        return text;
      }
    }
  }
}
</script>

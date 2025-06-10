<template>
  <section>
    <loading-tile v-if="isLoading === true" />
    <section
      v-else
    >
      <article
        v-if="(viewItem !== null && typeof viewItem.slug === 'undefined')"
        class="flex w-full h-full relative flex-col post-card rounded-md shadow-md hover:shadow-2xl"
      >
        <div
          class="group lazy overlay overflow-hidden transition-all relative flex-initial rounded-md h-full background overlay-black" 
          :data-bg="getFallbackImage()"
          :data-bg-hidpi="getFallbackImage()"
        >
          <div class="px-8 py-4 absolute bottom-0 right-0 left-0 flex-auto overlay-content">
            <h6>
              <p
                class="line-clamp-3 font-bold mt-2 block text-xl text-white group-hover:text-primary"
              >
                <span>{{ $t('DATA_NOT_FOUND_ERROR') }}</span>
              </p>
            </h6>
          </div>
        </div>
      </article>
      <div 
        class="shadow-md overlay lazy overlay-medium-pagination-tile h-full p-8 overflow-hidden relative hover:shadow-3xl transition-all flex items-center rounded-md background"
        :data-bg="getFallbackImage()"
        :data-bg-hidpi="getFallbackImage()"
      >
        <a 
          :href="slug"
          class="group block w-full h-full"
        >
          <div class="w-full flex items-center">
            <div class="w-1/4">
              <img
                :src="getImageSrc(viewItem)"
                class="object-cover transition-all shadow-md group-hover:shadow-2xl fit-cover rounded-full"
                :class="getImageClass()"
                :alt="viewItem.label"
              >
            </div>
            <div class="ml-6 w-3/4">
              <h5
                class="group-hover:text-primary text-black transition-colors"
                :class="getHeadlineSize()"
              >{{ viewItem.label }}</h5>
            </div>
          </div>
          <p class="text-gray-700 font-medium text-sm flex mt-4 line-clamp-3 h-20">
            <span
              v-html="getTeaser(viewItem.biography ?? '')"
            />
          </p>
        </a>
      </div>
    </section>
  </section>
</template>

<script>
import {defineAsyncComponent} from "vue";
import ApiService from '@SmugAdministration/js/services/api/api.service';
import TextService from '@SmugAdministration/js/services/text/text.service';
import ImageService from '@SmugAdministration/js/services/image/image.service';
const LoadingTile = defineAsyncComponent(() =>
  import("../../../../common/Content/LoadingTile" /* webpackChunkName: "loading-tile" */)
);

export default {
  name: "MediumTilePaginationCard",
  components: {
    LoadingTile
  },
  props: {
    getCall: {
      type: String,
      required: false,
      default: ''
    },
    item: {
      type: Object,
      required: false,
      default: null
    },
    mode: {
      type: String,
      required: true
    },
    slug: {
      type: String,
      required: true
    },
    imageClass: {
      type: String,
      required: false,
      default: 'h-12 w-12 md:w-20 md:h-20'
    },
    class: {
      type: String,
      required: false,
      default: 'h-72 lg:h-96 xl:h-72'
    },
    headlineSize: {
      type: String,
      required: false,
      default: ''
    }
  },
  data() {
    return {
      viewItem: null,
      schemaData: {
      },
      isLoading: true
    }
  },
  mounted() {
    this.getItem();
  },
  unmounted() {
    this.viewItem = null;
  },
  methods: {
    getItem() {
      if (this.item !== null && typeof this.item !== 'undefined') {
        this.viewItem = this.item;
        this.isLoading = false;
      } else {
        ApiService.get(this.getCall, this.slug)
          .then(result =>  {
            this.viewItem = result;
            this.isLoading = false;
            this.$forceUpdate();
          })
          .catch(function (error) {
          })
          .then(function () {
          });
      }
    },
    getClass() {
      return this.class;
    },
    getHref() {
      return this.slug;
    },
    getImageClass() {
      return this.imageClass;
    },
    getHeadlineSize() {
      return this.headlineSize;
    },
    getText(text) {
      return TextService.getOutput(text, 56);
    },
    getTeaser(teaser) {
      return TextService.getOutput(teaser, 144);
    },
    getFallbackImage: function () {
      return ImageService.getFallbackImage();
    },
    getImageSrc(item) {
      const image = JSON.parse(item.image) ?? item.image;
      return ImageService.getImagePathFromMedia(image.media);
    }
  }
}
</script>

<template>
  <section>
    <loading-tile v-if="isLoading === true" />
    <section
      v-else
      :class="getClass()"
    >
      <article
        v-if="(viewItem !== null && typeof viewItem.slug === 'undefined')"
        data-aos="fade-zoom-in"
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
      <article
        v-else
        data-aos="fade-zoom-in"
        class="flex w-full h-full relative flex-col post-card rounded-md shadow-md hover:shadow-2xl"
      >
        <div class="flex items-center justify-between absolute top-8 right-8 left-8 z-20">
          <rating
            v-if="viewItem.rating"
            :value="viewItem.rating"
          />
        </div>
        <div
          class="group lazy overlay overflow-hidden transition-all relative flex-initial rounded-md h-full background overlay-black" 
          :data-bg="getImageSrc(viewItem)"
          :data-bg-hidpi="getImageSrc(viewItem)"
        >
          <a 
            :href="getHref()"
            :title="getText(viewItem)"
            class="absolute block w-full h-full"
          >
            <div class="px-8 py-4 absolute bottom-0 right-0 left-0 flex-auto overlay-content">
              <p
                class="line-clamp-3  font-bold mt-2 block text-xl text-white group-hover:text-primary"
              >
                {{ getText(viewItem) }}
              </p>
              <p
                v-if="viewItem.subTitle"
                class="text-white font-medium transition-all line-clamp-2 text-sm"
              >
                {{ viewItem.subTitle }}
              </p>
            </div>
          </a>
        </div>
      </article>
    </section>
  </section>
</template>

<script>
import {defineAsyncComponent} from "vue";
import ApiService from '@SmugAdministration/js/services/api/api.service';
import TextService from '@SmugAdministration/js/services/text/text.service';
import ImageService from '@SmugAdministration/js/services/image/image.service';
const Rating = defineAsyncComponent(() => import("../../../../common/Slider/Rating" /* webpackChunkName: "rating" */));
const LoadingTile = defineAsyncComponent(() =>
  import("../../../../common/Content/LoadingTile" /* webpackChunkName: "loading-tile" */)
);

export default {
  name: "PaginationCard",
  components: {
    Rating,
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
    class: {
      type: String,
      required: false,
      default: 'h-72 lg:h-96 xl:h-72'
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
          .then(response =>  {
            this.viewItem = response.data;
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
    getText(item) {
      if (typeof item.title === 'undefined') {
        return TextService.getOutput(item.label, 56);
      } else {
        return TextService.getOutput(item.title, 56);
      }
    },
    getTeaser(teaser) {
      return TextService.getOutput(teaser, 144);
    },
    getFallbackImage: function () {
      return ImageService.getFallbackImage();
    },
    getImageSrc(item) {
      if (typeof item.image === 'undefined') {
        return ImageService.getFallbackImage();
      }
      
      const image = JSON.parse(item.image) ?? item.image;

      if (image.media) {
        return ImageService.getImagePathFromMedia(image.media);
      } else {
        return ImageService.getImagePath(image);
      }
    }
  }
}
</script>

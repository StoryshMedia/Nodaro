<template>
  <section>
    <loading-tile v-if="isLoading === true" />
    <section
      v-if="isLoading === false"
      :class="getClass()"
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
      <article
        v-else
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
          :data-bg="getImageSrc(viewItem, 'desktop')"
          :data-bg-hidpi="getImageSrc(viewItem, 'desktop')"
          itemscope=""
        >
          <a 
            :href="getHref()"
            :title="(viewItem.title) ? viewItem.title : viewItem.completeName"
            :target="getTarget()"
            class="absolute block w-full h-full"
          >
            <div class="px-8 py-4 absolute bottom-0 right-0 left-0 flex-auto overlay-content">
              <p
                v-if="typeof viewItem.title !== 'undefined'"
                class="line-clamp-3  font-bold mt-2 block text-xl text-white group-hover:text-primary"
              >
                {{ viewItem.title }}
              </p>
              <p
                v-else
                class="line-clamp-3  font-bold mt-2 block text-xl text-white group-hover:text-primary"
              >
                {{ viewItem.completeName }}
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
const Rating = defineAsyncComponent(() => import("../Slider/Rating" /* webpackChunkName: "rating" */));
const LoadingTile = defineAsyncComponent(() =>
  import("../Content/LoadingTile" /* webpackChunkName: "loading-tile" */)
);
import ApiService from '@SmugAdministration/js/services/api/api.service';
import TextService from '@SmugAdministration/js/services/text/text.service';
import ImageService from '@SmugAdministration/js/services/image/image.service';

export default {
  name: "ApiCard",
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
    },
    target: {
      type: String,
      required: false,
      default: '_self'
    }
  },
  data() {
    return {
      viewItem: null,
      isLoading: true
    }
  },
  created() {
    this.getItem();
  },
  methods: {
    getItem() {
      if (this.item !== null && typeof this.item !== 'undefined') {
        this.viewItem = this.item;
        this.isLoading = false;
      } else {
        ApiService.get(this.getCall, this.slug, false)
          .then(result =>  {
            this.viewItem = result;
            this.isLoading = false;
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
    getTarget() {
      return this.target;
    },
    getHref() {
      return this.slug;
    },
    getTeaser(teaser) {
      return TextService.getOutput(teaser, 144);
    },
    getFallbackImage: function () {
      return ImageService.getFallbackImage();
    },
    getImageSrc(item, viewport) {
      return ImageService.getImageFromItem(item);
    }
  }
}
</script>

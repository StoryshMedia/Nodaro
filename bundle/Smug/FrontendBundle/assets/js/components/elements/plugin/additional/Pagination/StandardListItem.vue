<template>
  <section>
    <loading-tile v-if="isLoading === true" />
    <section
      v-else
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
      >
        <a 
          :href="getHref()"
          :title="getTitle(viewItem.title)"
          class="block w-full h-full"
        >
          <div
            class="text-left flex flex-wrap items-center lg:items-start"
          >
            <div class="w-full text-center flex items-center md:w-3/12 lg:w-2/12">
              <div class="mx-auto">
                <div
                  class="text-center md:text-left relative w-32 h-32 rounded-full border-4 border-primary overflow-hidden"
                >
                  <detail-image
                    v-if="getImageSrc(viewItem)"
                    :headline="getTitle(viewItem.title)"
                    :height="'h-32'"
                    :image="getImageSrc(viewItem)"
                  />
                  <fallback-image
                    v-else
                    :headline="getTitle(viewItem.title)"
                    :height="'h-32'"
                  />
                </div>
              </div>
            </div>
            <div class="md:w-9/12 lg:w-10/12 mt-5 lg:mt-0">
              <div class="py-5">
                <h4 class="text-left card-title text-xl font-semibold">
                  {{ getTitle(viewItem.title) }}
                </h4>
                <div class="justify-center md:justify-start text-dark mx-auto py-5 hidden lg:flex lg:flex-wrap">
                  <div class="tags-wrapper flex items-center text-sm lg:text-base">
                    <p
                      v-if="viewItem.category"
                      class="flex flex-wrap"
                    >
                      <span class="mr-4 ml-5 text-primary"><icon :icon-string="'IconTag'" /></span>
                      <span class="mr-4 text-dark"> {{ viewItem.category }}</span>
                    </p>
                    <section
                      class="flex flex-wrap"
                    >
                      <p
                        v-for="(tag, tagIndex) in viewItem.tags ?? []"
                        :key="tagIndex"
                        class="flex flex-wrap"
                      >
                        <span class="mr-4 ml-5 text-primary"><icon :icon-string="'IconTag'" /></span>
                        <span class="mr-4 text-dark">{{ tag }}</span>
                      </p>
                    </section>
                  </div>
                </div>
                <div class="bg-dark text-white rounded-md flex flex-wrap px-3 py-3">
                  <p v-if="viewItem.teaser">
                    <span
                      v-html="truncate(viewItem.teaser, 255, '...')"
                    />
                  </p>
                  <p v-if="viewItem.description">
                    <span
                      v-html="truncate(viewItem.description, 255, '...')"
                    />
                  </p>
                  <p v-if="viewItem.information">
                    <span
                      v-html="truncate(viewItem.information, 255, '...')"
                    />
                  </p>
                </div>
              </div>
            </div>
          </div>
        </a>
      </article>
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
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);

export default {
  name: "StandardListItem",
  components: {
    Icon,
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
        ApiService.get(this.getCall, this.slug, false).then(result => {
          this.viewItem = result;
          this.isLoading = false;
        });
      }
    },
    getClass() {
      return this.class;
    },
    getHref() {
      return this.slug;
    },
    getTitle(text) {
      return TextService.getOutput(text, 255);
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
    truncate: function (text, length, suffix) {
      return TextService.truncate(text, length, suffix);
    },
    getImageSrc(item) {
      try {
        const image = JSON.parse(item.image) ?? item.image;
        return ImageService.getImagePathFromMedia(image.media);
      } catch (e) {
        return null;
      }
    }
  }
}
</script>

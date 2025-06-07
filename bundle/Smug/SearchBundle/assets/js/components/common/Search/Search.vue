<template>
  <section
    class="fixed inset-0 z-90 h-100vh overflow-auto font-nunito"
  >
    <div class="inset-0 fixed h-100vh overlay overlay-post transition-all   bg-white z-10" />
    <div class="container px-4 lg:px-8 py-8 sm:py-12 z-20 relative mx-auto">
      <button
        :aria-label="$t('CLOSE')"
        class="search-btn flex mb-4 sm:mb-8 items-center text-primary hover:text-dark font-medium text-sm"
        @click="toggleButton()"
      >
        <span class="mr-4"><icon-left /></span>
        <span class="font-sans font-bold uppercase text-xs sm:text-sm">{{ $t("BACK") }}</span>
      </button>
      <h3 class="text-black transition-colors font-libre">
        {{ $t("SEARCH_HEADLINE") }}
      </h3>
      <p class="text-dark font-medium text-sm">
        {{ $t("SEARCH_TEXT") }}
      </p>
      <div class="relative">
        <input
          id="search-input"
          ref="searchValueInput"
          v-model="searchTerm"
          autocomplete="off"
          class="w-full text-dark my-6 input-lg" 
          :placeholder="$t('SEARCH')"
          @input="searchit"
        >
        <span
          id="search-loading"
          class="text-primary animate-spin fill-current feedback feedback-loading absolute right-8 top-11 mt-0.5 hidden"
        ><icon-loading /></span>
      </div>
      <div class="block flex items-start sm:items-center justify-between flex-wrap">
        <div class="block sm:flex sm:items-center sm:space-x-8">
          <span class="flex items-center">
            <code class="shadow-md border border-black border-opacity-5 outline-none mr-2 py-1.5 px-2 text-xs sm:text-sm leading-none font-bold rounded-md bg-white text-primary">ESC</code>
            <span class="text-gray-700 font-medium text-xs sm:text-sm">{{ $t('EXIT_SEARCH') }}</span>
          </span>
        </div>
      </div>
      <div
        v-if="results.length === 0 || searchTerm === ''"
        id="search-suggestions"
        class="mt-12 sm:mt-16"
      >
        <div class="grid space-y-10 md:space-y-0 gap-0 md:gap-10 grid-cols-1 md:grid-cols-2 pb-10">
          <div
            v-for="(marketingItem, marketingItemIndex) in windowData.marketingItems"
            :key="marketingItemIndex"
            class="h-searchTeaser bg-cover group overlay overflow-hidden transition-all relative flex-initial rounded-md h-full background overlay-black" 
            :style="{ 'background-image': getImageSrc(marketingItem)}"
          >
            <a 
              :href="marketingItem.linkTarget"
              target="_blank"
              :title="marketingItem.headline"
            >
              <div class="h-full w-full backdrop-filter backdrop-blur-sm transition-all opacity-0 group-hover:opacity-100" />
              <div class="px-8 py-4 absolute bottom-0 right-0 left-0 flex-auto overlay-content">
                <h6>
                  <p
                    class="line-clamp-3  font-bold mt-2 block text-xl text-white group-hover:text-primary"
                  >
                    {{ marketingItem.headline }}
                  </p>
                </h6>
                <p
                  class="text-white font-medium transition-all line-clamp-2 text-sm"
                >
                  {{ marketingItem.captionText }}
                </p>
              </div>
            </a>
          </div>
        </div>
        <div class="col-span-2 sm:col-span-1 md:col-span-2 lg:col-span-1">
          <div class="grid gap-5 lg:gap-10 grid-cols-1 lg:grid-cols-2 justify-items-stretch mt-8 lg:mt-12">
            <div
              v-for="(listItem, listItemIndex) in windowData.listItems"
              :key="listItemIndex"
            >
              <list-item :item="listItem" />
            </div>
          </div>
        </div>
        <div class="mt-12 sm:mt-0 lg:mt-0 col-span-2 sm:col-span-1 md:col-span-2 lg:col-span-1">
          <div class="grid gap-10 grid-cols-2 justify-items-stretch mt-8 lg:mt-12" />
        </div>
      </div>
      <div
        v-if="results.length > 0"
        id="search-results"
        class="mt-8 space-y-4"
      >
        <a
          v-if="results.length > 0"
          class="block text-sm text-corduroy-800 border-b border-corduroy-800 text-l mb-2 py-2 px-3 text-right"
          role="menuitem"
          :href="getCompleteLink()"
        >
          {{ $t('SEE_ALL') }}
        </a>
        <div
          v-for="result in results"
          :key="result.label"
          role="none"
        >
          <div
            v-if="result.results.length > 0 && (filterMode === '' || filterMode === result.detailMode)"
            class="p-4"
          >
            <h3 class="text-xl font-libre font-normal uppercase leading-normal mt-0 mb-2 text-corduroy-800 sm:truncate border-b-2 border-corduroy-800">
              {{ $t(result.label) }}
            </h3>
            <div
              v-for="(res, index) in result.results"
              :key="index"
              class="relative mx-3"
            >
              <a
                class="search-result block text-sm text-corduroy-800"
                role="menuitem"
                :href="getItemSlug(result.detailMode, res)"
              >
                <div class="p-2 flex items-center">
                  <div class="w-3/12 lg:w-1/12">
                    <div
                      class="transition-all mr-6 shadow-md group-hover:shadow-2xl w-20 h-20 rounded-full background"
                      style="flex: 0 0 auto;"
                      :style="{ 'background-image': 'url(' + getViewportImage(res.image) + ')'}"
                    />
                  </div>
                  <div class="py-2 pl-4 pr-2 w-9/12 lg:w-11/12">
                    <div class="mb-2">
                      <h6 class="search-result--label text-black line-clamp-1 font-sans transition-colors">{{ res.label }}</h6>
                      <div
                        v-if="res.additional"
                        class="pt-1.5 text-gray-700 font-medium text-sm"
                      >
                        <div
                          v-for="(additionalItem, additionalIndex) in res.additional.detail"
                          :key="additionalIndex"
                        >
                          <p
                            v-if="additionalItem.type === 'string'"
                            class="text-sm pb-1.5"
                          >
                            {{ additionalItem.value }} <span v-if="additionalItem.label">{{ $t(additionalItem.label) }}</span>
                          </p>
                          <div
                            v-if="additionalItem.type === 'array'"
                            class="flex flex-wrap text-sm pb-1.5"
                          >
                            <span
                              v-for="(item, additionalItemIndex) in additionalItem.value"
                              :key="additionalItemIndex"
                              class="mr-1.5"
                            >
                              <p
                                v-if="additionalItemIndex < 10"
                                class="text-s"
                              >{{ item }} <span v-if="additionalItemIndex > 0">|</span> </p>
                              <p
                                v-if="additionalItemIndex === 10"
                                class="text-s"
                              >...</p>
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </a>
            </div>
          </div>
          <div
            v-if="result.results.length === 0 && filterMode === result.detailMode"
            class="p-4"
          >
            <div
              id="search-empty"
              class="mt-12"
            >
              <h3 class="text-black font-libre transition-colors">
                {{ $t("NO_RESULTS_HEADLINE") }}
              </h3>
              <p class="mt-2 text-gray-700 font-medium text-sm">
                {{ $t("NO_RESULTS_TEXT") }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import ApiService from '../../../../../../AdministrationBundle/assets/js/services/api/api.service';
import { defineAsyncComponent } from "vue";
const IconLeft = defineAsyncComponent(() =>
  import("../../../../../../FrontendBundle/assets/js/icons/icons/IconLeft" /* webpackChunkName: "image-left" */)
);
const IconLoading = defineAsyncComponent(() =>
  import("../../../../../../FrontendBundle/assets/js/icons/icons/IconLoading" /* webpackChunkName: "image-loading" */)
);
const ListItem = defineAsyncComponent(() =>
  import("./additional/ListItem" /* webpackChunkName: "fe-search-window-list-item" */)
);

export default {
  name: "Search",
  components: {
    IconLeft,
    ListItem,
    IconLoading
  },
  model: {
    isFocused: false,
    showSearch: false,
    searchTerm: '',
    results: []
  },
  props: {
    windowData:{
      type: Object,
      required: true
    },
    showMobileSearch: {
      type: Boolean,
      required: true
    }
  },
  data() {
    return {
      timeout: null,
      isFocused: false,
      showSearch: false,
      searchTerm: '',
      filterMode: '',
      results: [],
      detailPages: []
    };
  },
  computed: {
    input: {
      get() {
        return this.searchTerm
      },
      set(val) {
        if (this.timeout) clearTimeout(this.timeout)
        this.timeout = setTimeout(() => {
          this.debouncedInput = val
        }, 300)
      }
    }
  },
  mounted() {
    window.addEventListener('keydown', (e) => {
      if (e.key == 'Escape') {
        this.toggleButton();
      }
    });
    this.detailPages = JSON.parse(this.windowData.detailPages);
  },
  methods: {
    handleFocus() {
      this.isFocused = true;
    },
    getItemSlug(type, result) {
      if (typeof this.detailPages[type] === 'undefined') {
        return result.slug;
      }

      const slug = (typeof result.slug !== 'undefined') ? result.slug : result.path;
      
      return this.detailPages[type] + slug;
    },
    setSearchFilter(event) {
      this.filterMode = event.target.value;
    },
    getTeaser(teaser) {
      return this.truncate(teaser, 144, '</p>');
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
    },
    showSearchFunction() {
      let windowWidth = window.innerWidth;

      if (windowWidth >= 1200) {
        this.showSearch = true;
        setTimeout(() => {
          this.$refs.searchValueInput.focus();
        }, 500);
        this.isFocused = true;
      } else {
        this.$emit('showMobileSearch', false);
      }
    },
    handleFocusOut(event) {
      if (event.relatedTarget === null || event.relatedTarget.tagName !== 'A') {
        // this.isFocused = false;
        this.searchTerm = '';
      }
    },
    getCompleteLink() {
      return this.windowData.searchDetailLink + '?searchTerm=' + this.searchTerm;
    },
    getViewportImage(image) {
      if (this.deviceWidth > 480) {
        return this.getImageSrc(image, 'desktop');
      } else {
        return this.getImageSrc(image, 'mobile');
      }
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
    searchit(val, event) {
      this.isFocused = true;
      if (this.searchTerm.length === 0) {
        this.results = [];
      }
      if (this.searchTerm.length > 2) {
        if (this.timeout) clearTimeout(this.timeout)
        this.timeout = setTimeout(() => {
          ApiService.post('/fe/api/search', {searchTerm: this.searchTerm}, false)
            .then(result => {
              this.results = result.results;
            });
        }, 400);
      }
    },
    toggleButton() {
      this.$emit('showSearch', false);
    }
  }
}
</script>
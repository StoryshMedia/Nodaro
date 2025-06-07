<template>
  <div
    v-if="results.length > 0"
    id="searchStart"
    class="container mx-auto mt-12 pb-32"
  >
    <div v-if="results.length > 0">
      <div v-if="showDetailResults === false">
        <div
          v-for="result in results"
          :key="result.label"
          class="p-2 md:p-4"
          role="none"
        >
          <div v-if="result.results.length > 0">
            <h2 class="text-lg lg:text-xl flex justify-between font-normal uppercase flex justify-between leading-normal mt-0 mb-2 text-kelp-700 sm:truncate border-b-2 border-kelp-700">
              <span
                class="block text-kelp-700 text-lg mb-2 py-2 px-3 text-right cursor-pointer"
              >
                {{ $t(result.label) }}
              </span>
              <span
                class="block text-kelp-700 text-lg mb-2 py-2 px-3 text-right cursor-pointer"
                role="menuitem"
                @click="showDetails(result.detailMode)"
              >
                {{ $t('DETAIL_SEE_ALL_' + result.detailMode) }}
              </span>
            </h2>
            <div
              v-for="(res, index) in result.results"
              :key="res.label"
              class="relative mx-3"
            >
              <div
                v-if="index === 3"
                class="grid grid-cols-2 lg:grid-cols-4 cursor-pointer py-4"
              >
                <div
                  v-for="marketingRes in result.marketing"
                  :key="marketingRes.label"
                  class="my-3"
                >
                  <a
                    class="block text-sm text-kelp-700"
                    role="menuitem"
                    :href="marketingRes.path"
                  >
                    <div class="p-2">
                      <div class="rounded overflow-hidden">
                        <detail-image
                          :image="marketingRes.image"
                          :headline="marketingRes.label"
                          :height="''"
                        />
                        <div class="p-2">
                          <div class="lg:text-xl mb-2">{{ marketingRes.label }}</div>
                        </div>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
              <a
                class="block text-sm text-kelp-700 border-b border-kelp-700"
                role="menuitem"
                :href="res.path"
              >
                <div class="py-2 md:px-2 flex flex-wrap">
                  <div class="w-1/3 md:w-1/12">
                    <search-image
                      :image="res.image"
                      :headline="res.label"
                    />
                  </div>
                  <div class="py-2 pl-4 pr-2 w-2/3 md:w-11/12">
                    <div class="text-xl mb-2">
                      <p>{{ res.label }}</p>
                      <div
                        v-if="res.additional"
                        class="hidden md:block pt-1.5"
                      >
                        <div
                          v-for="additionalItem in res.additional.detail"
                          :key="additionalItem.label"
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
        </div>
      </div>
      <div v-else>
        <div v-if="showLoading === false">
          <div class="fixed top-80 z-100 bg-dark text-white   left-0 h-12 w-12 md:h-16 md:w-16 hover:text-primary">
            <a
              :href="''"
              alt="Zurück zur Übersicht"
              class="flex justify-center items-center text-center h-full w-full"
              @click="showDetailResults = false"
            >
              <icon-double-left :size="'h-6 w-6 md:h-8 md:w-8'" />
            </a>
          </div>
          <h3 class="text-xl flex justify-between font-normal uppercase leading-normal mt-0 mb-2 text-kelp-700 sm:truncate border-b-2 border-kelp-700">
            <span class="block text-xl text-kelp-700 text-l mb-2 py-2 px-3 text-right">
              {{ $t(detailMode) }}
            </span>
          </h3>
          <direct-paginated-list
            :search-term="getSearchTerm()"
            :mode="getDetailMode()"
            :slug-mode="getSlugMode()"
            :show-padding-top="false"
            :big-tiles="getBigTiles()"
            :is-search="true"
            :show-filter-selection="false"
            :call="'search/detail'"
            :model="getSlugMode()"
          />
        </div>
        <div v-else>
          <loading />
        </div>
      </div>
    </div>
    <div v-else>
      <loading />
    </div>
  </div>
</template>


<script>

import { defineAsyncComponent } from "vue";
import axios from "axios";
const SearchImage = defineAsyncComponent(() =>
  import("../../../../FrontendBundle/assets/js/components/common/Image/SearchImage" /* webpackChunkName: "search-image" */)
);
const DetailImage = defineAsyncComponent(() =>
  import("../../../../FrontendBundle/assets/js/components/common/Image/DetailImage" /* webpackChunkName: "detail-image" */)
);
const Loading = defineAsyncComponent(() =>
  import("../../../../FrontendBundle/assets/js/components/common/Content/Loading" /* webpackChunkName: "loading" */)
);
const DirectPaginatedList = defineAsyncComponent(() =>
  import("../../../../FrontendBundle/assets/js/components/common/ItemList/DirectPaginatedList" /* webpackChunkName: "direct-paginated-list" */)
);
const IconDoubleLeft = defineAsyncComponent(() =>
  import("../../../../FrontendBundle/assets/js/icons/icons/IconDoubleLeft" /* webpackChunkName: "icon-double-left" */)
);

export default {
  name: "StoryList",
  components: {
    DirectPaginatedList,
    Loading,
    DetailImage,
    IconDoubleLeft,
    SearchImage
  },
  model: {
    results: [],
    detailResults: [],
    showDetailResults: false,
    showLoading: false
  },
  data() {
    return {
      props: '',
      results: [],
      detailResults: [],
      detailMode: '',
      showDetailResults: false,
      showLoading: false,
    };
  },
  mounted() {
    this.props = JSON.stringify({
      searchTerm: this.getSearchTerm(),
      mode: this.getDetailMode(),
      slugMode: this.getSlugMode(),
      showPaddingTop: false,
      call: 'search/detail',
      model: 'results'
    });
    axios.post(process.env.apiURL + '/fe/api/search/complete', {searchTerm: this.getSearchTerm()})
      .then(response => {
        this.results = response.data;
      })
      .catch(function (error) {
      })
      .then(function () {
      });
  },
  methods: {
    getDomain() {
      return process.env.apiURL
    },
    getSearchTerm() {
      let urlParams = new URLSearchParams(window.location.search);

      return urlParams.get('searchTerm');
    },
    getDetailMode() {
      if (this.detailMode === 'author') {
        return 'authors';
      } else {
        return this.detailMode;
      }
    },
    getBigTiles() {
      if (this.detailMode === 'author') {
        return true;
      } else {
        return false;
      }
    },
    getSlugMode() {
      switch (this.detailMode) {
      case 'story':
        return 'stories'
      case 'publication':
        window.localStorage.setItem('filter-page-publication', 1)
        return 'publications'
      case 'author':
        window.localStorage.setItem('filter-page-authors', 1)
        return 'authors'
      case 'genre':
        return 'genres'
      default:
        return this.detailMode
      }
    },
    showDetails(mode) {
      this.detailMode = mode;
      this.showDetailResults = true;
      const el = document.getElementById("searchStart");
      if (el) {
        el.scrollIntoView({behavior: 'smooth'});
      }
    }
  },
}
</script>
<template>
  <div
    id="paginationListStart"
    class="mx-auto px-4 lg:pb-24 min-h-56"
    :class="getPaddingTop()"
  >
    <div
      v-if="showTileFilters === true"
    >
      <h4 class="text-2xl text-left ml-5 font-semibold text-kelp-800 pb-5">
        {{ $t('CATEGORY_FILTER_HEADLINE') }}
      </h4>
        
      <div
        v-if="categoriesRaw.length > 0"
        class="container grid gap-0 md:gap-5 lg:gap-10 grid-cols-12 mb-24 md:mb-12 lg:mb-0"
      >
        <div 
          v-for="(category, index) in categoriesRaw"
          :key="index"
          class="h-56 w-full col-span-12 md:col-span-6 lg:col-span-4 rounded-md cursor-pointer"
          :class="{'border-primary border-4': category.id === selectedCategoryId}"
          @click="setCategoryFilter(category)"
        >
          <article
            data-aos="fade-zoom-in"
            class="flex w-full h-full relative flex-col post-card rounded-md shadow-md hover:shadow-2xl"
          >
            <div
              class="group lazy overlay overflow-hidden transition-all relative flex-initial rounded-md h-full background overlay-black" 
              :data-bg="getBackground(category)"
              :data-bg-hidpi="getBackground(category)"
            >
              <div class="h-full w-full backdrop-filter backdrop-blur-sm transition-all opacity-0 group-hover:opacity-100" />
              <div class="px-8 py-4 bottom-0 absolute right-0 left-0 flex-auto overlay-content">
                <h6>
                  <p
                    class="line-clamp-3  font-bold mt-2 block text-2xl text-white hover:text-primary"
                  >
                    {{ category.title }}
                  </p>
                </h6>
              </div>
            </div>
          </article>
        </div>
      </div>
    </div>
    <div class="flex flex-wrap lg:mb-12">
      <div
        v-if="showFilters === true"
        class="w-full lg:w-1/4 md:px-5 py-10 lg:py-0"
      >
        <h2
          class="border-b-2 border-gray-800 text-xl font-normal leading-normal mt-2 mb-2 pl-2 text-gray-800 px-4"
          :class="getPaddingTop()"
        >
          {{ $t('LIST_FILTERS') }}
        </h2>
        <div
          v-if="typeof listFilters.filters !== 'undefined'"
        >
          <div
            v-for="listFilter in listFilters.filters"
            :key="listFilter.title"
            class="mx-1"
          >
            <div v-if="listFilter.type === 'subSelect'">
              <h3
                class="text-base font-normal leading-normal mt-2 mb-2 pl-2 text-gray-800"
              >
                {{ $t(listFilter.title) }}
              </h3>
              <div class="relative w-full mb-3 py-3">
                <select-box
                  :items="listFilter.filters"
                  :option-label-identifier="'label'"
                  :clear-on-select="true"
                  @option-selected="setFilter($event, listFilter.mode, 1)"
                />
              </div>

              <div v-if="selectedFilters[listFilter.mode]">
                <div
                  v-for="selectedFilter in selectedFilters[listFilter.mode]"
                  :key="selectedFilter.id"
                >
                  <div class="flex flex-wrap justify-between rounded-full my-3 px-5 bg-primary text-white">
                    <h4
                      class="text-base font-normal leading-normal mt-2 mb-2 pl-2 bg-primary text-white"
                    >
                      {{ truncate(selectedFilter.title, 25, '...') }}
                    </h4>
                    <span
                      class="mt-2 cursor-pointer"
                      @click="removeFilter(selectedFilter, listFilter.mode)"
                    >
                      <icon-close />
                    </span>
                  </div>

                  <div
                    v-if="selectedFilter.children && selectedFilter.children.length > 0"
                    class="relative w-full mb-3 py-3"
                  >
                    <select-box
                      :items="selectedFilter.children"
                      :option-label-identifier="'label'"
                      :clear-on-select="true"
                      @option-selected="setFilter($event, listFilter.mode, 0)"
                    />
                  </div>
                  <div
                    v-else
                    class="bg-primary text-white px-5 py-1.5 my-5 rounded-full"
                  >
                    {{ $t('NO_MORE_SUB_FILTERS') }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div
          v-else
          class="border border-blue-300 shadow rounded-md p-4 max-w-sm w-full mx-auto"
        >
          <app-loading />
        </div>
        <div
          class="mx-1"
        >
          <h3
            class="text-base font-normal leading-normal mt-2 mb-2 pl-2 text-dark"
          >
            {{ $t('SORTING') }}
          </h3>

          <div class="relative w-full mb-3 py-3">
            <select-box
              :items="sortings"
              :option-label-identifier="'label'"
              :translate-label="true"
              @option-selected="setSorting($event)"
            />
          </div>

          <h3
            v-if="selectedSorting !== ''"
            class="text-base font-normal leading-normal mt-2 mb-2 pl-2 text-dark"
          >
            {{ $t('SORT_DIRECTION') }}
          </h3>

          <div
            v-if="selectedSorting !== ''"
            class="relative w-full mb-3 py-3"
          >
            <select-box
              :items="sortDirections"
              :option-label-identifier="'label'"
              :translate-label="true"
              @option-selected="setSortingDirection($event)"
            />
          </div>
        </div>
      </div>
      <div
        class="w-full"
        :class="getFilterClass()"
      >
        <div
          class="flex flex-wrap"
          :class="getPaddingTop()"
        >
          <div
            v-if="showAlphabetical === true"
            class="w-full"
          >
            <div class="hidden lg:block border-b-2 border-gray-800 mb-5">
              <div class="flex flex-wrap justify-between pb-3">
                <p
                  v-for="letter in alphabet"
                  :key="letter"
                  class="cursor-pointer"
                  @click="setAlphabeticalFilter(letter)"
                >
                  {{ $t(letter) }}
                </p>
              </div>
            </div>
            <div class="block lg:hidden mb-5 mx-1">
              <h3
                class="text-base font-normal leading-normal mt-2 mb-2 pl-2 text-dark"
              >
                {{ $t('ALPHABETIC') }}
              </h3>

              <select
                class="px-2 py-4 placeholder-kelp-800 border-2 border-kelp-800 placeholder-opacity-100 text-dark bg-white text-sm focus:outline-none w-full"
                @change="setMobileAlphabeticalFilter($event)"
              >
                <option
                  v-for="letter in alphabet"
                  :key="letter"
                  :value="letter"
                >
                  {{ $t(letter) }}
                </option>
              </select>
            </div>
          </div>
          <div
            v-if="showLoading === false"
            class="w-full relative"
          >
            <div
              v-if="showLoadingOverlay === true"
              class="paginated--list-overlay"
            >
              <div class="text center mt-96">
                <div class="flex items-center justify-center h-full">
                  <div class="relative">
                    <div class="h-24 w-24 rounded-full border-t-8 border-b-8 border-bright" />
                    <div class="absolute top-0 left-0 h-24 w-24 rounded-full border-t-8 border-b-8 border-primary animate-spin" />
                  </div>
                </div>
              </div>
            </div>
            <div v-if="showList === false">
              <div
                v-if="bigTiles === false"
                class="grid gap-5 lg:gap-10 grid-cols-12 lg:mt-12 xl:mt-24"
              >
                <div
                  v-for="item in listItems"
                  :key="item.label"
                  class="h-100"
                  :class="getGridClass()"
                >
                  <api-card
                    :slug="item.slug"
                    :item="item"
                    :class="'h-100'"
                    :get-call="getDetailGetCall()"
                  />
                </div>
              </div>

              <div
                v-else
              >
                <div
                  v-if="model === 'authors'"
                  class="container grid space-y-10 md:space-y-0 gap-0 lg:gap-10 grid-cols-12 justify-items-stretch mt-12"
                >
                  <div
                    v-for="(item, index) in listItems"
                    :key="index"
                    class="col-span-12 md:col-span-6 lg:col-span-6 xl:col-span-4 h-72 lg:h-96 xl:h-72"
                  >
                    <author-api-card
                      :slug="item.slug"
                      :mode="getMode()"
                      :get-call="getDetailGetCall()"
                    />
                  </div>
                </div>
                <div
                  v-else
                  class="container flex flex-wrap gap-y-3"
                >
                  <div
                    v-for="(item, index) in listItems"
                    :key="item.label"
                    class="relative px-2 post-loop-wrapper h-72 h-extra-big-tile"
                    :class="(index === 0 || index === 1 || index === 5 || index === 6 || index === 10 || index === 11) ? 'w-full md:w-1/2' : 'w-full md:w-1/2 lg:w-1/3'"
                  >
                    <api-card
                      :slug="item.slug"
                      :item="item"
                      :class="'h-72 h-extra-big-tile'"
                      :get-call="getDetailGetCall()"
                    />
                  </div>
                </div>
              </div>
            </div>
            <div v-else>
              <div
                v-for="(item, index) in listItems"
                :key="index"
                class="relative"
              >
                <app-standard-list-item
                  :model="model"
                  :mode="getMode()"
                  :slug-mode="slugMode"
                  :get-data="true"
                  :loading="showLoading"
                  :show-filters="showFilters"
                  :additional-call="getAdditionalCall()"
                  :detail-get-call="getDetailGetCall()"
                  :item="item"
                />
              </div>
            </div>
            <div
              v-if="listItems && listItems.length === 0"
              class="w-full"
            >
              <div
                class="rounded text-dark px-4 py-3 mx-3 mt-4"
                role="alert"
              >
                <div class="flex">
                  <div class="py-1">
                    <svg
                      class="fill-current h-6 w-6 text-dark mr-4"
                      xmlns="http://www.w3.org/2000/svg"
                      viewBox="0 0 20 20"
                    ><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z" /></svg>
                  </div>
                  <div>
                    <p class="font-bold">
                      {{ $t('NO_ITEMS_HEADLINE') }}
                    </p>
                    <p class="text-sm">
                      {{ $t('NO_ITEMS_TEXT') }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div
            v-if="showLoading === true"
            class="w-full pb-10 lg:pb-0"
          >
            <div
              v-if="bigTiles === false"
              class="grid gap-5 lg:gap-10 grid-cols-12 lg:mt-12 xl:mt-24"
            >
              <div
                v-for="(n,index) in itemLimit"
                :key="index"
                :class="getGridClass()"
              >
                <loading-tile />
              </div>
            </div>

            <div
              v-else
            >
              <div
                v-if="model === 'authors'"
                class="container grid space-y-10 md:space-y-0 gap-0 lg:gap-10 grid-cols-12 justify-items-stretch mt-12"
              >
                <div
                  v-for="(n,index) in itemLimit"
                  :key="index"
                  class="col-span-12 md:col-span-6 lg:col-span-6 xl:col-span-4"
                >
                  <loading-tile :class="'h-72 lg:h-96 xl:h-72'" />
                </div>
              </div>
              <div
                v-else
                class="container flex flex-wrap"
              >
                <div
                  v-for="(n,index) in itemLimit"
                  :key="index"
                  class="relative p-3 post-loop-wrapper"
                  :class="(index === 0 || index === 1 || index === 5 || index === 6 || index === 10 || index === 11) ? 'w-full md:w-1/2' : 'w-full md:w-1/2 lg:w-1/3'"
                >
                  <loading-tile :class="'h-72 lg:h-96 xl:h-72'" />
                </div>
              </div>
            </div>
          </div>

          <div
            v-if="showLoading === false"
            class="w-full px-4"
          >
            <div class="flex flex-col items-center my-12">
              <div class="flex flex-wrap text-gray-700">
                <a
                  v-if="page > 1"
                  :href="getHref(page - 1)"
                  :title="$t('PREVIOUS_PAGE')"
                  class="h-12 w-12 mr-4 flex justify-center items-center rounded-full bg-primary cursor-pointer"
                  @click="setPage(page - 1)"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="100%"
                    height="100%"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="text-white feather feather-chevron-left w-4 h-4"
                  >
                    <polyline points="15 18 9 12 15 6" />
                  </svg>
                </a>
                <a
                  :href="getHref(pages.start)"
                  :title="$t('PAGE') + pages.start"
                  class="h-12 px-3 md:flex paginationLink justify-center items-center hidden cursor-pointer leading-5 transition duration-150 border-b-2 border-transparent ease-in hover:border-primary"
                  :class="{
                    'border-primary': page == pages.start
                  }"
                  @click="setPage(pages.start)"
                >
                  <span
                    class="paginationLink"
                  >
                    {{ pages.start }}
                  </span>
                </a>
                <div 
                  v-for="(paginationPage, index) in pages.preSteps"
                  :key="index"
                  class="md:flex"
                >
                  <p
                    v-if="index === 0"
                    class="md:flex paginationLink justify-center items-center"
                  >
                    ...
                  </p>
                  <a
                    :href="getHref(paginationPage)"
                    class="h-12 px-3 md:flex paginationLink justify-center items-center hidden cursor-pointer leading-5 transition duration-150 border-b-2 border-transparent ease-in hover:border-primary"
                    :class="{
                      'border-primary': paginationPage == page,
                      'last': (paginationPage === lastPage && page !== paginationPage && lastPage > 3),
                      'first': (paginationPage === 1 && lastPage > 3 && page > 3)
                    }"
                    @click="setPage(paginationPage)"
                  >
                    <span
                      class="paginationLink"
                    >
                      {{ paginationPage }}
                    </span>
                  </a>
                  <p class="md:flex paginationLink justify-center items-center">
                    ...
                  </p>
                </div>
                <a
                  v-for="(paginationPage) in pages.mainSteps"
                  :key="paginationPage"
                  :title="$t('PAGE') + paginationPage"
                  :href="getHref(paginationPage)"
                  class="h-12 px-3 md:flex paginationLink justify-center items-center hidden cursor-pointer leading-5 transition border-b-2 border-transparent ease-in hover:border-primary"
                  :class="{
                    'border-primary': paginationPage == page,
                    'last': (paginationPage === lastPage && page !== paginationPage && lastPage > 3),
                    'first': (paginationPage === 1 && lastPage > 3 && page > 3)
                  }"
                  @click="setPage(paginationPage)"
                >
                  <span
                    class="paginationLink"
                  >
                    {{ paginationPage }}
                  </span>
                </a>
                <div 
                  v-for="(paginationPage, index) in pages.postSteps"
                  :key="index"
                  class="md:flex"
                >
                  <p
                    v-if="index === 0"
                    class="md:flex paginationLink justify-center items-center"
                  >
                    ...
                  </p>
                  <a
                    :href="getHref(paginationPage)"
                    class="h-12 px-3 md:flex paginationLink justify-center items-center hidden cursor-pointer leading-5 transition duration-150 border-b-2 border-transparent ease-in hover:border-primary"
                    :class="{
                      'border-primary': paginationPage == page,
                      'last': (paginationPage === lastPage && page !== paginationPage && lastPage > 3),
                      'first': (paginationPage === 1 && lastPage > 3 && page > 3)
                    }"
                    @click="setPage(paginationPage)"
                  >
                    <span
                      class="paginationLink"
                    >
                      {{ paginationPage }}
                    </span>
                  </a>
                  <p class="md:flex paginationLink justify-center items-center">
                    ...
                  </p>
                </div>
                <a
                  v-if="lastPage !== pages.start"
                  :href="getHref(lastPage)"
                  :title="$t('PAGE') + lastPage"
                  class="h-12 px-3 md:flex paginationLink justify-center items-center hidden cursor-pointer leading-5 transition duration-150 border-b-2 border-transparent ease-in hover:border-primary"
                  :class="{
                    'border-primary': page == lastPage
                  }"
                  @click="setPage(lastPage)"
                >
                  <span
                    class="paginationLink"
                  >
                    {{ lastPage }}
                  </span>
                </a>
                <a
                  v-if="page < lastPage"
                  :href="getHref(+page + +1)"
                  :title="$t('NEXT_PAGE')"
                  class="h-12 w-12 ml-4 flex justify-center items-center rounded-full bg-primary cursor-pointer"
                  @click="setPage(+page + +1)"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="100%"
                    height="100%"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    class="text-white feather feather-chevron-right w-4 h-4"
                  >
                    <polyline points="9 18 15 12 9 6" />
                  </svg>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineAsyncComponent } from "vue";
import axios from "axios";
const ApiCard = defineAsyncComponent(() =>
  import("./ApiCard" /* webpackChunkName: "api-card" */)
);
const AppStandardListItem = defineAsyncComponent(() =>
  import("./StandardListItem" /* webpackChunkName: "standard-list-item" */)
);
const LoadingTile = defineAsyncComponent(() =>
  import("../Content/LoadingTile" /* webpackChunkName: "loading-tile" */)
);
const AuthorApiCard = defineAsyncComponent(() =>
  import("../../../../../../PublicationBundle/assets/js/components/common/ItemList/AuthorApiCard" /* webpackChunkName: "author-api-card" */)
);
const SelectBox = defineAsyncComponent(() =>
  import("../Input/SelectBox" /* webpackChunkName: "select-box" */)
);
const IconClose = defineAsyncComponent(() =>
  import("@core/js/icons/icons/IconClose" /* webpackChunkName: "icon-close" */)
);

export default {
  name: "PaginatedList",
  components: {
    AppStandardListItem,
    ApiCard,
    LoadingTile,
    AuthorApiCard,
    IconClose,
    SelectBox
  },
  inject: ['dataset'],
  props: {
    givenSelectedFilters: {
      type: Array,
      required: false,
      default: null
    }
  },
  data() {
    return {
      allSortings: [],
      allFilters: null,
      filter: null,
      scrollToStart: true,
      model: true,
      showPaddingTop: true,
      forceReload: false,
      showLoadingOverlay: false,
      showList: false,
      bigTiles: false,
      showAlphabetical: false,
      showFilterSelection: false,
      showTileFilters: false,
      slugMode: '',
      detailGetCall: '',
      filterMode: '',
      additionalCall: '',
      selectedCategoryId: '',
      searchTerm: '',
      mode: '',
      call: '',
      itemLimit: 18,
      listItems: [],
      pages: [],
      categories: [],
      categoriesRaw: [],
      lastPage: 1,
      currentIndex: -1,
      searchTitle: "",
      showFilters: false,
      isGenre: false,
      genreTitle: '',
      genreSlug: '',
      showLoading: true,
      isInitialLoaded: false,
      listFilters: [],
      alphabet: ['ALL', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'],
      alphabetical: '',
      sortings: [],
      selectedFilters: {},
      selectedSorting: 'c.slug',
      sortDirection: 'ASC',
      activeSortingDirection: null,
      activeSorting: null,
      sortDirections: [
        {
          label: 'ASC',
          value: 'ASC'
        },
        {
          label: 'DESC',
          value: 'DESC'
        }
      ],
      page: 1,
      count: 0,
      limit: this.itemLimit,
      pageSizes: [this.itemLimit, 36, 54]
    };
  },
  watch: {
    "$store.state.auth.token": {
      handler: function(nv) {
        if (this.isInitialLoaded === true) {
          this.retrieveItems();
        }
      },
      deep: true,
      immediate: true // provides initial (not changed yet) state
    },
    forceReload: {
      handler: function(nv) {
        if (this.isInitialLoaded === true && this.forceReload === true) {
          this.retrieveItems();
        }
      },
      deep: true,
      immediate: true // provides initial (not changed yet) state
    },
    givenSelectedFilters: {
      handler: function(nv) {
        if (typeof nv !== 'undefined' && nv !== null) {
          const keys = Object.keys(nv);
          if (keys.length > 0) {
            this.selectedFilters = [];
            const valueLength = keys.length - 1;
            let storageFilter = JSON.parse(window.localStorage.getItem('filter-' + this.filterMode));
            if (storageFilter === null) {
              storageFilter = {};
            }

            for (let count = 0; count <= valueLength; count++) {
              const value = nv[keys[count]];
              this.selectedFilters[value.mode] = value.value;
              if ( count === valueLength) {
                storageFilter.selectedFilters = this.selectedFilters;
                window.localStorage.setItem('filter-' + this.filterMode, JSON.stringify(storageFilter));
                this.retrieveItems();
              }
            }
          } else {
            let storageFilter = JSON.parse(window.localStorage.getItem('filter-' + this.filterMode));
            if (storageFilter === null) {
              storageFilter = {};
            }
            this.selectedFilters = [];
            storageFilter.selectedFilters = this.selectedFilters;
            window.localStorage.setItem('filter-' + this.filterMode, JSON.stringify(storageFilter));
            this.retrieveItems();
          }
        }
      },
      deep: true,
      immediate: true // provides initial (not changed yet) state
    },
  },
  async created() {
    await this.setProps();

    this.limit = this.itemLimit;
    this.showLoading = true;
    this.pageSizes = [
      this.itemLimit,
      this.itemLimit * 2,
      this.itemLimit * 3
    ];
    if (this.showFilterSelection === true) {
      this.sortings = this.allSortings;
      this.getFilters();
    }

    if (this.showTileFilters === true) {
      this.getCategories();
    }
    let urlItems = window.location.href.split('/');

    if (urlItems[urlItems.length - 1].includes('p-')) {
      this.setFilterPage(parseInt(urlItems[urlItems.length - 1].replace('p-', '')));
    }
    
    if (this.isGenre === true) {
      const item = JSON.parse(window.localStorage.getItem('selected-publication-genre'));
      if (typeof item === 'undefined' || item === null) {
        let slug = (urlItems[urlItems.length - 1].includes('p-')) ? urlItems[urlItems.length - 2] : urlItems[urlItems.length - 1];

        this.setFilter(
          {
            "mode":"genre",
            "slug":slug,
            "title":slug
          },
          'genre',
          0
        );
      } else {
        this.setFilter(item.value, item.mode, 0);
      }
    } else {
      const props = JSON.parse(this.dataset.props);
      if (typeof window.localStorage.getItem('filter-' + props.filterMode) !== 'undefined') {
        const item = JSON.parse(window.localStorage.getItem('filter-' + props.filterMode));
        if (item !== null && typeof item.selectedFilters !== 'undefined') {
          this.selectedFilters = item.selectedFilters;
        }
        this.retrieveItems(true);
      } else {
        this.retrieveItems(true);
      }
    }
  },
  methods: {
    getHref(paginationPage) {
      if (this.mode === 'publications') {
        if (window.location.href.includes('genres')) {
          let slug = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);

          if (slug.substring(0, 2) === 'p-') {
            let tempSlug = window.location.href.substring(0, window.location.href.lastIndexOf('/'));
            let slug = tempSlug.substring(tempSlug.lastIndexOf('/') + 1);
            return '/genres/' + slug + '/p-' + paginationPage;
          }
          return '/genres/' + slug + '/p-' + paginationPage;
        } else {
          return '/' + this.mode + '/p-' + paginationPage;
        }
      } else if (this.mode === 'newRelease') {
        return '/neuerscheinungen/p-' + paginationPage;
      } else if (this.mode === 'threads') {
        return '/community/forum/p-' + paginationPage;
      } else {
        return '/' + this.mode + '/p-' + paginationPage;
      }
    },
    setPage(paginationPage) {
      this.page = paginationPage;
      this.setFilterPage(this.page).then(set => {
        window.location.replace('/' + this.mode + '/p-' + paginationPage);
      });
    },
    setProps() {
      const props = JSON.parse(this.dataset.props);
      (props.itemLimit) ? this.itemLimit = props.itemLimit : 18;
      (props.allSortings) ? this.allSortings = props.allSortings : [];
      (props.allFilters) ? this.allFilters = props.allFilters : null;
      (props.filter) ? this.filter = props.filter : null;
      (props.model) ? this.model = props.model : true;
      (typeof props.selectedFilters !== 'undefined') ? this.selectedFilters = props.selectedFilters : {};
      (typeof props.isGenre !== 'undefined') ? this.isGenre = props.isGenre : false;
      if (this.isGenre === true) {
        let slug = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);

        if (slug.substring(0, 2) === 'p-') {
          let tempSlug = window.location.href.substring(0, window.location.href.lastIndexOf('/'));
          slug = tempSlug.substring(tempSlug.lastIndexOf('/') + 1);
          this.genreTitle = 'filter-page-genres-' + slug;
          this.genreSlug = slug;
        } else {
          this.genreTitle = 'filter-page-genres-' + slug;
          this.genreSlug = slug;
        }
      }
      (typeof props.showPaddingTop !== 'undefined') ? this.showPaddingTop = props.showPaddingTop : true;
      (props.forceReload) ? this.forceReload = props.forceReload : false;
      (props.showList) ? this.showList = props.showList : false;
      (props.bigTiles) ? this.bigTiles = props.bigTiles : false;
      (props.showAlphabetical) ? this.showAlphabetical = props.showAlphabetical : false;
      (props.showFilterSelection) ? this.showFilterSelection = props.showFilterSelection : false;
      (props.showTileFilters) ? this.showTileFilters = props.showTileFilters : false;
      (props.slugMode) ? this.slugMode = props.slugMode : '';
      (props.detailGetCall) ? this.detailGetCall = props.detailGetCall : '';
      (props.filterMode) ? this.filterMode = props.filterMode : '';
      (props.additionalCall) ? this.additionalCall = props.additionalCall : '';
      (props.searchTerm) ? this.searchTerm = props.searchTerm : '';
      (props.mode) ? this.mode = props.mode : '';
      (props.call) ? this.call = props.call : '';
    },
    getDetailGetCall() {
      return this.detailGetCall;
    },
    getAdditionalCall() {
      return this.additionalCall;
    },
    increasePage() {
      this.page++;
      this.retrieveItems();
    },
    decreasePage() {
      this.page--;
      this.retrieveItems();
    },
    setCategoryFilter(category) {
      if (this.selectedCategoryId === category.id) {
        this.selectedCategoryId = '';
        delete this.selectedFilters['category'];
        this.retrieveItems();
      } else {
        this.selectedCategoryId = category.id;
        this.selectedFilters['category'] =
          {
            mode: 'category',
            value: category
          };
        this.retrieveItems();
      }
    },
    setFilter(listFilter, mode, level) {
      this.showLoadingOverlay = true;
      if (typeof this.selectedFilters[mode] === 'undefined') {
        this.selectedFilters[mode] = [];
      }
      this.getFilterPage().then(page => {
        this.setFilterPage(
          page
        ).then(set => {
          let storageFilter = JSON.parse(window.localStorage.getItem('filter-' + this.filterMode));
          if (storageFilter === null) {
            storageFilter = {};
          }
          if (this.isGenre === true) {
            this.retrieveItems();
            this.showLoadingOverlay = false;
          } else {
            axios.get(process.env.apiURL + '/fe/api/filter/' + this.filterMode + '/' + mode + '/children/' + listFilter.slug)
              .then((response) => {
                let childCount = 0;
                const childLength = response.data.children.length;
                const data = [];

                if (childLength > 0) {
                  for (childCount; childCount <= childLength - 1; childCount++) {
                    data.push({
                      label: response.data.children[childCount].title,
                      value: response.data.children[childCount]
                    });

                    if (childCount === childLength - 1) {
                      listFilter.children = data;
                      listFilter.id = response.data.main.id;
                      if (level === 1) {
                        this.selectedFilters[mode][0] = listFilter;
                      } else {
                        try {
                          if (this.isGenre === false) {
                            this.selectedFilters[mode].push(listFilter);
                          }
                        } catch (error) {
                          console.error(error);
                        }
                      }

                      storageFilter.selectedFilters = this.selectedFilters;

                      if (this.filterMode !== '' && this.isGenre === false) {
                        window.localStorage.setItem('filter-' + this.filterMode, JSON.stringify(storageFilter));
                      }

                      this.retrieveItems();
                      this.showLoadingOverlay = false;
                    }
                  }
                } else {
                  listFilter.children = response.data;
                  listFilter.id = response.data.main.id;

                  if (level === 1) {
                    this.selectedFilters[mode][0] = listFilter;
                  } else {
                    try {
                      if (this.isGenre === false) {
                        this.selectedFilters[mode].push(listFilter);
                      }
                    } catch (error) {
                      console.error(error);
                    }
                  }
                
                  storageFilter.selectedFilters = this.selectedFilters;

                  if (this.filterMode !== '' && this.isGenre === false) {
                    window.localStorage.setItem('filter-' + this.filterMode, JSON.stringify(storageFilter));
                  }

                  this.retrieveItems();
                  this.showLoadingOverlay = false;
                }
              })
              .catch((e) => {
              });
          }
        });
      })
    },
    getFilterClass() {
      return (this.showFilters === true) ? 'lg:w-3/4' : '';
    },
    truncate: function (text, length, suffix) {
      if (text.length > length) {
        return text.substring(0, length) + suffix;
      } else {
        return text;
      }
    },
    getGridClass() {
      return (this.showFilters === true) ? 'col-start-3 md:col-start-0 col-span-8 md:col-span-6 lg:col-span-6 xl:col-span-4 2xl:col-span-3' : 'col-start-3 md:col-start-0 col-span-8 md:col-span-6 lg:col-span-6 xl:col-span-3';
    },
    setSorting(sorting) {
      this.activeSorting = sorting;
      this.selectedSorting = sorting;
  
      this.retrieveItems();
    },
    setSortingDirection(direction) {
      this.activeSortingDirection = direction;
      this.sortDirection = direction;
      this.retrieveItems();
    },
    clearFilter(event) {
    },
    setAlphabeticalFilter(letter) {
      this.alphabetical = (letter === 'ALL') ? '' : letter;
      const mode = (this.isGenre === true) ? this.genreSlug : this.mode;
      window.localStorage.setItem('filter-alphabetical-' + mode, this.alphabetical);
      this.setFilterPage(1).then(set => {
        window.location.replace(this.getHref(1));
      });
    },
    setMobileAlphabeticalFilter(letter) {
      this.alphabetical = (letter.target.value === 'ALL') ? '' : letter.target.value;
      window.localStorage.setItem('filter-alphabetical-' + (this.genreSlug === true) ? this.genreSlug : this.mode, this.alphabetical);

      this.setFilterPage(1).then(set => {
        this.retrieveItems();
      });
    },
    async setFilterPage(page) {
      if (this.mode === 'publications') {
        if (this.isGenre === true) {
          window.localStorage.setItem(this.genreTitle, page);
          return true;
        } else {
          window.localStorage.setItem('filter-page-' + this.mode, page);
          return true;
        }
      } else {
        window.localStorage.setItem('filter-page-' + this.mode, page);
        return true;
      }
    },
    removeFilter(listFilter, mode) {
      this.setFilterPage(1).then(set => {
        const length = this.selectedFilters[mode].length - 1;
        let i = 0;

        let storageFilter = (this.filterMode !== '') ? JSON.parse(window.localStorage.getItem('filter-' + this.filterMode)) : {};

        if (storageFilter === null) {
          storageFilter = {};
        }

        for (i; i <= length; i++) {
          const value = this.selectedFilters[mode][i];

          if (typeof value !== 'undefined') {
            if (value.id === listFilter.id || value.parentId === listFilter.id) {
              if (i === 0) {
                this.selectedFilters[mode] = [];
                storageFilter.selectedFilters = {};

                if (this.isGenre === false) {
                  window.localStorage.setItem('filter-' + this.filterMode, JSON.stringify(storageFilter));
                }
                window.location.replace(this.getHref(1));
              } else {
                this.selectedFilters[mode].splice(i, length);
                storageFilter.selectedFilters = this.selectedFilters;

                if (this.isGenre === false) {
                  window.localStorage.setItem('filter-' + this.filterMode, JSON.stringify(storageFilter));
                }
                window.location.replace(this.getHref(1));
              }
            }
          } else {
            window.location.replace(this.getHref(1));
          }
          
        }
      });
    },
    getCategories() {
      axios.get(process.env.apiURL + '/fe/api/blog/categories')
        .then((res) => {
          let count = 0;
          const data = [], length = res.data.length;
          this.categoriesRaw = res.data;

          for (count; count <= length - 1; count++) {
            data.push({
              label: res.data[count].title,
              value: res.data[count]
            });

            if (count === length - 1) {
              this.categories = data;
            }
          }
        })
        .catch((error) => {
        }).finally(() => {
        });
    },
    getBackground(category) {
      return process.env.apiURL + '/site/img/' + category.slug + '.webp';
    },
    getFilters() {
      this.showFilters = true;
      axios.get(process.env.apiURL + '/fe/api/filter/' + this.filterMode)
        .then((response) => {
          this.listFilters = response.data;
          let count = 0;
          const length = this.listFilters.filters.length;

          for (count; count <= length - 1; count++) {
            let filterCount = 0;
            const filterLength = this.listFilters.filters[count].data.length;
            const data = [];

            for (filterCount; filterCount <= filterLength - 1; filterCount++) {
              data.push({
                label: this.listFilters.filters[count].data[filterCount].title,
                value: this.listFilters.filters[count].data[filterCount]
              });

              if (filterCount === filterLength - 1) {
                this.listFilters.filters[count].filters = data;
              }
            }
          }
        })
        .catch((e) => {
        });
    },
    async getRequestParams(searchTitle, page, pageSize, init = false) {
      const filterPage = await this.getFilterPage();
      this.page = filterPage;
      let params = {};

      if (init === true && (this.filterMode !== '' && window.localStorage.getItem('filter-' + this.filterMode) !== null)) {
        const storageFilter = JSON.parse(window.localStorage.getItem('filter-' + this.filterMode));

        if (typeof storageFilter.selectedSorting !== 'undefined') {
          this.selectedSorting = storageFilter.selectedSorting;
        }
        if (typeof storageFilter.sortDirection !== 'undefined') {
          this.sortDirection = storageFilter.sortDirection;
        }
        if (typeof storageFilter.selectedFilters !== 'undefined' && init === false) {
          this.selectedFilters = storageFilter.selectedFilters;
        }
      }

      if (searchTitle) {
        params["title"] = searchTitle;
      }

      if (filterPage) {
        params["page"] = filterPage;
      }

      const mode = (this.isGenre === true) ? this.genreSlug : this.mode;
      window.localStorage.getItem('filter-alphabetical-' + mode, this.alphabetical);

      params["alphabetical"] = (window.localStorage.getItem('filter-alphabetical-' + mode) !== null && window.localStorage.getItem('filter-alphabetical-' + mode) !== 'null') ? window.localStorage.getItem('filter-alphabetical-' + mode) : this.alphabetical;

      if (params["alphabetical"] === 'ALL') {
        params["alphabetical"] = '';
      }

      if (pageSize) {
        params["limit"] = pageSize;
      }

      if (this.filter !== null) {
        params["filter"] = this.filter;
      }

      if (this.searchTerm !== '') {
        params["search"] = this.searchTerm;
      }

      if (this.mode !== '') {
        params["mode"] = this.mode;
      }

      if (this.allSortings !== null && Object.keys(this.selectedSorting).length > 0) {
        params["allSortings"] = this.allSortings;
        params["sorting"] = this.selectedSorting;
        params["direction"] = this.sortDirection;
      }

      if (this.allFilters !== null) {
        params["allFilters"] = this.allFilters;

        const selectedFilterKeys = Object.keys(this.selectedFilters);
        if (selectedFilterKeys.length > 0) {
          const length = selectedFilterKeys.length - 1;
          let tempFilters = [];
          let i = 0;

          for (i; i <= length; i++) {
            const modeLength = this.selectedFilters[selectedFilterKeys[i]].length - 1;
            let j = 0;

            if (typeof this.selectedFilters[selectedFilterKeys[i]] === 'object') {
              if (this.allFilters.length > 1) {
                const allFilterLength = this.allFilters.length - 1;
                
                for (let allFilterCounter = 0; allFilterCounter <= allFilterLength; allFilterCounter++) {
                  if (this.allFilters[allFilterCounter].value === selectedFilterKeys[i]) {
                    const filter = this.allFilters[allFilterCounter];

                    filter.filterData.parameters[0].value = this.selectedFilters[selectedFilterKeys[i]].id;

                    if (selectedFilterKeys.length > 1) {
                      tempFilters.push(filter);

                      if (i === length) {
                        params['filter'] = tempFilters;
                      }
                    } else {
                      params['filter'] = filter;
                    }
                  }
                }
              } else {
                const filter = this.allFilters[i];

                if (this.selectedFilters[selectedFilterKeys[i]].length > 1) {
                  filter.filterData.parameters[0].value = this.selectedFilters[selectedFilterKeys[i]].slice(-1)[0].id;
                  if (selectedFilterKeys.length > 1) {
                    tempFilters.push(filter);

                    if (i === length) {
                      params['filter'] = tempFilters;
                    }
                  } else {
                    params['filter'] = filter;
                  }
                } else {
                  if (this.selectedFilters[selectedFilterKeys[i]].length > 0 ) {
                    filter.filterData.parameters[0].value = (typeof this.selectedFilters[selectedFilterKeys[i]][0].id !== 'undefined') ? this.selectedFilters[selectedFilterKeys[i]][0].id : this.selectedFilters[selectedFilterKeys[i]][0].slug;

                    if (selectedFilterKeys.length > 1) {
                      tempFilters.push(filter);

                      if (i === length) {
                        params['filter'] = tempFilters;
                      }
                    } else {
                      params['filter'] = filter;
                    }
                  } else {
                    if (typeof this.selectedFilters[selectedFilterKeys[i]].value !== 'undefined') {
                      if (typeof this.selectedFilters[selectedFilterKeys[i]].value.id !== 'undefined') {
                        filter.filterData.parameters[0].value = this.selectedFilters[selectedFilterKeys[i]].value.id;
                      } else {
                        filter.filterData = this.selectedFilters[selectedFilterKeys[i]].filterData;
                      }
                      params['filter'] = filter;
                    }
                  }
                }
              }
            } else {
              for (j; j <= modeLength; j++) {
                const allFilterLength = this.allFilters.length - 1;
                let k = 0;

                for (k; k <= allFilterLength; k++) {
                  if (this.selectedFilters[selectedFilterKeys[i]][j].mode === this.allFilters[k].value) {
                    const filter = this.allFilters[k];

                    filter.filterData.parameters[0].value = this.selectedFilters[selectedFilterKeys[i]][j].id;
                    params['filter'] = filter;
                  }
                }
              }
            }

            if (i === length) {
              return params;
            }
          }
        } else {
          return params;
        }
      } else {
        return params;
      }
    },
    async getFilterPage() {
      if (this.mode === 'publications') {
        if (window.location.href.includes('genres')) {
          let slug = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);

          if (slug.substring(0, 2) === 'p-') {
            let tempSlug = window.location.href.substring(0, window.location.href.lastIndexOf('/'));
            let slug = tempSlug.substring(tempSlug.lastIndexOf('/') + 1);
            return (window.localStorage.getItem('filter-page-genres-' + slug) !== null && window.localStorage.getItem('filter-page-genres-' + slug) !== 'null') ? window.localStorage.getItem('filter-page-genres-' + slug) : this.page;
          }

          return (window.localStorage.getItem('filter-page-genres-' + slug) !== null && window.localStorage.getItem('filter-page-genres-' + slug) !== 'null') ? window.localStorage.getItem('filter-page-genres-' + slug) : this.page;
        } else {
          return (window.localStorage.getItem('filter-page-' + this.mode) !== null && window.localStorage.getItem('filter-page-' + this.mode) !== 'null') ? window.localStorage.getItem('filter-page-' + this.mode) : this.page;
        }
      } else {
        return (window.localStorage.getItem('filter-page-' + this.mode) !== null && window.localStorage.getItem('filter-page-' + this.mode) !== 'null') ? window.localStorage.getItem('filter-page-' + this.mode) : this.page;
      }
    },
    async retrieveItems(init = false) {
      const params = await this.getRequestParams(
        this.searchTitle,
        this.page,
        (this.showFilterSelection === true) ? this.limit : this.itemLimit,
        init
      );
      if (this.$store.state.auth.token) {
        const config = {
          headers: { Authorization: `Bearer ${this.$store.state.auth.token}` }
        };
        this.showLoading = true;
        axios.post(process.env.apiURL + '/fe/api/' + this.call, params)
          .then((response) => {
            this.showLoading = false;
            this.isInitialLoaded = init;
            this.listItems = response.data[this.model];
            this.pages = response.data.pages;
            this.page = params.page;
            this.lastPage = (response.data.lastPage) ? response.data.lastPage : response.data.pages.end;
            this.count = this.listItems.length;
            this.$forceUpdate();
          })
          .catch((e) => {
          });
      } else {
        axios.post(process.env.apiURL + '/fe/api/' + this.call, params)
          .then((response) => {
            if (this.scrollToStart === true && init === false) {
              const el = document.getElementById("paginationListStart");
              if (el) {
                el.scrollIntoView({behavior: 'smooth'});
              }
            }
            this.showLoading = false;
            this.isInitialLoaded = init;
            this.listItems = response.data[this.model];
            this.pages = response.data.pages;
            this.page = params.page;
            this.lastPage = (response.data.lastPage) ? response.data.lastPage : response.data.pages.end;
            this.count = this.listItems.length;
            this.$forceUpdate();
          })
          .catch((e) => {
          });
      }
    },
    getPaddingTop() {
      return (this.showPaddingTop === true) ? 'lg:pt-24 ' : 'lg:pt-2';
    },
    getMode() {
      if (this.mode === 'newRelease') {
        return 'publications/';
      }
      return this.mode + '/';
    },
    handlePageChange(value) {
      this.page = value;
      this.retrieveItems();
    },
    handlePageSizeChange(event) {
      this.limit = event.target.value;
      this.page = 1;
      this.retrieveItems();
    },
  }
}
</script>

<style lang="scss">
.paginationLink.first::after {
  content:' ...'
}

.paginationLink.last::before {
  content:'... '
}
</style>

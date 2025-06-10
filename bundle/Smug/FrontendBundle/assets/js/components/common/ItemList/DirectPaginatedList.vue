<template>
  <div
    id="paginationListStart"
    class="mx-auto px-4 lg:pb-24"
    :class="getPaddingTop()"
  >
    <div class="flex flex-wrap mb-12">
      <div
        class="w-full"
        :class="getFilterClass()"
      >
        <div
          class="flex flex-wrap"
          :class="getPaddingTop()"
        >
          <div
            v-if="showLoading === false"
            class="w-full"
          >
            <div v-if="showList === false">
              <div
                v-if="bigTiles === false"
                class="grid"
                :class="getGridClass()"
              >
                <div
                  v-for="item in listItems"
                  :key="item.label"
                >
                  <api-card
                    :slug="item.slug"
                    :class="'h-100'"
                    :get-call="getDetailGetCall()"
                  />
                </div>
              </div>

              <div
                v-else
                class="container flex flex-wrap"
              >
                <div
                  v-for="(item, index) in listItems"
                  :key="item.label"
                  class="relative p-3 post-loop-wrapper"
                  :class="(index === 0 || index === 1 || index === 5 || index === 6 || index === 10 || index === 11) ? 'w-full md:w-1/2' : 'w-full md:w-1/2 lg:w-1/3'"
                >
                  <api-card
                    :slug="item.slug"
                    :get-call="getDetailGetCall()"
                  />
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
            <app-loading />
          </div>

          <div
            v-if="pages.length > 1 && showLoading === false"
            class="w-full px-4"
          >
            <div class="flex flex-col items-center my-12">
              <div class="flex text-gray-700">
                <div
                  v-if="page > 1"
                  class="h-12 w-12 mr-4 flex justify-center items-center rounded-full bg-primary cursor-pointer"
                  @click="decreasePage()"
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
                </div>
                <div
                  class="flex h-12 font-medium bg-white"
                  :class="((lastPage - page) > 3) ? 'rounded-l-full' : 'rounded-full'"
                >
                  <div
                    v-for="(paginationPage, index) in pages"
                    :key="paginationPage"
                    class="md:flex paginationLink justify-center bg-white items-center hidden cursor-pointer leading-5 transition duration-150 ease-in rounded-full hover:bg-primary hover:text-white"
                    :class="{
                      'w-12': index < pages.length && paginationPage < 100,
                      'px-3': paginationPage > 100,
                      'bg-primary': paginationPage == page,
                      'text-white': paginationPage == page,
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
                  </div>
                </div>
                <div class="flex h-12 font-medium rounded-r-full bg-white">
                  <div
                    v-if="(lastPage - page) > 3"
                    class="md:flex w-24 last paginationLink justify-center bg-white items-center hidden cursor-pointer leading-5 transition duration-150 ease-in rounded-full hover:bg-primary hover:text-white"
                    @click="setPage(lastPage)"
                  >
                    <span
                      class="paginationLink"
                    >
                      {{ lastPage }}
                    </span>
                  </div>
                </div>
                <div
                  v-if="page < pages.length"
                  class="h-12 w-12 ml-4 flex justify-center items-center rounded-full bg-primary cursor-pointer"
                  @click="increasePage()"
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
                </div>
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
const AppLoading = defineAsyncComponent(() =>
  import("../Content/Loading" /* webpackChunkName: "loading" */)
);

export default {
  name: "DirectPaginatedList",
  components: {
    AppStandardListItem,
    ApiCard,
    AppLoading
  },
  model: {
    listItems: [],
    pages: [],
    lastPage: 1,
    currentIndex: -1,
    searchTitle: "",
    page: 1,
    count: 0,
    limit: 18,
    pageSizes: [18, 36, 54]
  },
  props: {
    call: {
      type: String,
      required: true
    },
    model: {
      type: String,
      required: true
    },
    isSearch: {
      type: Boolean,
      required: false,
      default: false
    },
    showFilterSelection: {
      type: Boolean,
      required: false,
      default: false
    },
    showAlphabetical: {
      type: Boolean,
      required: false,
      default: false
    },
    bigTiles: {
      type: Boolean,
      required: false,
      default: false
    },
    showList: {
      type: Boolean,
      required: false,
      default: false
    },
    detailGetCall: {
      type: String,
      required: false,
      default: ''
    },
    additionalCall: {
      type: String,
      required: false,
      default: ''
    },
    itemLimit: {
      type: Number,
      required: false,
      default: 18
    },
    forceReload: {
      type: Boolean,
      required: false,
      default: false
    },
    filterMode: {
      type: String,
      required: false,
      default: ''
    },
    filter: {
      type: Object,
      required: false,
      default: null
    },
    showPaddingTop: {
      type: Boolean,
      required: false,
      default: true
    },
    scrollToStart: {
      type: Boolean,
      required: false,
      default: true
    },
    searchTerm: {
      type: String,
      required: false,
      default: ''
    },
    mode: {
      type: String,
      required: false,
      default: ''
    },
    slugMode: {
      type: String,
      required: false,
      default: ''
    },
    allFilters: {
      type: Array,
      required: false,
      default: null
    },
    givenSelectedFilters: {
      type: Array,
      required: false,
      default: null
    },
    allSortings: {
      type: Array,
      required: false,
      default: null
    }
  },
  data() {
    return {
      listItems: [],
      pages: [],
      lastPage: 1,
      currentIndex: -1,
      searchTitle: "",
      showFilters: false,
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
  created() {
    if (window.localStorage.getItem('selected-publication-genre') !== null && this.isSearch === false) {
      const item = JSON.parse(window.localStorage.getItem('selected-publication-genre'));
      this.setFilter(item.value, item.mode, 0);
    } else {
      this.retrieveItems(true);
    }

    this.limit = this.itemLimit;
    this.pageSizes = [
      this.itemLimit,
      this.itemLimit * 2,
      this.itemLimit * 3
    ];
    if (this.showFilterSelection === true) {
      this.sortings = this.allSortings;
      this.getFilters();
    }
  },
  methods: {
    setPage(paginationPage) {
      this.page = paginationPage;
      window.localStorage.setItem('filter-page-' + this.mode, this.page);
      this.retrieveItems();
    },
    getDetailGetCall() {
      switch (this.mode) {
      default:
        return ''
      }
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
    setFilter(listFilter, mode, level) {
      if (typeof this.selectedFilters[mode] === 'undefined') {
        this.selectedFilters[mode] = [];
      }
      let storageFilter = JSON.parse(window.localStorage.getItem('filter-' + this.filterMode));
      if (storageFilter === null) {
        storageFilter = {};
      }
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
                  this.selectedFilters[mode].push(listFilter);
                }

                storageFilter.selectedFilters = this.selectedFilters;

                if (this.filterMode !== '') {
                  window.localStorage.setItem('filter-' + this.filterMode, JSON.stringify(storageFilter));
                }

                window.localStorage.removeItem('selected-publication-genre');
                this.retrieveItems();
              }
            }
          } else {
            listFilter.children = response.data;
            listFilter.id = response.data.main.id;
            if (level === 1) {
              this.selectedFilters[mode][0] = listFilter;
            } else {
              this.selectedFilters[mode].push(listFilter);
            }
            
            storageFilter.selectedFilters = this.selectedFilters;

            if (this.filterMode !== '') {
              window.localStorage.setItem('filter-' + this.filterMode, JSON.stringify(storageFilter));
            }

            window.localStorage.removeItem('selected-publication-genre');
            this.retrieveItems();
          }
        })
        .catch((e) => {
        });
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
      return (this.showFilters === true) ? 'grid space-y-10 md:space-y-0 gap-0 md:gap-10 grid-cols-1 md:grid-cols-3 justify-items-stretch mt-12' : 'grid space-y-10 md:space-y-0 gap-0 md:gap-10 grid-cols-1 md:grid-cols-4 justify-items-stretch mt-12';
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
      this.page = 1;
      this.retrieveItems();
    },
    setMobileAlphabeticalFilter(letter) {
      this.alphabetical = (letter.target.value === 'ALL') ? '' : letter.target.value;
      this.retrieveItems();
    },
    removeFilter(listFilter, mode) {
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

              window.localStorage.setItem('filter-' + this.filterMode, JSON.stringify(storageFilter));
              this.retrieveItems();
            } else {
              this.selectedFilters[mode].splice(i, length);
              storageFilter.selectedFilters = this.selectedFilters;

              window.localStorage.setItem('filter-' + this.filterMode, JSON.stringify(storageFilter));
              this.retrieveItems();
            }
          }
        } else {
          this.retrieveItems();
        }
        
      }
    },
    getFilters() {
      this.showFilters = true;
      window.localStorage.setItem('filter-page-' + this.mode, null);
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
      this.page = page;
      let params = {};

      if (init === true && (this.filterMode !== '' && window.localStorage.getItem('filter-' + this.filterMode) !== null)) {
        const storageFilter = JSON.parse(window.localStorage.getItem('filter-' + this.filterMode));

        if (typeof storageFilter.selectedSorting !== 'undefined') {
          this.selectedSorting = storageFilter.selectedSorting;
        }
        if (typeof storageFilter.sortDirection !== 'undefined') {
          this.sortDirection = storageFilter.sortDirection;
        }
        if (typeof storageFilter.selectedFilters !== 'undefined') {
          this.selectedFilters = storageFilter.selectedFilters;
        }
      }

      if (searchTitle) {
        params["title"] = searchTitle;
      }

      if (page) {
        params["page"] = page;
      }

      params["alphabetical"] = this.alphabetical;

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
    retrieveItems(init = false) {
      this.getRequestParams(
        this.searchTitle,
        (window.localStorage.getItem('filter-page-' + this.mode) !== null && window.localStorage.getItem('filter-page-' + this.mode) !== 'null') ? window.localStorage.getItem('filter-page-' + this.mode) : this.page,
        (this.showFilterSelection === true) ? this.limit : 20,
        init
      ).then(params => {
        this.showLoading = true;

        if (this.$store.state.auth.token) {
          const config = {
            headers: { Authorization: `Bearer ${this.$store.state.auth.token}` }
          };
          axios.post(process.env.apiURL + '/fe/api/' + this.call, params)
            .then((response) => {
              this.showLoading = false;
              this.isInitialLoaded = init;
              this.listItems = response.data[this.model];
              this.pages = response.data.pages;
              this.lastPage = response.data.lastPage;
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
              this.lastPage = response.data.lastPage;
              this.count = this.listItems.length;
              this.$forceUpdate();
            })
            .catch((e) => {
            });
        }
      });
    },
    getPaddingTop() {
      return (this.showPaddingTop === true) ? 'lg:pt-24 ' : 'lg:pt-2';
    },
    getMode() {
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

<template>
  <div
    id="simplePaginationListStart"
    class="mx-auto px-4 min-h-56"
  >
    <div :class="{'min-h-56': showFilterSection()}">
      <filters
        v-if="configuration.showFilters === true && initialLoaded === true "
        :config="configuration"
        @set-sorting="setSorting($event)"
        @apply="apply($event)"
        @set-sorting-direction="setSortingDirection($event)"
      />
    </div>

    <alphabetical
      v-if="configuration.showAlphabetical === true"
      @set-alphabetical="setAlphabeticalFilter($event)"
    />

    <loading
      v-if="showList === false"
    />
    <div
      v-else
      class="flex flex-wrap"
    >
      <simple-pagination
        :pages="pages"
        :list-items="listItems"
        :detail-page="configuration.detailPage"
        :page="configuration.page"
      />
    </div>
  </div>
</template>

<script>
import { defineAsyncComponent } from "vue";
import ValueService from '@SmugAdministration/js/services/value/value.service';
import ApiService from '@SmugAdministration/js/services/api/api.service';
import LocalStorageService from '@SmugAdministration/js/services/storage/local.storage.service';
import ParameterService from '../../../../services/api/parameter.service';
const Loading = defineAsyncComponent(() =>
  import("../../../common/Content/Loading" /* webpackChunkName: "frontend-loading" */)
);
const SimplePagination = defineAsyncComponent(() =>
  import("../additional/Pagination/SimplePagination.vue" /* webpackChunkName: "simple-pagination" */)
);
const Alphabetical = defineAsyncComponent(() =>
  import("../additional/Pagination/Alphabetical" /* webpackChunkName: "pagination-alphabetical" */)
);
const Filters = defineAsyncComponent(() =>
  import("../additional/List/Filters" /* webpackChunkName: "pagination-filters" */)
);

export default {
  name: "SimplePaginatedList",
  components: {
    SimplePagination,
    Loading,
    Alphabetical,
    Filters
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
      initialLoaded: false,
      showList: true,
      listItems: [],
      pages: [],
      getResponse: {},
      configuration: {}
    };
  },
  created() {
    this.start(true);
  },
  methods: {
    async start(init = false) {
      this.showList = false;
      if (init === true) {
        await this.setProps();
        this.initialLoaded = true;
      }

      this.configuration.pageSizes = [
        this.configuration.itemLimit,
        this.configuration.itemLimit * 2,
        this.configuration.itemLimit * 3
      ];

      this.configuration.pageSize = this.configuration.itemLimit;

      LocalStorageService.set('list--config-' + this.dataset.frontendid, JSON.stringify(this.configuration));
      
      this.getItems();
    },
    async setProps() {
      const props = JSON.parse(this.dataset.props);
      this.configuration = {
        itemLimit: (props.itemLimit) ? parseInt(props.itemLimit) : 10,
        allSortings: (props.allSortings) ? props.allSortings : [],
        allFilters: (props.allFilters) ? props.allFilters : null,
        facets: (props.facets) ? props.facets : [],
        filter: (props.filter) ? props.filter : null,
        selectedSorting: 'title',
        sortDirection: 'ASC',
        page: 1,
        detailPage: props.detailPage ?? '/',
        selectedFilters: (props.selectedFilters) ? props.selectedFilters : {},
        forceSiteReload: (props.forceReload) ? ValueService.getBooleanValue(props.forceReload) : false,
        showAlphabetical: (props.showAlphabetical) ? ValueService.getBooleanValue(props.showAlphabetical) : false,
        showFilters: (props.showFilters) ? ValueService.getBooleanValue(props.showFilters) : false,
        showSearch: (props.showSearch) ? ValueService.getBooleanValue(props.showSearch) : false,
        searchTerm: (props.searchTerm) ? props.searchTerm : '',
        core: (props.core) ? props.core : ''
      }

      if (this.configuration.filter !== null && Object.keys(this.configuration.filter).length > 0) {
        for (const [key, value] of Object.entries(this.configuration.filter)) {

          this.configuration[key] = value;
        }
      }
      let urlParams = new URLSearchParams(window.location.search);

      if (urlParams.has('p')) {
        this.setFilterPage(parseInt(urlParams.get('p')));
        this.configuration.page = urlParams.get('p');
      }
    },
    apply(data) {
      this.configuration.sortDirection = data.sortDirection;
      this.configuration.selectedSorting = data.selectedSorting;
      this.configuration.facets = data.facets;
      this.configuration.searchTerm = data.searchTerm;
  
      this.start();
    },
    async setFilterPage(page) {
      window.localStorage.setItem('filter-page-' + this.mode, page);
    },
    showFilterSection() {
      return (this.configuration.showFilters && this.configuration.showFilters === true);
    },
    async getItems() {
      await this.getData();
      this.showList = true;
    },
    async getData() {
      await this.renewRequestParams();
      await ApiService.post(
        '/fe/api/list/search',
        this.params,
        false
      ).then(result => {
        this.pages = result.pages;
        this.page = this.params.page;
        this.lastPage = (result.lastPage) ? result.lastPage : result.pages.end;
        this.$nextTick(()=> {
          this.configuration.allFilters = result.filters;
          this.listItems = result.results;
        })
      }).catch(function (error) {
      });
    },
    async renewRequestParams() {
      this.filterPage = await this.getFilterPage();
      this.params = await ParameterService.getPaginationParameters(this.configuration);
    },
    async getFilterPage() {
      return (LocalStorageService.has('filter-page-' + this.mode)) ? LocalStorageService.get('filter-page-' + this.mode) : this.configuration.page;
    },
    handlePageChange(value) {
      this.configuration.page = value;
      this.start();
    },
    handlePageSizeChange(event) {
      this.limit = event.target.value;
      this.configuration.page = 1;
      this.start();
    },
  }
}
</script>

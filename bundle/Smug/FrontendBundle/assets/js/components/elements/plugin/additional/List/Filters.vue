<template>
  <div
    class="w-full py-5"
  >
    <div class="text-right">
      <button
        class="my-8 outline-link outline-link-primary"
        @click="showFilterSelection = !showFilterSelection"
      >
        {{ $t("FILTER_SELECTION") }}
      </button>
    </div>
    <div v-if="configuration.showSearch === true">
      <input
        id="search-input"
        ref="searchValueInput"
        v-model="configuration.searchTerm"
        autocomplete="off"
        class="w-full input rounded-3xl shadow-lg border-gray mb-3"
        :placeholder="$t('SEARCH')"
        @input="searchit"
      >
    </div>
    <div class="flex flex-wrap my-4">
      <div
        v-if="configuration.sortDirection !== '' && applied === true"
        class="bg-primary rounded-md py-2 px-3 mr-3 text-white cursor-pointer"
        @click="removeConfiguration('sortDirection')"
      >
        {{ $t('SORT_DIRECTION') }}: {{ $t(configuration.sortDirection) }}
      </div>
      <div
        v-if="configuration.selectedSorting !== '' && applied === true"
        class="bg-primary rounded-md py-2 px-3 mr-3 text-white cursor-pointer"
        :class="{'ml-3': page == configuration.sortDirection !== ''}"
        @click="removeConfiguration('selectedSorting')"
      >
        {{ $t('SORTING') }}: {{ $t(configuration.selectedSorting) }}
      </div>
      <div 
        v-for="(facet, facetIndex) in configuration.facets"
        :key="facetIndex"
      >
        <div
          v-if="applied === true && !Array.isArray(facet.values)"
          class="bg-primary rounded-md py-2 px-3 mx-3 text-white cursor-pointer"
          @click="removeFilterConfiguration(facetIndex)"
        >
          {{ $t(facet.values) }}
        </div>
        <div
          v-if="applied === true && Array.isArray(facet.values)"
          class="flex flex-wrap"
        >
          <div 
            v-for="(facetValue, facetValueIndex) in facet.values"
            class="bg-primary rounded-md py-2 px-3 mx-3 text-white cursor-pointer"
            @click="removeFilterArrayConfiguration(facetIndex, facetValueIndex)"
          >
            {{ $t(facet.type) }}: {{ $t(facetValue) }}
          </div>
        </div>
      </div>
    </div>
    <filter-selection
      v-if="showFilterSelection"
      :config="config"
      @change-visibility="handleVisibilty($event)"
      @apply="apply($event)"
      @set-sorting="setSorting($event)"
      @set-listfilter="setListfilter($event)"
      @set-sorting-direction="setSortingDirection($event)"
    />
  </div>
</template>
  
<script>
import { defineAsyncComponent } from "vue";
const FilterSelection = defineAsyncComponent(() =>
  import("./FilterSelection" /* webpackChunkName: "frontend-filter-selection" */)
);
  
export default {
  name: "Filters",
  components: {
    FilterSelection
  },
  props: {
    config: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      showFilterSelection: false,
      applied: false,
      configuration: {
        allFilters: [],
        showSearch: false,
        facets: [],
        searchTerm: '',
        selectedSorting: '',
        sortDirection: '',
        filters: []
      }
    };
  },
  mounted() {
    if (this.applied === false && this.config.sortDirection !== 'ASC') {
      this.configuration.sortDirection = this.config.sortDirection;
    }
    this.configuration.selectedSorting = this.config.selectedSorting;
    this.configuration.allFilters = this.config.allFilters;
    this.configuration.searchTerm = this.config.searchTerm;
    this.configuration.showSearch = this.config.showSearch;
    this.configuration.facets = this.config.facets ?? [];

    if (this.configuration.allFilters) {
      for (let i = 0; i <= this.configuration.allFilters.length - 1; i++) {
        if (!this.configuration.facets.some(e => e.type === this.configuration.allFilters[i].mode)) {
          this.configuration.facets.push({
            type: this.configuration.allFilters[i].mode,
            values: []
          });
        }
      }
    }
    this.applied = true;
  },
  methods: {
    handleVisibilty(value) {
      this.showFilterSelection = value;
    },
    setSorting(sorting) {
      this.configuration.selectedSorting = sorting;
    },
    setSortingDirection(direction) {
      this.configuration.sortDirection = direction;
      this.$emit('apply', this.configuration);
    },
    setListfilter(filter) {
      if (this.configuration.facets.length === 0) {
        this.configuration.facets.push({
          type: filter.mode,
          values: []
        });
        this.configuration.facets[0].values.push(filter.value);
        this.$emit('apply', this.configuration);
      }  else {
        for (let i = 0; i <= this.configuration.facets.length - 1; i++) {
          if (this.configuration.facets[i].type === filter.mode) {
            if (this.configuration.facets[i].values.indexOf(filter.value) < 0) {
              this.configuration.facets[i].values.push(filter.value);
            } else {
              this.configuration.facets[i].values.splice(this.configuration.facets[i].values.indexOf(filter.value), 1);
            }
            this.$emit('apply', this.configuration);
          }
        }
      }
    },
    searchit(val, event) {
      this.isFocused = true;
      if (this.configuration.searchTerm.length === 0) {
        this.$emit('apply', this.configuration);
      }
      if (this.configuration.searchTerm.length > 2) {
        if (this.timeout) clearTimeout(this.timeout)
        this.timeout = setTimeout(() => {
          this.$emit('apply', this.configuration);
        }, 400);
      }
    },
    removeConfiguration(property) {
      this.configuration[property] = '';
      this.$emit('apply', this.configuration);
    },
    removeFilterConfiguration(index) {
      this.configuration.filters.splice(index, 1);
      this.$emit('apply', this.configuration);
    },
    removeFilterArrayConfiguration(index, valueIndex) {
      this.configuration.facets[index].values.splice(valueIndex, 1);
      this.$emit('apply', this.configuration);
    },
    apply() {
      this.showFilterSelection = false;
      this.applied = true;
      this.$emit('apply', this.configuration);
    }
  }
}
</script>
  
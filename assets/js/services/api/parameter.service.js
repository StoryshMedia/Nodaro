import { isProxy, toRaw } from 'vue';

class ParameterService {
  async getPaginationParameters(options) {
    let params = {};
    params.page = options.page ?? 1;

    if (options.init === true && (options.filterMode !== '' && window.localStorage.getItem('filter-' + options.filterMode) !== null)) {
      const storageFilter = JSON.parse(window.localStorage.getItem('filter-' + options.filterMode));

      if (typeof storageFilter.selectedSorting !== 'undefined') {
        options.selectedSorting = storageFilter.selectedSorting;
      }
      if (typeof storageFilter.selectedFilters !== 'undefined' && options.init === false) {
        options.selectedFilters = storageFilter.selectedFilters;
      }
    }

    if (options.searchTitle) {
      params["title"] = options.searchTitle;
    }

    window.localStorage.getItem('filter-alphabetical-' + options.mode, options.alphabetical);

    params["alphabetical"] = (window.localStorage.getItem('filter-alphabetical-' + options.mode) !== null && window.localStorage.getItem('filter-alphabetical-' + options.mode) !== 'null') ? window.localStorage.getItem('filter-alphabetical-' + options.mode) : options.alphabetical;

    if (params["alphabetical"] === 'ALL') {
      params["alphabetical"] = '';
    }

    params["limit"] = options.pageSize ?? 999;

    if (options.facets !== null) {
      params["facets"] = (isProxy(options.facets)) ? toRaw(options.facets) : options.facets;
    }

    if (options.searchTerm !== '') {
      params["search"] = options.searchTerm;
    }

    if (options.mode !== '') {
      params["mode"] = options.mode;
    }

    params["core"] = options.core ?? '';
    params["isPaginated"] = true;
    params["direction"] = options.sortDirection;

    if (options.allSortings !== null && Object.keys(options.selectedSorting).length > 0) {
      params["allSortings"] = options.allSortings;
      params["sorting"] = options.selectedSorting;
    }

    if (options.allFilters !== null) {
      params["allFilters"] = options.allFilters;

      const selectedFilterKeys = Object.keys(options.selectedFilters);
      if (selectedFilterKeys.length > 0) {
        const length = selectedFilterKeys.length - 1;
        let tempFilters = [];
        let i = 0;

        for (i; i <= length; i++) {
          const modeLength = options.selectedFilters[selectedFilterKeys[i]].length - 1;
          let j = 0;

          if (typeof options.selectedFilters[selectedFilterKeys[i]] === 'object') {
            if (options.allFilters.length > 1) {
              const allFilterLength = options.allFilters.length - 1;
              
              for (let allFilterCounter = 0; allFilterCounter <= allFilterLength; allFilterCounter++) {
                if (options.allFilters[allFilterCounter].value === selectedFilterKeys[i]) {
                  const filter = options.allFilters[allFilterCounter];

                  filter.filterData.parameters[0].value = options.selectedFilters[selectedFilterKeys[i]].id;

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
              const filter = options.allFilters[i];

              if (options.selectedFilters[selectedFilterKeys[i]].length > 1) {
                filter.filterData.parameters[0].value = options.selectedFilters[selectedFilterKeys[i]].slice(-1)[0].id;
                if (selectedFilterKeys.length > 1) {
                  tempFilters.push(filter);

                  if (i === length) {
                    params['filter'] = tempFilters;
                  }
                } else {
                  params['filter'] = filter;
                }
              } else {
                if (options.selectedFilters[selectedFilterKeys[i]].length > 0 ) {
                  filter.filterData.parameters[0].value = (typeof options.selectedFilters[selectedFilterKeys[i]][0].id !== 'undefined') ? this.selectedFilters[selectedFilterKeys[i]][0].id : this.selectedFilters[selectedFilterKeys[i]][0].slug;

                  if (selectedFilterKeys.length > 1) {
                    tempFilters.push(filter);

                    if (i === length) {
                      params['filter'] = tempFilters;
                    }
                  } else {
                    params['filter'] = filter;
                  }
                } else {
                  if (typeof options.selectedFilters[selectedFilterKeys[i]].value !== 'undefined') {
                    if (typeof options.selectedFilters[selectedFilterKeys[i]].value.id !== 'undefined') {
                      filter.filterData.parameters[0].value = options.selectedFilters[selectedFilterKeys[i]].value.id;
                    } else {
                      filter.filterData = options.selectedFilters[selectedFilterKeys[i]].filterData;
                    }
                    params['filter'] = filter;
                  }
                }
              }
            }
          } else {
            for (j; j <= modeLength; j++) {
              const allFilterLength = options.allFilters.length - 1;
              let k = 0;

              for (k; k <= allFilterLength; k++) {
                if (options.selectedFilters[selectedFilterKeys[i]][j].mode === options.allFilters[k].value) {
                  const filter = options.allFilters[k];

                  filter.filterData.parameters[0].value = options.selectedFilters[selectedFilterKeys[i]][j].id;
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
  }
}
export default new ParameterService();
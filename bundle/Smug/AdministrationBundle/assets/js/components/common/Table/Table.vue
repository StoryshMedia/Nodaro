<template>
  <section class="min-h-80vh">
    <div
      v-if="isLoading === true"
      class="h-full flex"
    >
      <loading /> 
    </div>
    <div
      v-if="isLoading === false && notAllowed === false"
    >
      <div class="flex items-end mb-8">
        <button
          v-if="tableConfig.listConfig.url.administration.back"
          type="button"
          class="btn btn-dark mr-3"
          @click="back()"
        >
          {{ $t('BACK') }}
        </button>
        <button
          v-if="editAllowed === true && tableConfig.listConfig.url.administration.add"
          type="button"
          class="btn btn-success"
          @click="setAdd()"
        >
          {{ $t('ADD') }}
        </button>
      </div>
      <div class="datatable">
        <div class="bh-datatable bh-antialiased bh-relative bh-text-black bh-text-sm bh-font-normal">
          <div class="datatable relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full max-w-full text-sm text-left text-gray-500">
              <thead class="text-xs text-gray-700 bg-gray-50">
                <tr>
                  <th
                    scope="col"
                    class="p-4"
                  >
                    <div class="flex items-center">
                      <input
                        id="checkbox-all-search"
                        type="checkbox"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                      >
                      <label
                        for="checkbox-all-search"
                        class="sr-only"
                      >checkbox</label>
                    </div>
                  </th>
                  <th
                    v-for="(column, colindex) in cols"
                    :key="colindex"
                    scope="col"
                    class="px-6 py-3"
                  >
                    {{ $t(getColumnHeader(column.identifier)) }}
                  </th>
                  <th
                    scope="col"
                    class="px-6 py-3 text-right"
                  >
                    {{ $t('ACTIONS') }}
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(item, itemindex) in tableData"
                  :key="itemindex"
                  class="bg-white border-b hover:bg-gray-50"
                >
                  <td class="w-4 p-4">
                    <div class="flex items-center">
                      <input
                        id="checkbox-table-search-1"
                        type="checkbox"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                      >
                      <label
                        for="checkbox-table-search-1"
                        class="sr-only"
                      >checkbox</label>
                    </div>
                  </td>
                  <td
                    v-for="(col, colindex) in cols"
                    :key="colindex"
                    scope="row"
                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap max-w-1/2"
                  >
                    <span
                      v-if="col.type === 'string' && getOutput(getItemData(item, col))"
                      v-html="$t(getOutput(getItemData(item, col)))"
                    />
                    <span
                      v-if="col.type === 'date'"
                      v-html="$t(getDateOutput(getItemData(item, col)))"
                    />
                    <div
                      v-if="col.type === 'array'"
                      class="grid grid-cols-1 md:grid-cols-4 gap-3"
                    >
                      <div
                        v-for="(subData, subDataindex) in getItemData(item, col)"
                        :key="subDataindex"
                        class="pr-3 overflow-hidden"
                      >
                        <strong>{{ subDataindex }}</strong>:<br>
                        <span
                          class="break-words"
                          v-html="getOutput(subData)"
                        />
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex justify-end">
                      <button
                        type="button"
                        class="mr-2"
                        data-tippy-target="edit"
                        @click="setEdit(item.id)"
                      >
                        <icon
                          :icon-string="'IconPencil'"
                          :class="'w-18 h-18'"
                        />
                      </button>
                      <button
                        v-if="editAllowed === true"
                        type="button"
                        data-tippy-target="delete"
                      >
                        <icon
                          :icon-string="'IconTrashLines'"
                          :class="'w-18 h-18'"
                        />
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>

            <div class="py-5 px-3">
              <span class="text-sm font-normal text-gray-500 my-4 block w-full md:inline md:w-auto">
                <span class="font-semibold text-gray-900">
                  {{ range.from }} - {{ range.to }}
                </span>
                {{ $t('OF') }}
                <span class="font-semibold text-gray-900">
                  {{ absolute }}
                </span>
              </span>
            </div>

            <div class="bh-pagination bh-py-5 flex items-end pr-5">
              <nav
                class="bh-pagination-number sm:bh-ml-auto bh-inline-flex bh-items-center bh-space-x-1"
                aria-label="Pagination"
              >
                <button
                  type="button"
                  class="bh-page-item first-page"
                  :class="{ disabled: page === pages.start }"
                  @click="setPage(1)"
                >
                  <span>
                    <svg
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                      class="w-4.5 h-4.5"
                    >
                      <path
                        d="M13 19L7 12L13 5"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      />
                      <path
                        opacity="0.5"
                        d="M16.9998 19L10.9998 12L16.9998 5"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      />
                    </svg>
                  </span>
                </button>
                <button
                  type="button"
                  :class="{ disabled: page === pages.start }"
                  class="bh-page-item previous-page"
                  @click="decreasePage()"
                >
                  <span>
                    <svg
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                      class="w-4.5 h-4.5"
                    >
                      <path
                        d="M15 5L9 12L15 19"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      />
                    </svg>
                  </span>
                </button>
                <button 
                  class="bh-page-item"
                  :class="{
                    'disabled bh-active': pages.start == page
                  }"
                  @click="setPage(pages.start)"
                >
                  {{ pages.start }}
                </button>
                <button 
                  v-for="(paginationPage, index) in pages.preSteps"
                  :key="index"
                  class="bh-page-item"
                  :class="{
                    'disabled bh-active': paginationPage == page
                  }"
                  @click="setPage(paginationPage)"
                >
                  {{ paginationPage }}
                </button>
                <button
                  v-for="(paginationPage) in pages.mainSteps"
                  :key="paginationPage"
                  :class="{
                    'disabled bh-active': paginationPage == page
                  }"
                  type="button"
                  class="bh-page-item"
                  @click="setPage(paginationPage)"
                >
                  {{ paginationPage }}
                </button>
                <button 
                  v-for="(paginationPage, index) in pages.postSteps"
                  :key="index"
                  class="bh-page-item"
                  :class="{
                    'disabled bh-active': paginationPage == page
                  }"
                  @click="setPage(paginationPage)"
                >
                  {{ paginationPage }}
                </button>
                <button
                  type="button"
                  class="bh-page-item next-page"
                  :class="{ disabled: page === pages.end }"
                  @click="increasePage()"
                >
                  <span>
                    <svg
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                      class="w-4.5 h-4.5"
                    >
                      <path
                        d="M9 5L15 12L9 19"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      />
                    </svg>
                  </span>
                </button>
                <button
                  type="button"
                  class="bh-page-item last-page"
                  :class="{ disabled: page === pages.end }"
                  @click="lastPage()"
                >
                  <span>
                    <svg
                      width="24"
                      height="24"
                      viewBox="0 0 24 24"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                      class="w-4.5 h-4.5"
                    >
                      <path
                        d="M11 19L17 12L11 5"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      />
                      <path
                        opacity="0.5"
                        d="M6.99976 19L12.9998 12L6.99976 5"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      />
                    </svg>
                  </span>
                </button>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="isLoading === false && notAllowed === true">
      <not-allowed />
    </div>
  </section>
</template>

<script>
import { defineAsyncComponent } from "vue";
import TextService from "../../../services/text/text.service";
import DateService from "../../../services/date/date.service";
import ApiService from '../../../services/api/api.service';
const Icon = defineAsyncComponent(() =>
  import("../../../../../../FrontendBundle/assets/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);
const NotAllowed = defineAsyncComponent(() =>
  import("../Main/NotAllowed.vue" /* webpackChunkName: "not-allowed" */)
);
const Loading = defineAsyncComponent(() =>
  import("../Main/Loading.vue" /* webpackChunkName: "loading" */)
);

export default {
  name: "Table",
  components: {
    Icon,
    Loading,
    NotAllowed
  },
  inject: ['dataset'],
  data() {
    return{
      cols: [],
      isLoading: false,
      notAllowed: false,
      editAllowed: false,
      tableConfig: {},
      security: {},
      tableData: [],
      pages: [],
      range: [],
      absolute: 1,
      page: 1,
      filterData: {
        limit: 12
      }
    }
  },
  async created() {
    await this.setProps();
    await this.getData();

    this.cols = this.tableConfig.columns;
  },
  methods: {
    setProps() {
      (this.dataset.config) ? this.tableConfig = JSON.parse(this.dataset.config) : null;
      (this.dataset.security) ? this.security = JSON.parse(this.dataset.security) : null;
    },
    getOutput(value) {
      return TextService.getOutput(value);
    },
    getDateOutput(value) {
      return DateService.getFormattedDate(value);
    },
    setEdit(id) {
      window.location.replace(this.tableConfig.listConfig.url.administration.detail + id);
    },
    setAdd() {
      window.location.replace(this.tableConfig.listConfig.url.administration.add);
    },
    back() {
      window.location.replace(this.tableConfig.listConfig.url.administration.back)
    },
    getItemData(item, column) {
      return (column.subIdentifier) ? item[column.identifier][column.subIdentifier] : item[column.identifier];
    },
    getColumnHeader(column) {
      return column.toUpperCase();
    },
    getData() {
      this.isLoading = true;

      ApiService.post(
        '/be/api/allowed',
        this.tableConfig
      )
        .then(result =>  {
          if (result.read !== true) {
            this.isLoading = false;
            this.notAllowed = true;
          } else {
            this.editAllowed = result.write;
            this.getTableData();
          }
        })
        .catch(error => {
          this.isLoading = false;
        })
        .then(function () {
        });
    },
    decreasePage() {
      this.page = this.page - 1;
      this.getTableData();
    },
    increasePage() {
      this.page = this.page + 1;
      this.getTableData();
    },
    lastPage() {
      this.page = this.pages.end;
      this.getTableData();
    },
    setPage(page) {
      this.page = page;
      this.getTableData();
    },
    getTableData() {
      this.filterData.page = this.page;

      ApiService.post(this.tableConfig.listConfig.url.api.get, this.filterData)
        .then(result =>  {
          if (!result[this.tableConfig.listConfig.paginatorModel]) {
            this.isLoading = false;
            this.notAllowed = true;
          } else {
            this.tableData = result[this.tableConfig.listConfig.paginatorModel];
            this.pages = result.pages;
            this.absolute = result.absolute;
            this.range = result.range;
            this.isLoading = false;
          }
        })
        .catch(error => {
          this.isLoading = false;
        })
        .then(function () {
        });
    }
  }
}
</script>
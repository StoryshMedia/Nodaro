<template>
  <div class="datatable">
    <div class="flex items-end mb-8">
      <button
        v-if="editAllowed === true && fieldConfig.addConfig"
        type="button"
        class="btn btn-success"
        @click="setAdd()"
      >
        {{ $t('ADD') }}
      </button>
    </div>
    <div class="bh-datatable bh-antialiased bh-relative bh-text-black bh-text-sm bh-font-normal">
      <div class="datatable relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500">
          <thead class="text-xs text-gray-700 bg-gray-50">
            <tr>
              <th
                v-for="(column, colindex) in fieldConfig.columns"
                :key="colindex"
                scope="col"
                class="px-6 py-3"
              >
                <span v-if="column.identifier">{{ $t(getColumnHeader(column.identifier)) }}</span>
              </th>
            </tr>
          </thead>
          <tbody v-if="tableData">
            <table-row
              v-for="(item, itemindex) in tableData"
              :key="itemindex"
              :item="item"
              :field-config="fieldConfig"
              :index="itemindex"
            />
          </tbody>
        </table>
      </div>
    </div>
    <data-modal
      v-if="showAdd === true"
      :item-data="{}"
      :template="fieldConfig.addConfig.config.template.data"
      :save-button-text="'SAVE'"
      :abort-button-text="'ABORT'"
      :headline="fieldConfig.addConfig.config.headline"
      @edit-reaction="handleAction($event, {})"
    />
  </div>
</template>

<script>
import ApiService from '@SmugAdministrationServices/api/api.service';
import ValueService from '@SmugAdministrationServices/value/value.service';
import { defineAsyncComponent } from "vue";
const TableRow = defineAsyncComponent(() =>
  import("./additional/table/row/TableRow.vue" /* webpackChunkName: "administration-table-row" */)
);
const DataModal = defineAsyncComponent(() =>
  import("../../../Modal/DataModal.vue" /* webpackChunkName: "administration-modal-dialog" */)
);

export default {
  name: "Table",
  components: {
    TableRow,
    DataModal
  },
  props: {
    editAllowed:{
      type: Boolean,
      required: true
    },
    baseId:{
      type: String,
      required: false,
      default: null
    },
    fieldValue:{
      type: String,
      required: false,
      default: ''
    },
    fieldConfig:{
      type: Object,
      required: false,
      default: () => ({})
    },
    fieldPlaceholder:{
      type: String,
      required: false,
      default: 'TABLE_PLACEHOLDER'
    }
  },
  data() {
    return {
      tableData: null,
      showAdd: false
    };
  },
  mounted() {
    if (this.fieldConfig.getCall) {
      this.getData();
    } else {
      this.tableData = this.fieldValue;
    }
  },
  methods: {
    getColumnHeader(column) {
      return column.toUpperCase();
    },
    getItemData(item, column) {
      return (column.subIdentifier) ? item[column.identifier][column.subIdentifier] : item[column.identifier];
    },
    setAdd() {
      this.showAdd = !this.showAdd;
    },
    handleAction(event, item, index) {
      if (event === false) {
        this.setAdd();
        return;
      }

      if (event.item === item) {
        if (event.event === 'remove') {
          this.tableData.splice(index, 1);
          return;
        }
      }

      if (typeof index !== 'undefined') {
        if (event.item) {
          this.tableData[index] = event.item;
          this.setAdd();
        }
        return;
      }
      
      this.tableData.push((event.item) ? event.item : event);
      this.setAdd();
    },
    getData() {
      const value = ValueService.getValue(this.fieldValue, this.fieldConfig);
      if (!value) {
        this.isLoading = true;
        ApiService.get(this.fieldConfig.getCall, this.fieldConfig.id)
          .then(result =>  {
            this.tableData = result;
            this.isLoading = false;
          })
          .catch(error => {
            this.isLoading = false;
          })
          .then(function () {
          });
      } else {
        this.tableData = value;
      }
    }
  }
}
</script>
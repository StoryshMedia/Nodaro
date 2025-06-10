<template>
  <tr
    v-if="itemData"
    class="bg-white border-b hover:bg-gray-50"
  >
    <td
      v-for="(col, colindex) in fieldConfig.columns"
      :key="colindex"
      scope="row"
      class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"
    >
      <column
        :column-value="(typeof itemData === 'object') ? getItemData(itemData, col) : itemData"
        :column-config="col"
        :column-type="col.type"
      />
    </td>
    <td class="px-6 py-4">
      <div class="flex justify-end">
        <span 
          v-for="(control, controlindex) in fieldConfig.controls"
          :key="controlindex"
        >
          <control
            :item="itemData"
            :type="control.type"
            :config="control.config"
            @update-value="handleAction($event, itemData, itemindex)"
          />
        </span>
      </div>
    </td>
  </tr>
</template>

<script>
import ApiService from 'SmugAdministration/js/services/api/api.service';
import ValueService from 'SmugAdministration/js/services/value/value.service';
import { defineAsyncComponent } from "vue";
const Column = defineAsyncComponent(() =>
  import("../Column.vue" /* webpackChunkName: "administration-table-column" */)
);
const Control = defineAsyncComponent(() =>
  import("../Control.vue" /* webpackChunkName: "administration-table-control" */)
);

export default {
  name: "TableRow",
  components: {
    Control,
    Column
  },
  props: {
    itemindex:{
      type: Number,
      required: true
    },
    item:{
      type: Object,
      required: true
    },
    fieldConfig:{
      type: Object,
      required: false,
      default: () => ({})
    }
  },
  data() {
    return {
      itemData: null
    };
  },
  mounted() {
    this.getData();
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
      if (this.fieldConfig.detailCall) {
        this.isLoading = true;

        const id = (this.item.id) ? this.item.id : this.item;
        ApiService.get(this.fieldConfig.detailCall, id)
          .then(result =>  {
            this.itemData = result;
            this.isLoading = false;
          })
          .catch(error => {
            this.isLoading = false;
          });
      } else {
        this.itemData = ValueService.getValue(this.item, this.fieldConfig);
      }
    }
  }
}
</script>
<template>
  <div>
    <div
      v-for="(row, rowindex) in tabConfig.rows"
      :key="rowindex"
      :class="row.class"
    >
      <div 
        v-for="(field, fieldindex) in getRowFields(row.fields)"
        :key="fieldindex"
      >
        <field-wrapper
          :key="reload"
          :field="field"
          :edit-allowed="editAllowed"
          :item="dataObject"
          @update-value="setValueChange($event, field.identifier)"
          @refresh-data="triggerRefreshData($event)"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { defineAsyncComponent } from "vue";
import ApiService from '@SmugAdministration/js/services/api/api.service';
import ConditionService from '@SmugAdministration/js/services/condition/condition.service';
const FieldWrapper = defineAsyncComponent(() =>
  import("./FieldWrapper.vue" /* webpackChunkName: "field-wrapper" */)
);

export default {
  name: "TabContent",
  components: {
    FieldWrapper
  },
  inject: ['dataset'],
  props: {
    item: {
      type: Object,
      required: true
    },
    tabConfig: {
      type: Object,
      required: true
    },
    disallowedFields: {
      type: Array,
      required: true
    },
    hiddenFields: {
      type: Array,
      required: true
    },
    editAllowed: {
      type: Boolean,
      required: true
    }
  },
  data() {
    return{
      tabs: [],
      detailData: {},
      config: {},
      dataObject: {},
      reload: 0
    }
  },
  mounted() {
    this.dataObject = this.item;
  },
  methods: {
    setValueChange(event, identifier) {
      if (identifier) {
        if (identifier.includes('.')) {
          const identifiers = identifier.split('.');

          if (typeof this.dataObject[identifiers[0]] === 'undefined') {
            this.dataObject[identifiers[0]] = {};
          }
          this.dataObject[identifiers[0]][identifiers[1]] = event;
        }

        this.dataObject[identifier] = event;
      } else {
        this.dataObject = event;
      }

      this.$emit('updateValue', this.dataObject);
    },
    triggerRefreshData(event) {
      this.reload++;
    },
    getRowFields(fields) {
      let activeFields = [];
      const fieldLength = fields.length;

      for (let count = 0; count <= fieldLength - 1; count++) {
        if (fields[count].identifier === '') {
          if (fields[count].config && fields[count].config.condition) {
            if (ConditionService.check(fields[count].config, this.dataObject)) {
              activeFields.push(fields[count]);
            }
          } else {
            activeFields.push(fields[count]);
          }
        } else {
          if (!this.hiddenFields.includes(fields[count].identifier)) {
            if (this.disallowedFields.includes(fields[count].identifier)) {
              fields[count].config.disabled = true;
            }

            if (fields[count].config && fields[count].config.condition) {
              if (ConditionService.check(fields[count].config, this.dataObject)) {
                activeFields.push(fields[count]);
              }
            } else {
              activeFields.push(fields[count]);
            }
          }
        }

        if (count === fieldLength - 1) {
          return activeFields;
        }
      }
    },
    getData() {
      this.isLoading = true;
      ApiService.get(this.config.getCall, this.config.id).then(result => {
        this.detailData = result;
        this.isLoading = false;
      });
    }
  }
}
</script>
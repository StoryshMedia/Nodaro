<template>
  <div>
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
    <div
      v-for="(item, itemindex) in items"
      :key="itemindex"
    >
      <div
        v-for="(row, rowindex) in fieldConfig.rows"
        :key="rowindex"
        :class="row.rowClass"
      >
        <div
          v-if="row.controls"
          :class="row.controlClass"
        >
          <button-controls
            :key="reload"
            :controls="row.controls"
            :item="item"
            @called="getData()"
          />
        </div>
        <div 
          v-for="(field, fieldindex) in row.fields"
          :key="fieldindex"
        >
          <field-wrapper
            :key="reload"
            :field="field"
            :edit-allowed="editAllowed"
            :item="item"
            @update-value="setValueChange($event)"
            @refresh-data="triggerRefreshData($event)"
          />
        </div>
      </div>
    </div>
    <div v-if="items.length === 0">
      <div class="flex items-center p-3.5 rounded text-white bg-success">
        <span class="pr-2">{{ $t('NO_DATA_FOUND') }}</span>
      </div>
    </div>
    <data-modal
      v-if="showAdd === true"
      :item-data="baseObject"
      :template="fieldConfig.addConfig.config.template.data"
      :save-button-text="'SAVE'"
      :abort-button-text="'ABORT'"
      :headline="fieldConfig.addConfig.config.headline"
      @edit-reaction="handleAction($event)"
    />
  </div>
</template>

<script>
import ApiService from '@SmugAdministrationServices/api/api.service';
import { defineAsyncComponent } from "vue";
const FieldWrapper = defineAsyncComponent(() =>
  import("../../FieldWrapper.vue" /* webpackChunkName: "field-wrapper" */)
);
const DataModal = defineAsyncComponent(() =>
  import("../../../Modal/DataModal.vue" /* webpackChunkName: "administration-modal-dialog" */)
);
const ButtonControls = defineAsyncComponent(() =>
  import("./additional/button/ButtonControls.vue" /* webpackChunkName: "administration-button-controls" */)
);

export default {
  name: "AdvancedAssociation",
  components: {
    ButtonControls,
    FieldWrapper,
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
      type: Object,
      required: false,
      default: null
    },
    fieldConfig:{
      type: Object,
      required: false,
      default: () => ({})
    },
    fieldPlaceholder:{
      type: String,
      required: false,
      default: 'TEXT_PLACEHOLDER'
    }
  },
  data() {
    return {
      items: [],
      baseObject: {},
      reload: 0,
      showAdd: false
    }
  },
  mounted() {
    if (!this.fieldConfig.getCall) {
      this.items = this.fieldValue;
    } else {
      this.getData();
    }
    if (this.fieldConfig.id && this.fieldConfig.parent) {
      this.baseObject[this.fieldConfig.parent] = {
        id: this.fieldConfig.id
      };
    }
  },
  methods: {
    setAdd() {
      this.showAdd = !this.showAdd;
    },
    setContent() {
      this.$emit('updateValue', this.items);
    },
    removeItem(event, index) {
      if (event.event === 'remove') {
        this.items.splice(index, 1);
        this.reload++;
      }
    },
    setValueChange(event) {
      this.$emit('updateValue', event);
    },
    triggerRefreshData(event) {
      this.reload++;
    },
    handleAction(event, index) {
      if (event === false) {
        this.setAdd();
        return;
      } else {
        if (typeof index !== 'undefined') {
          this.items[index] = event.item;
          this.setAdd();
          this.$emit('updateValue', this.items);
          return;
        }

        if (this.fieldConfig.addConfig.config.saveCall) {
          ApiService.post(this.fieldConfig.addConfig.config.saveCall, event.item)
            .then(result =>  {
              this.items.push(event.item);
              this.setAdd();
              this.$emit('updateValue', this.items);
            })
            .catch(error => {
              this.isLoading = false;
            })
            .then(function () {
            });
        } else {
          this.items.push(event.item);
          this.setAdd();
          this.$emit('updateValue', this.items);
        }
      }
    },
    getData() {
      ApiService.get(this.fieldConfig.getCall, this.fieldConfig.id)
        .then(result =>  {
          this.items = result;
          this.reload++;
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
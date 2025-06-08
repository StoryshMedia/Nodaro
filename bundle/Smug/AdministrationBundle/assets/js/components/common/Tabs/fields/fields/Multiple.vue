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
      v-if="items && items.length > 0"
      :class="getItemClass()"
    >
      <div 
        v-for="(field, fieldindex) in items"
        :key="fieldindex"
      >
        <div class="mt-2 mx-auto">
          <field
            :key="reload"
            :field-string="fieldConfig.type"
            :item-value="field"
            :field-config="fieldConfig.config"
            :field-placeholder="fieldConfig.placeholder"
            :edit-allowed="editAllowed"
            @update-value="setContent($event)"
            @remove-value="removeItem($event, fieldindex)"
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
      :item-data="{}"
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
const Field = defineAsyncComponent(() =>
  import("../Field.vue" /* webpackChunkName: "field" */)
);
const DataModal = defineAsyncComponent(() =>
  import("../../../Modal/DataModal.vue" /* webpackChunkName: "administration-modal-dialog" */)
);

export default {
  name: "Multiple",
  components: {
    Field,
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
      reload: 0,
      items: [],
      showAdd: false
    }
  },
  mounted() {
    if (!this.fieldConfig.config.getCall) {
      this.items = this.fieldValue;
    } else {
      this.getData();
    }
    window.addEventListener('refresh-view', (event) => {
      if (!this.fieldConfig.config.getCall) {
        this.items = this.fieldValue;
      } else {
        this.getData();
      }
    });
  },
  methods: {
    setAdd() {
      this.showAdd = !this.showAdd;
    },
    getItemClass() {
      return (this.fieldConfig.itemClass) ? this.fieldConfig.itemClass : 'grid grid-cols-1 md:grid-cols-3';
    },
    setContent(event) {
      if (this.fieldConfig.saveCall) {
        ApiService.put(this.fieldConfig.saveCall, event)
          .then(response =>  {
            if (typeof this.fieldConfig.emitReaction === 'undefined' || this.fieldConfig.emitReaction === true) {
              this.$emit('updateValue', this.fieldValue);
            }
          })
          .catch(error => {
            this.isLoading = false;
          });
      } else {
        this.$emit('updateValue', this.fieldValue);
      }
    },
    removeItem(event, index) {
      if (event.event === 'remove') {
        this.items.splice(index, 1);
        this.reload++;
      }
    },
    handleAction(event, index) {
      if (event === false) {
        this.setAdd();
        return;
      } else {
        if (this.fieldConfig.addConfig.config.template.data.config.saveCall && typeof index === 'undefined') {
          this.performAddItemCall(event.item, this.fieldConfig.addConfig.config.template.data.config.saveCall);
        } else {
          if (typeof index !== 'undefined') {
            this.items[index] = event.item;
            this.setAdd();
            this.$emit('updateValue', this.items);
            return;
          }

          this.items.push(event.item);
          this.setAdd();
          this.$emit('updateValue', this.items);
        }
      }
    },
    performAddItemCall(data, call) {
      if (this.fieldConfig.addConfig.config.parentIdentifier && this.fieldConfig.id) {
        data[this.fieldConfig.addConfig.config.parentIdentifier] = {id: this.fieldConfig.id};
        ApiService.post(call, data)
          .then(result =>  {
            this.items.push(result);
            this.setAdd();
            this.$emit('updateValue', this.items);
          })
          .catch(error => {
            this.isLoading = false;
          });
      }
    },
    getData() {
      if (this.fieldConfig.id) {
        ApiService.get(this.fieldConfig.config.getCall, this.fieldConfig.id)
          .then(result =>  {
            this.items = result;
            this.reload++;
            this.$emit('updateValue', this.items);
            this.isLoading = false;
          })
          .catch(error => {
            this.isLoading = false;
          });
      } else {
        ApiService.get(this.fieldConfig.config.getCall)
          .then(result =>  {
            this.items = result;
            this.reload++;
            this.$emit('updateValue', this.items);
            this.isLoading = false;
          })
          .catch(error => {
            this.isLoading = false;
          });
      }
    }
  }
}
</script>
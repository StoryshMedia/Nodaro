<template>
  <section>
    <h5
      v-if="field.placeholder"
      class="font-semibold text-lg pb-2"
    >
      {{ $t(field.placeholder) }}
    </h5>
            
    <alert
      v-if="field.information"
      :edit-allowed="false"
      :type="'dark'"
      :label="field.information"
    />

    <field
      :key="reload"
      :field-string="field.type"
      :item-value="getItemValue()"
      :field-config="field.config"
      :base-id="getBaseId()"
      :field-placeholder="field.placeholder"
      :edit-allowed="editAllowed"
      @update-value="setValueChange($event)"
      @refresh-data="triggerRefreshData($event)"
    />
  </section>
</template>

<script>
import { defineAsyncComponent } from 'vue';
const Field = defineAsyncComponent(() =>
  import("./fields/Field.vue" /* webpackChunkName: "field" */)
);

export default {
  name: "FieldWrapper",
  components: {
    Field
  },
  props: {
    editAllowed:{
      type: Boolean,
      required: true
    },
    field:{
      type: Object,
      required: true
    },
    item:{
      type: Object,
      required: false,
      default: () => ({})
    }
  },
  data() {
    return {
      reload: 0
    }
  },
  methods: {
    getBaseId() {
      if (this.field.identifier && this.field.identifier.includes('.')) {
        const identifiers = this.field.identifier.split('.');

        if (this.item[identifiers[0]]) {
          return this.item[identifiers[0]].id ?? null;
        }
      } else {
        return this.item.id ?? null;
      }
    },
    getItemValue() {
      if (this.field.type === 'Column') {
        return this.item;
      }

      if (this.field.identifier && this.field.identifier.includes('.')) {
        const identifiers = this.field.identifier.split('.');

        if (this.item[identifiers[0]]) {
          return this.item[identifiers[0]][identifiers[1]] ?? '';
        }
      }
      return this.item[this.field.identifier] ?? '';
    },
    setValueChange(event) {
      this.$emit('updateValue', event);
    },
    triggerRefreshData(event) {
      this.$emit('refreshData', true);
      this.reload++;
    }
  }
}
</script>
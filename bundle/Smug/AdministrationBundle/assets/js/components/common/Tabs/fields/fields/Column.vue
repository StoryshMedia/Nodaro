<template>
  <div>
    <div
      v-if="items && items.length > 0"
    >
      <div 
        v-for="(field, fieldindex) in items"
        :key="fieldindex"
        :class="fieldConfig.fieldClasses"
      >
        <div class="mt-2 mx-auto">
          <h5
            v-if="field.placeholder"
            class="font-semibold text-lg pb-2"
          >
            {{ $t(field.placeholder) }}
          </h5>
          
          <field
            :key="reload"
            :field-string="field.type"
            :item-value="fieldValue[field.identifier]"
            :field-config="getFieldConfig(field.config)"
            :field-placeholder="field.placeholder"
            :edit-allowed="editAllowed"
            @update-value="setContent(field.identifier, $event)"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineAsyncComponent } from "vue";
const Field = defineAsyncComponent(() =>
  import("../Field.vue" /* webpackChunkName: "field" */)
);

export default {
  name: "Column",
  components: {
    Field
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
      items: []
    }
  },
  mounted() {
    this.items = this.fieldConfig.items;
  },
  methods: {
    setContent(identifier, event) {
      this.fieldValue[identifier] = event;
      this.$emit('updateValue', this.fieldValue);
    },
    getFieldConfig(config) {
      if (this.fieldConfig.id) {
        config.id = this.fieldConfig.id;
      }

      return config;
    }
  }
}
</script>
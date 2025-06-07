<template>
  <div>
    <div 
      v-for="(field, fieldindex) in fieldValue"
      :key="fieldindex"
    >
      <div class="mx-auto">
        <h5
          v-if="field.placeholder"
          class="font-semibold text-lg pb-2"
        >
          {{ $t(field.placeholder) }}
        </h5>
        <field
          :key="reload"
          :field-string="field.type"
          :item-value="field.value"
          :field-config="field.config"
          :field-placeholder="field.placeholder"
          :edit-allowed="editAllowed"
          @update-value="setContent($event, fieldindex)"
        />
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
  name: "PluginFields",
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
      type: String,
      required: false,
      default: '',
      immediate: true
    },
    fieldConfig:{
      type: Object,
      required: false,
      default: () => ({})
    }
  },
  data() {
    return {
    };
  },
  methods: {
    setContent(event, index) {
      this.fieldValue[index].value = event;
      this.$emit('update-value', this.fieldValue);
    }
  }
}
</script>
<template>
  <section>
    <component
      :is="componentLoader"
      :field-value="itemValue"
      :base-id="baseId"
      :field-placeholder="fieldPlaceholder"
      :field-config="fieldConfig"
      :object-values="objectValues"
      :edit-allowed="editAllowed"
      @update-value="emitData($event, 'updateValue')"
      @refresh-data="emitData($event, 'refreshData')"
    />
  </section>
</template>

<script>
import { defineAsyncComponent } from 'vue'
import * as activeFields from 'Bundle/activeFields.json'

export default {
  name: "Field",
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
    fieldString:{
      type: String,
      required: true
    },
    itemValue:{
      type: [String, Number, Object, Array, Boolean],
      required: true
    },
    fieldPlaceholder:{
      type: String,
      required: false,
      default: ''
    },
    fieldConfig:{
      type: Object,
      required: false,
      default: () => ({})
    },
    objectValues:{
      type: Object,
      required: false,
      default: () => ({})
    }
  },
  data() {
    return {
      value: '',
      reload: 0
    }
  },
  computed: {
    componentLoader () {
      const field = activeFields[activeFields.findIndex((obj) => obj.name === this.fieldString)];

      try {
        if (field) {
          return defineAsyncComponent(() => import("../../../../../../../../" + field.path + ".vue"));
        } else {
          return '';
        }
      } catch (e) {
        return '';
      }
    }
  },
  mounted () {
    this.value = this.itemValue;
  },
  methods: {
    emitData(value, eventName) {
      this.$emit(eventName, value);
    }
  }
}
</script>
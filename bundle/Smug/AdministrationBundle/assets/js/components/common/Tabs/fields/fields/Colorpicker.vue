<template>
  <input
    type="color"
    class="form-input"
    :value="getValue()"
    @change="setContent($event)"
  >
</template>

<script>
import ValueService from '@SmugAdministrationServices/value/value.service';

export default {
  name: "Colorpicker",
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
      default: 'TEXT_PLACEHOLDER'
    }
  },
  methods: {
    getValue() {
      return ValueService.getValue(this.fieldValue, this.fieldConfig);
    },
    setContent(content) {
      this.$emit('updateValue', content.target.value);
    },
  }
}
</script>
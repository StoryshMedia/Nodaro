<template>
  <textarea
    type="text"
    :placeholder="$t(fieldPlaceholder)"
    class="form-textarea"
    :class="fieldConfig.classes ?? ''"
    :rows="fieldConfig.rows ?? 5"
    :value="getValue()"
    :disabled="isDisabled()"
    @change="setContent($event)"
  />
</template>

<script>
import ValueService from '@SmugAdministrationServices/value/value.service';

export default {
  name: "Textarea",
  props: {
    editAllowed:{
      type: Boolean,
      required: true
    },
    fieldValue:{
      type: String,
      required: false,
      default: ''
    },
    baseId:{
      type: String,
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
  methods: {
    getValue() {
      return ValueService.getValue(this.fieldValue, this.fieldConfig);
    },
    setContent(content) {
      this.$emit('updateValue', content.target.value);
    },
    isDisabled() {
      if (this.editAllowed === false) {
        return true;
      }
      if (this.fieldConfig.disabled && this.fieldConfig.disabled === true) {
        return true;
      }
    }
  }
}
</script>
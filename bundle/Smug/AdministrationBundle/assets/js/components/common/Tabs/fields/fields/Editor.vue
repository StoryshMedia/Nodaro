<template>
  <div>
    <QuillEditor
      v-if="editAllowed"
      :content="value"
      :read-only="isDisabled()"
      :content-type="'html'"
      :theme="'snow'"
      @ready="setReady()"
      @update:content="changeContent($event)"
    />
    <div
      v-else
      v-html="fieldValue"
    />
  </div>
</template>

<script>
import ValueService from '@SmugAdministration/js/services/value/value.service';

export default {
  name: "Editor",
  props: {
    editAllowed:{
      type: Boolean,
      required: false
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
  data() {
    return {
      value: '',
      isLoaded: false
    };
  },
  mounted() {
    this.value = this.getFieldValue();
    this.setReady();
  },
  methods: {
    getFieldValue() {
      return ValueService.getValue(this.fieldValue, this.fieldConfig);
    },
    setReady() {
      this.isLoaded = true;
    },
    changeContent(value) {
      this.$emit('updateValue', value);
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
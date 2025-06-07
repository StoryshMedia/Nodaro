<template>
  <div class="flex items-start">
    <input
      :id="id"
      type="checkbox"
      :checked="isChecked()"
      :model="value"
      :disabled="isDisabled()"
    >
    <label
      class="checkbox-label"
      :class="{ active: isChecked() }"
      :for="id"
      @click="setContent()"
    >
      <span
        v-if="isChecked()"
        class="ms-3 text-sm font-medium text-dark"
      >{{ $t(getLabel('true')) }}</span>
      <span
        v-else
        class="ms-3 text-sm font-medium text-dark"
      >{{ $t(getLabel('false')) }}</span>
    </label>
  </div>
</template>

<script>
import ValueService from '../../../../../services/value/value.service';

export default {
  name: "Checkbox",
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
      type: Boolean,
      required: false,
      default: false
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
    return{
      value: false
    }
  },
  mounted() {
    this.value = ValueService.getBooleanValue(this.fieldValue);
  },
  methods: {
    isChecked() {
      return this.value === true;
    },
    setContent() {
      if (this.isDisabled()) {
        return;
      }
      const value = this.value;
      this.value = !value;
      this.$emit('updateValue', this.value);
    },
    isDisabled() {
      if (this.editAllowed === false) {
        return true;
      }
      if (this.fieldConfig.disabled && this.fieldConfig.disabled === true) {
        return true;
      }
    },
    getLabel(type) {
      if (this.fieldConfig[type + 'Label']) {
        return this.fieldConfig[type + 'Label'];
      }

      return type;
    }
  }
}
</script>
<template>
  <select
    class="form-select text-dark"
    :model="getFieldValue()"
    :disabled="isDisabled()"
    @change="setContent($event)"
  >
    <optgroup
      v-for="(core, coreIndex) in coreItems"
      :key="coreIndex"
      :label="core.title"
    >
      <option
        v-for="(itemValue, itemIndex) in core.items"
        :key="itemIndex"
        :value="getValue(itemValue.value)"
        :selected="isSelected(getValue(itemValue.value))"
      >
        <span>
          {{ $t(itemValue['title']) }}
        </span>
      </option>
    </optgroup>
  </select>
</template>

<script>
import ApiService from '@SmugAdministration/js/services/api/api.service';
import ValueService from '@SmugAdministration/js/services/value/value.service';

export default {
  name: "CoreSelect",
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
  data() {
    return {
      coreItems: [],
      isLoading: false
    }
  },
  mounted() {
    this.getData();
  },
  methods: {
    getFieldValue() {
      return ValueService.getValue(this.fieldValue, this.fieldConfig);
    },
    getValue(value) {
      return value;
    },
    setContent(content) {
      this.$emit('updateValue', content.target.value);
    },
    isSelected(value) {
      if (typeof this.fieldValue === 'string') {
        return value.replace(/['"]+/g, '') === this.fieldValue;
      }

      return value === this.getValue(this.fieldValue);
    },
    getData() {
      this.isLoading = true;
      ApiService.get('/be/api/custom/core/list').then(result => {
        this.isLoading = false;
        this.coreItems = result;
      });
    },
    isDisabled() {
      if (this.isLoading === true) {
        return true;
      }
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
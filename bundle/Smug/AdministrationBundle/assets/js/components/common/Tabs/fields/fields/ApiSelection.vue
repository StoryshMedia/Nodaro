<template>
  <select
    class="form-select text-dark"
    :model="getFieldValue()"
    :disabled="isDisabled()"
    @change="setContent($event)"
  >
    <option value="">
      {{ $t('PLEASE_CHOOSE') }}
    </option>
    <option
      v-for="(entpoint, entpointIndex) in endpoints"
      :key="entpointIndex"
      :value="getValue(entpoint.value)"
      :selected="isSelected(getValue(entpoint.value))"
    >
      <span>
        {{ $t(entpoint.title) }}
      </span>
    </option>
  </select>
</template>

<script>
import ApiService from '../../../../../services/api/api.service';
import ValueService from '../../../../../services/value/value.service';

export default {
  name: "ApiSelection",
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
      endpoints: [],
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
      ApiService.get('/be/api/custom/api/endpoint/list').then(result => {
        this.isLoading = false;
        this.endpoints = result;
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
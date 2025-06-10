<template>
  <flat-pickr
    v-model="value"
    class="form-input"
    :config="configuration"
    :disabled="isDisabled()"
    @change="setContent($event)"
  />
</template>

<script>
import ValueService from 'SmugAdministration/js/services/value/value.service';
import flatPickr from 'vue-flatpickr-component';
import { German } from "flatpickr/dist/l10n/de.js";
flatpickr.localize(German);
import 'flatpickr/dist/flatpickr.css';

export default {
  name: "Datepicker",
  components: {
    flatPickr
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
      default: 'DATE'
    }
  },
  data() {
    return {
      configuration: {},
      value: ''
    };
  },
  async created() {
    await this.setConfiguration();
  },
  methods: {
    setContent(content) {
      this.$emit('updateValue', content.target.value);
    },
    setConfiguration() {
      const value = ValueService.getValue(this.fieldValue, this.fieldConfig);
      const formats = {
        'date': 'd.m.Y',
        'dateTime': 'd.m.Y H:i',
        'time': 'H:i',
      };
      if (value !== '') {
        this.value = new Date(value.date);
      }


      this.configuration = {
        dateFormat: formats[this.fieldConfig.valueType],
        noCalendar: (this.fieldConfig.valueType && this.fieldConfig.valueType === 'time'),
        enableTime: (this.fieldConfig.valueType && this.fieldConfig.valueType === 'time')
      }
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
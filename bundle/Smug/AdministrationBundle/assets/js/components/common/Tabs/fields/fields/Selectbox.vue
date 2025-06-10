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
      v-for="(itemValue, itemIndex) in items"
      :key="itemIndex"
      :value="getValue(itemValue.value)"
      :selected="isSelected(getValue(itemValue.value))"
    >
      <span>
        {{ $t(itemValue.title) }}
      </span>
    </option>
  </select>
</template>

<script>
import ApiService from '@SmugAdministration/js/services/api/api.service';
import ValueService from '@SmugAdministration/js/services/value/value.service';

export default {
  name: "Selectbox",
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
      items: []
    }
  },
  mounted() {
    if (!this.fieldConfig.items && this.fieldConfig.getCall) {
      this.getData();
    } else if(this.fieldConfig.items) {
      this.items = this.fieldConfig.items;
    }
  },
  methods: {
    getFieldValue() {
      return ValueService.getValue(this.fieldValue, this.fieldConfig);
    },
    isDisabled() {
      if (this.editAllowed === false) {
        return true;
      }
      if (this.fieldConfig.disabled && this.fieldConfig.disabled === true) {
        return true;
      }
    },
    setContent(content) {
      if (content.target.value !== '') {
        this.$emit('updateValue', JSON.parse(content.target.value));
      }
    },
    isSelected(value) {
      if (typeof this.fieldValue === 'string') {
        try {
          value = JSON.parse(value);
        } catch (e) {
          return false;
        }

        let compareValue = (typeof value === 'string') ? value.replace(/['"]+/g, '') : value.value;

        return compareValue === this.fieldValue;
      }


      if (JSON.parse(value).id) {
        return JSON.parse(value).id === this.fieldValue.id;
      }

      return value.id === this.getValue(this.fieldValue).id;
    },
    getTitle(itemValue) {
      if (this.fieldConfig.titleIdentifier) {
        return itemValue[this.fieldConfig.titleIdentifier];
      }

      return itemValue['title'];
    },
    getData() {
      this.isLoading = true;

      if (this.fieldConfig.payload) {
        ApiService.post(this.fieldConfig.getCall, this.fieldConfig.payload)
          .then(result =>  {
            let items = [];
            let count = 0;

            for (count; count <= result.length - 1; count++) {
              const itemValue = (result[count].value) ? result[count].value : result[count];

              items.push({value: itemValue, title: this.getTitle(result[count])});

              if (count === result.length - 1) {
                this.items = items;
              }
            }
          })
          .catch(error => {
            this.isLoading = false;
          })
          .then(function () {
          });
      } else {
        ApiService.get(this.fieldConfig.getCall)
          .then(result =>  {
            let items = [];
            let count = 0;

            for (count; count <= result.length - 1; count++) {
              const itemValue = (result[count].value) ? result[count].value : result[count];

              items.push({value: itemValue, title: this.getTitle(result[count])});

              if (count === result.length - 1) {
                this.items = items;
              }
            }
          })
          .catch(error => {
            this.isLoading = false;
          })
          .then(function () {
          });
      }
    },
    getValue(value) {
      return JSON.stringify(value);
    }
  }
}
</script>
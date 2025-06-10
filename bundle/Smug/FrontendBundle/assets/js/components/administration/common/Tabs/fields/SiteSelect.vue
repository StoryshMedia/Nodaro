<template>
  <section class="w-full">
    <multiselect
      v-if="sites.length > 0"
      id="site--select"
      v-model="currentValue"
      :options="sites"
      :placeholder="$t('PLEASE_SELECT')"
      label="title"
      track-by="slug"
      @select="setContent($event)"
      @remove="setContent('')"
    />
  </section>
</template>

<script>
import Multiselect from 'vue-multiselect';
import ValueService from '@SmugAdministration/js/services/value/value.service';
import ApiService from '@SmugAdministration/js/services/api/api.service';

export default {
  name: "SiteSelect",
  components: {
    Multiselect
  },
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
      currentValue: {},
      sites: [],
      isLoading: false
    }
  },
  mounted() {
    if (this.fieldValue !== '') {
      this.currentValue = (typeof this.fieldValue === 'string') ? JSON.parse(this.fieldValue) : this.fieldValue;
    }
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
      this.$emit('updateValue', content);
    },
    getData() {
      this.isLoading = true;
      ApiService.get('/be/api/custom/site/domain/sites/', this.baseId).then(result => {
        this.isLoading = false;
        this.sites = result;
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
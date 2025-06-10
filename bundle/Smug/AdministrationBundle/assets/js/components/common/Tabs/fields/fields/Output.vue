<template>
  <div class="group">
    <div
      v-if="fieldConfig.showHeader && fieldConfig.showHeader === true"
      class="group-hover:text-primary font-semibold text-base whitespace-nowrap pb-3"
    >
      {{ $t(fieldConfig.header) }}
    </div>
    <div
      class="text-white-dark overflow-hidden w-full line-clamp-1"
      v-html="getFieldValue()"
    />
  </div>
</template>

<script>
import ValueService from 'SmugAdministration/js/services/value/value.service';

export default {
  name: "Output",
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
  methods: {
    getFieldValue() {
      return ValueService.getValue(this.fieldValue, this.fieldConfig);
    }
  }
}
</script>
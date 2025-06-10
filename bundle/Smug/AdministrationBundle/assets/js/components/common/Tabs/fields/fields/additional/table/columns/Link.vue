<template>
  <div>
    <a :href="columnConfig.link + columnValue.id">
      <span v-html="getOutput(columnValue[columnConfig.subIdentifier])" />
    </a>
  </div>
</template>

<script>
import TextService from 'SmugAdministration/js/services/text/text.service';

export default {
  name: "String",
  props: {
    columnValue:{
      type: String,
      required: true
    },
    columnConfig:{
      type: Object,
      required: false,
      default: null
    }
  },
  methods: {
    getOutput(value) {
      return TextService.getOutput(value);
    }
  }
}
</script>
<template>
  <div v-html="getOutput(columnValue)" />
</template>

<script>
import TextService from '@SmugAdministrationServices/text/text.service';

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
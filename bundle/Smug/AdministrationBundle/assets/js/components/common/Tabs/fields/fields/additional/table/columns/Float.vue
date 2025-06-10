<template>
  <div>
    {{ getOutput(columnValue) }}
  </div>
</template>

<script>
import NumberService from 'SmugAdministration/js/services/number/number.service';

export default {
  name: "Float",
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
      return NumberService.getFloat(value);
    }
  }
}
</script>
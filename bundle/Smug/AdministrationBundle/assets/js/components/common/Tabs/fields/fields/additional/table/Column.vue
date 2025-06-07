<template>
  <component
    :is="componentLoader"
    :column-value="columnValue"
  />
</template>

<script>
import { defineAsyncComponent } from 'vue'

export default {
  name: "Column",
  props: {
    columnValue:{
      type: String,
      required: true
    },
    columnType:{
      type: String,
      required: true
    }
  },
  computed: {
    componentLoader () {
      return defineAsyncComponent(() => import("./columns/" + this.columnType.charAt(0).toUpperCase() + this.columnType.slice(1) + ".vue"));
    }
  }
}
</script>
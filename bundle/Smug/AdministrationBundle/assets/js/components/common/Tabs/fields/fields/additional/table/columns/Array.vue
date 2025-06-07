<template>
  <component
    :is="componentLoader"
    :column-config="columnConfig"
    :column-value="getValue()"
  />
</template>

<script>
import { defineAsyncComponent } from 'vue';

export default {
  name: "Array",
  props: {
    columnValue:{
      type: Object,
      required: true
    },
    columnConfig:{
      type: Object,
      required: true
    }
  },
  computed: {
    componentLoader () {
      return defineAsyncComponent(() => import("./" + this.columnConfig.subType.charAt(0).toUpperCase() + this.columnConfig.subType.slice(1) + ".vue"));
    }
  },
  methods: {
    getValue() {
      if (this.columnConfig.subType == 'link') {
        return this.columnValue;
      } else {
        return this.columnValue[this.columnConfig.subIdentifier];
      }
    }
  }
}
</script>
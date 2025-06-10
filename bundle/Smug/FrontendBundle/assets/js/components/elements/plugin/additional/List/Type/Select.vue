<template>
  <select-box
    :items="options"
    :option-label-identifier="'label'"
    :clear-on-select="true"
    @option-selected="setFilter($event)"
  />
</template>
  
<script>
import { defineAsyncComponent } from "vue";
const SelectBox = defineAsyncComponent(() =>
  import("../../../../../common/Input/SelectBox" /* webpackChunkName: "select-box" */)
);
  
export default {
  name: "Select",
  components: {
    SelectBox
  },
  props: {
    options: {
      type: Object,
      required: false,
      default: () => ({})
    },
    activeFilters: {
      type: Object,
      required: false,
      default: () => ({})
    }
  },
  methods: {
    setFilter(filter) {
      this.$emit('setFilter', filter);
    }
  }
}
</script>
  
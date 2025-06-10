<template>
  <component
    :is="componentLoader"
    :options="options"
    :active-filters="activeFilters"
    @setFilter="setFilter($event)"
  />
</template>
  
<script>
import { defineAsyncComponent } from "vue";

export default {
  name: "Type",
  props: {
    type: {
      type: String,
      required: true
    },
    activeFilters: {
      type: Object,
      required: false,
      default: () => ({})
    },
    options: {
      type: Object,
      required: false,
      default: () => ({})
    }
  },
  computed: {
    componentLoader () {
      try {
        return defineAsyncComponent(() => import("./" + this.type + ".vue"));
      } catch (e) {
        return '';
      }
    }
  },
  methods: {
    setFilter(filter) {
      this.$emit('setTypeFilter', filter);
    }
  }
}
</script>
  
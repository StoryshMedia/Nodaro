<template>
  <component
    :is="componentLoader"
    :item="item"
    :type="type"
    :config="config"
    @updateValue="emitAction($event)"
  />
</template>

<script>
import { defineAsyncComponent } from 'vue'

export default {
  name: "Control",
  props: {
    item:{
      type: String,
      required: true
    },
    type:{
      type: String,
      required: true
    },
    config:{
      type: Object,
      required: true
    }
  },
  computed: {
    componentLoader () {
      return defineAsyncComponent(() => import("./controls/" + this.type.charAt(0).toUpperCase() + this.type.slice(1) + ".vue"));
    }
  },
  methods: {
    emitAction(event) {
      this.$emit('updateValue', {event: event, item: this.item});
    }
  }
}
</script>
<template>
  <section>
    <component
      :is="componentLoader"
      :config="config"
    />
  </section>
</template>

<script>
import { defineAsyncComponent } from 'vue'
import * as activeComponents from 'Bundle/activeComponents.json'

export default {
  name: "DynamicContent",
  props: {
    contentType: {
      type: String,
      required: true
    },
    config:{
      type: Object,
      required: false,
      default: () => ({})
    }
  },
  computed: {
    componentLoader () {
      const component = activeComponents[activeComponents.findIndex((obj) => obj.name === this.contentType)];

      try {
        if (component) {
          return defineAsyncComponent(() => import("../../../../../../../" + component.path + ".vue"));
        } else {
          return '';
        }
      } catch (e) {
        return '';
      }
    }
  }
}
</script>
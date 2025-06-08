<template>
  <div>
    <button
      type="button"
      class="mx-auto btn bg-white w-8 h-8 p-0 rounded-full border-success text-success hover:bg-success hover:text-white transition-all duration-300"
      @click="emitClick()"
    >
      <icon :icon-string="config.icon" />
    </button>
    <p class="pt-2">
      {{ $t(config.label) }}
    </p>
  </div>
</template>
  
<script>
import { defineAsyncComponent } from "vue";
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);
  
export default {
  name: "Success",
  components: {
    Icon
  },
  props: {
    config:{
      type: Array,
      required: true
    }
  },
  methods: {
    emitClick() {
      this.$emit('clicked', this.config);
    }
  }
}
</script>
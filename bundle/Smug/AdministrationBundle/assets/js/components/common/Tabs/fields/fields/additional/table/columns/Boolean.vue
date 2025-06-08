<template>
  <div>
    <button
      v-if="columnValue === false"
      type="button"
      class="btn w-5 h-5 p-0 rounded-full border-danger text-danger transition-all duration-300"
    >
      <icon :icon-string="'IconX'" />
    </button>
    <button
      v-if="columnValue === true"
      type="button"
      class="btn bg-white w-5 h-5 p-0 rounded-full border-success text-success transition-all duration-300"
    >
      <icon :icon-string="'IconChecks'" />
    </button>
  </div>
</template>

<script>
import { defineAsyncComponent } from "vue";
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);

export default {
  name: "Boolean",
  components: {
    Icon
  },
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
  }
}
</script>
<template>
  <div>
    <button
      type="button"
      class="w-5 h-5 mr-2 p-0 text-dark transition-all duration-300"
    >
      <icon
        :icon-string="config.icon"
        :class="'w-18 h-18'"
      />
    </button>
  </div>
</template>

<script>
import { defineAsyncComponent } from "vue";
import TextService from "../../../../../../../../services/text/text.service";
const Icon = defineAsyncComponent(() =>
  import("../../../../../../../../../../../FrontendBundle/assets/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);

export default {
  name: "Link",
  components: {
    Icon
  },
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
  methods: {
    getOutput(value) {
      return TextService.getOutput(value);
    }
  }
}
</script>
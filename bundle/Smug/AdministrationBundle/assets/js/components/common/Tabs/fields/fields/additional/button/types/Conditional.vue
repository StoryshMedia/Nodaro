<template>
  <div
    v-if="activeConfig.type"
  >
    <danger
      v-if="activeConfig.type === 'danger'"
      :config="activeConfig"
      @clicked="emitClick()"
    />
    <success
      v-if="activeConfig.type === 'success'"
      :config="activeConfig"
      @clicked="emitClick()"
    />
  </div>
</template>
  
<script>
import { defineAsyncComponent } from "vue";
const Danger = defineAsyncComponent(() =>
  import("./Danger.vue" /* webpackChunkName: "administration-control-danger" */)
);
const Success = defineAsyncComponent(() =>
  import("./Success.vue" /* webpackChunkName: "administration-control-success" */)
);
  
export default {
  name: "Conditional",
  components: {
    Success,
    Danger
  },
  props: {
    config:{
      type: Array,
      required: true
    },
    item:{
      type: Object,
      required: true
    }
  },
  data() {
    return {
      activeConfig: {}
    };
  },
  async created() {
    await this.setConfig();
  },
  methods: {
    setConfig() {
      for (let configKey = 0; configKey <= this.config.conditions.length -1; configKey++) {
        if (this.config.conditions[configKey].condition === 'isTrue' && this.item[this.config.conditions[configKey].identifier] === true) {
          this.activeConfig = this.config.conditions[configKey];
        }
        if (this.config.conditions[configKey].condition === 'isFalse' && this.item[this.config.conditions[configKey].identifier] === false) {
          this.activeConfig = this.config.conditions[configKey];
        }
      }
    },
    emitClick() {
      this.$emit('clicked', this.activeConfig);
    }
  }
}
</script>
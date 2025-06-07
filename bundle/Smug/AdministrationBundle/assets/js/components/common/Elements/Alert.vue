<template>
  <div
    class="border-l-4 px-4 py-2 rounded-lg flex bg-dark border-primary text-white"
  >
    <span class="pr-2">
      <p
        v-if="header !== ''"
        class="text-lg font-semibold"
      >
        {{ $t(header) }}</p>
      <p>{{ $t(label) }}</p>
    </span>
    <button
      v-if="editAllowed === true"
      type="button"
      class="ml-auto hover:opacity-80"
      @click="emitData()"
    >
      <icon :icon-string="'IconX'" />
    </button>
  </div>
</template>

<script>
import { defineAsyncComponent } from 'vue'
const Icon = defineAsyncComponent(() =>
  import("../../../../../../FrontendBundle/assets/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);

export default {
  name: "Alert",
  components: {
    Icon
  },
  props: {
    editAllowed:{
      type: Boolean,
      required: false,
      default: true
    },
    label:{
      type: String,
      required: true
    },
    header:{
      type: String,
      required: false,
      default: ''
    },
    type:{
      type: String,
      required: false,
      default: 'primary'
    }
  },
  data() {
    return {
      value: '',
      conponentPath: '',
      reload: 0
    }
  },
  methods: {
    emitData() {
      this.$emit('remove', true);
    }
  }
}
</script>
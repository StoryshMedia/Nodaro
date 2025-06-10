<template>
  <div
    id="back-button"
    class="fixed z-100 bg-dark top-24 text-white left-0 h-12 w-12 md:h-16 md:w-16 hover:text-primary"
  >
    <a
      :href="getUrl()"
      title="Zurück zur Übersicht"
      class="flex justify-center items-center text-center h-full w-full"
    >
      <icon-double-left :size="'h-6 w-6 md:h-8 md:w-8'" />
    </a>
  </div>
</template>

<script>
import { defineAsyncComponent } from "vue";
const IconDoubleLeft = defineAsyncComponent(() =>
  import("@core/js/icons/icons/IconDoubleLeft" /* webpackChunkName: "icon-double-left" */)
);

export default {
  name: "Back",
  components: { IconDoubleLeft },
  inject: ['dataset'],
  methods: {
    getUrl() {
      return document.referrer;
    }
  }
};
</script>

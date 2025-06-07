<template>
  <a :href="getLink()">
    <icon
      class="w-4 h-5 flex-none"
      :icon-string="'IconMinus'"
    />

    {{ $t(item.label) }}
  </a>
</template>
  
  
<script>
import { defineAsyncComponent } from "vue";

const Icon = defineAsyncComponent(() =>
  import("../../../../../../FrontendBundle/assets/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);

export default {
  name: "NavigationLink",
  components: {
    Icon
  },
  props: {
    item: {
      type: Object,
      required: true
    }
  },
  methods: {
    getLink() {
      return process.env.frontendURL + '/admin/' + this.item.path
    }
  }
}
</script>
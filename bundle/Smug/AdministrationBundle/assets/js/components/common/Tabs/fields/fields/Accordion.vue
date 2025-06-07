<template>
  <div class="space-y-2 font-semibold">
    <Item
      v-for="(item, itemindex) in fieldValue"
      :key="itemindex"
      :item="item"
      :field-config="fieldConfig"
    />
  </div>
</template>

<script>
import { defineAsyncComponent } from "vue";
const Item = defineAsyncComponent(() =>
  import("./additional/accordion/Item.vue" /* webpackChunkName: "administration-accordion-item" */)
);

export default {
  name: "Accordion",
  components: {
    Item
  },
  props: {
    editAllowed:{
      type: Boolean,
      required: true
    },
    baseId:{
      type: String,
      required: false,
      default: null
    },
    fieldValue:{
      type: Object,
      required: false,
      default: null
    },
    fieldConfig:{
      type: Object,
      required: false,
      default: () => ({})
    },
    fieldPlaceholder:{
      type: String,
      required: false,
      default: 'TEXT_PLACEHOLDER'
    }
  },
  data() {
    return {
      reload: 0
    }
  }
}
</script>
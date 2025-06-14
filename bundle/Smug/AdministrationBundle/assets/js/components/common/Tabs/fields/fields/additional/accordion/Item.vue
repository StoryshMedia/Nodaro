<template>
  <div class="border border-gray-400  rounded">
    <button
      type="button"
      class="p-4 w-full flex items-center"
      :class="{ 'text-primary': expanded === true, 'text-dark': expanded === false }"
      @click="handleAccordionClick()"
    >
      {{ item[fieldConfig.headlineIdentifier] }}
      <div
        class="ml-auto"
      >
        <icon
          class="transform transition duration-300"
          :class="getIconClass(item.key)"
          :icon-string="'IconCaretDown'"
        /> 
      </div>
    </button>
    <vue-collapsible :is-open="expanded === true">
      <div class="p-4 border-t border-gray-400 ">
        <div 
          v-for="(field, fieldindex) in fieldConfig.fields"
          :key="fieldindex"
          class="my-5"
        >
          <field
            :key="reload"
            :field-string="field.type"
            :item-value="(field.type === 'Column') ? item : item[field.identifier]"
            :field-config="field.config"
            :field-placeholder="field.placeholder"
            :edit-allowed="editAllowed"
            @update-value="setValueChange($event)"
            @refresh-data="triggerRefreshData($event)"
          />
        </div>
      </div>
    </vue-collapsible>
  </div>
</template>

<script>
import { defineAsyncComponent } from "vue";
import VueCollapsible from 'vue-height-collapsible/vue3';
const Field = defineAsyncComponent(() =>
  import("../../../Field.vue" /* webpackChunkName: "field" */)
);
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);

export default {
  name: "Item",
  components: {
    Field,
    VueCollapsible,
    Icon
  },
  props: {
    fieldConfig:{
      type: Object,
      required: false,
      default: () => ({})
    },
    item:{
      type: Object,
      required: false,
      default: null
    }
  },
  data() {
    return {
      expanded: false,
      reload: 0
    };
  },
  methods: {
    handleAccordionClick() {
      this.expanded = !this.expanded;
    },
    getIconClass() {
      return (this.expanded === true) ? 'rotate-180' : '';
    }
  }
}
</script>
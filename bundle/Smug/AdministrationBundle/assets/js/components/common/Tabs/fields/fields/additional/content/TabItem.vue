<template>
  <div class="border border-gray rounded">
    <button
      type="button"
      class="p-1 w-full flex items-center"
      :class="{ 'text-primary': expanded === true, 'text-dark': expanded === false }"
      @click="handleAccordionClick()"
    >
      {{ $t('ELEMENT') + tabIndex }}
      <div
        class="ml-auto"
      >
        <icon
          class="transform transition duration-300"
          :icon-string="'IconCaretDown'"
        /> 
      </div>
    </button>
    <vue-collapsible :is-open="expanded === true">
      <div class="py-1 px-2 border-t border-gray">
        <div
          v-for="(field, fieldindex) in tab.fields"
          :key="fieldindex"
          class="my-2"
        >
          <h6
            :class="getSettingHeadlineClass(field)"
          >
            {{ $t(field.placeholder) }}
          </h6>
          <p
            v-if="field.description !== ''"
            class="mb-3 pl-2 text-xs"
          >
            {{ $t(field.description) }}
          </p>
          <field
            :edit-allowed="true"
            :field-string="getFieldString(field.type)"
            :item-value="getSettingValue(field)"
            :object-values="getObjectValues()"
            :field-config="getFieldConfig(field)"
            :base-id="baseId"
            :field-placeholder="field.placeholder"
            @updateValue="setSettingContent($event, fieldindex)"
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
  import("../../../../../../../../../../FrontendBundle/assets/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);

export default {
  name: "TabItem",
  components: {
    Field,
    VueCollapsible,
    Icon
  },
  props: {
    tab:{
      type: Object,
      required: false,
      default: () => ({})
    },
    baseId:{
      type: String,
      required: false,
      default: null
    },
    tabIndex:{
      type: Number,
      required: false,
      default: 1
    }
  },
  data() {
    return {
      expanded: false,
      reload: 0
    };
  },
  methods: {
    getSettingValue(setting) {
      return setting.value;
    },
    getFieldString(type) {
      return String(type).charAt(0).toUpperCase() + String(type).slice(1);
    },
    getFieldConfig(field) {
      let config = field.config ?? [];
      if (field.type === 'Selectbox' || field.type === 'AreaSelect') {
        if (!config.getCall) {
          config.getCall = '/be/api/custom/module/filter';
        }
        config.payload = {
          module: field.module
        };
      }
      if (field.config.valueType === 'file') {
        config.mini = true;
      }
      return config;
    },
    getSettingHeadlineClass(field) {
      return (field.description !== '') ? '' : 'mb-3';
    },
    getObjectValues() {
      let values = {};
      for (let i = 0; i <= this.tab.fields.length - 1; i++) {
        values[this.tab.fields[i].identifier] = this.tab.fields[i].value;

        if (i === this.tab.fields.length - 1) {
          return values;
        }
      }
    },
    setSettingContent(event, index) {
      this.tab.fields[index].value = event;
      this.$emit('updateValue', this.tab);
    },
    handleAccordionClick() {
      this.expanded = !this.expanded;
    },
    getIconClass() {
      return (this.expanded === true) ? 'rotate-180' : '';
    }
  }
}
</script>
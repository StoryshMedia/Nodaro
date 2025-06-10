<template>
  <section class="item--configuration">
    <perfect-scrollbar
      :options="{
        swipeEasing: true,
        wheelPropagation: false,
      }"
      class="h-full mb-16 py-3 mx-5"
    >
      <div
        class="text-right pr-2 pt-2"
      >
        <button
          class="text-dark hover:text-primary hover:border-b-2 hover:border-primary mx-auto"
          style="transition: all 0.15s ease 0s;"
          @click="close()"
        >
          {{ $t('CLOSE') }}
        </button>
      </div>

      <h5
        class="font-semibold text-lg pb-2"
      >
        {{ $t('CONTENT_ITEM_TITLE') }}
      </h5>
      <p
        class="mb-3 pl-2 text-xs"
      >
        {{ $t('CONTENT_ITEM_TITLE_DESCRIPTION') }}
      </p>
      <field
        :edit-allowed="true"
        :field-string="'Text'"
        :item-value="fieldValue.title"
        :field-config="fieldConfig"
        :base-id="baseId"
        :field-placeholder="'TITLE'"
        @updateValue="setItemTitle($event)"
      />

      <h2
        class="flex flex-row flex-nowrap items-center mb-5 mt-8"
      >
        <span class="flex-grow block border-t border-dark" />
        <span class="flex-none block mx-2 px-4 py-2.5 rounded leading-none bg-dark text-white">
          {{ $t('CLASSES') }}
        </span>
        <span class="flex-grow block border-t border-dark" />
      </h2>
      
      <class-select @updateValue="addClass($event)" />
      
      <div class="flex flex-wrap">
        <div
          v-for="(fieldClass, classIndex) in getFieldClasses()"
          :key="classIndex"
          class="px-0.5"
        >
          <span class="badge bg-dark px-0.5 rounded-full flex items-center text-xs">
            <span class="ml-2">{{ fieldClass }}</span>
            <span
              class="ml-2 mr-2 cursor-pointer hover:opacity-90"
              @click="removeClass(classIndex)"
            >x</span>
          </span>
        </div>
      </div>

      <div v-if="fieldValue.templateClasses.length > 0">
        <h2
          class="flex flex-row flex-nowrap items-center mb-5 mt-8"
        >
          <span class="flex-grow block border-t border-dark" />
          <span class="flex-none block mx-2 px-4 py-2.5 rounded leading-none bg-dark text-white">
            {{ $t('TEMPLATE_ITEM_SETTINGS') }}
          </span>
          <span class="flex-grow block border-t border-dark" />
        </h2>

        <div v-for="(item, itemIndex) in fieldValue.templateClasses">
          <h5
            class="font-semibold text-lg pb-2"
          >
            {{ $t(getTemplateItemClassesTitle(item)) }}
          </h5>

          <class-select @updateValue="addTemplateItemClass($event, itemIndex)" />
          
          <div class="flex flex-wrap">
            <div
              v-for="(fieldClass, classIndex) in item.value"
              :key="classIndex"
              class="px-0.5"
            >
              <span class="badge bg-dark px-0.5 rounded-full flex items-center text-xs">
                <span class="ml-2">{{ fieldClass }}</span>
                <span
                  class="ml-2 mr-2 cursor-pointer hover:opacity-90"
                  @click="removeTemplateItemClass(itemIndex, classIndex)"
                >x</span>
              </span>
            </div>
          </div>
        </div>
      </div>

      <h2
        v-if="showPluginSettingsHeadline()"
        class="flex flex-row flex-nowrap items-center mb-5 mt-8"
      >
        <span class="flex-grow block border-t border-dark" />
        <span class="flex-none block mx-2 px-4 py-2.5 rounded leading-none bg-dark text-white">
          {{ $t('PLUGIN_SETTINGS') }}
        </span>
        <span class="flex-grow block border-t border-dark" />
      </h2>

      <div
        v-for="(setting, settingIndex) in fieldValue.module.fields"
        :key="settingIndex"
        class="my-2"
      >
        <h6
          v-if="setting.isPlugin === true"
          :class="getSettingHeadlineClass(setting)"
        >
          {{ $t(setting.placeholder) }}
        </h6>
        <p
          v-if="setting.description !== ''"
          class="mb-3 pl-2 text-xs"
        >
          {{ $t(setting.description) }}
        </p>
        <field
          v-if="setting.isPlugin === true"
          :edit-allowed="true"
          :field-string="getFieldString(setting.type)"
          :item-value="getSettingValue(setting)"
          :object-values="getObjectValues()"
          :field-config="getFieldConfig(setting)"
          :base-id="baseId"
          :field-placeholder="setting.placeholder"
          @updateValue="setSettingContent($event, settingIndex)"
        />
      </div>

      <h2
        v-if="showTabsHeadline()"
        class="flex flex-row flex-nowrap items-center mb-5 mt-8"
      >
        <span class="flex-grow block border-t border-dark" />
        <span class="flex-none block mx-2 px-4 py-2.5 rounded leading-none bg-dark text-white">
          {{ $t('TABS') }}
        </span>
        <span class="flex-grow block border-t border-dark" />
      </h2>

      <div class="space-y-2 font-semibold">
        <TabItem
          v-for="(tab, tabIndex) in fieldValue.module.tabs"
          :key="tabIndex"
          :tab-index="tabIndex + 1"
          :tab="tab"
          :edit-allowed="true"
          :base-id="baseId"
          @updateValue="setSettingTabContent($event, tabIndex)"
        />
      </div>

      <div
        v-if="fieldValue.module.tabs.length > 0"
        class="w-full mt-8 mb-4"
      >
        <div
          class="text-center cursor-pointer md:col-span-1 text-white rounded-lg bg-dark bg-opacity-50 py-2"
          @click="addTab()"
        >
          {{ $t('ADD_TAB') }}
        </div>
      </div>

      <div
        v-if="fieldValue.module.module.multi === true"
        class="w-full mt-8 mb-4"
      >
        <div
          class="text-center cursor-pointer md:col-span-1 text-white rounded-lg bg-dark bg-opacity-50 py-2"
          @click="addColumn()"
        >
          {{ $t('ADD_COLUMN') }}
        </div>
      </div>

      <div class="flex-none h-16 mt-3">
        <div class="flex justify-end items-center pt-2">
          <button
            type="button"
            class="btn btn-success mr-2"
            @click="apply()"
          >
            {{ $t('APPLY') }}
          </button>
          <button
            type="button"
            class="btn btn-danger"
            @click="emitDelete()"
          >
            {{ $t('DELETE') }}
          </button>
        </div>
      </div>
    </perfect-scrollbar>
  </section>
</template>
<script>
import { defineComponent } from 'vue';
import { defineAsyncComponent } from "vue";
import ApiService from 'SmugAdministration/js/services/api/api.service';
const ClassSelect = defineAsyncComponent(() =>
  import("./ClassSelect.vue" /* webpackChunkName: "administration-content-class-select" */)
);
const Field = defineAsyncComponent(() =>
  import("../../../Field.vue" /* webpackChunkName: "field" */)
);
const TabItem = defineAsyncComponent(() =>
  import("./TabItem.vue" /* webpackChunkName: "administration-content-accordion-tab-item" */)
);
  
export default defineComponent({
  name: 'ElementConfiguration',
  components: {
    TabItem,
    Field,
    ClassSelect
  },
  props: {
    fieldValue:{
      type: String,
      required: false,
      default: ''
    },
    saveCall:{
      type: String,
      required: true
    },
    tabAddCall:{
      type: String,
      required: true
    },
    baseId:{
      type: String,
      required: false,
      default: null
    },
    fieldConfig:{
      type: Object,
      required: false,
      default: () => ({})
    }
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
        config.getCall = '/be/api/custom/module/filter';
        config.payload = {
          module: field.module
        };
      }
      return config;
    },
    getObjectValues() {
      let values = {};
      for (let i = 0; i <= this.fieldValue.module.fields.length - 1; i++) {
        values[this.fieldValue.module.fields[i].identifier] = this.fieldValue.module.fields[i].value;

        if (i === this.fieldValue.module.fields.length - 1) {
          return values;
        }
      }
    },
    getSettingHeadlineClass(setting) {
      return (setting.description !== '') ? '' : 'mb-3';
    },
    getItemValue() {
      return (this.fieldConfig.valueType === 'file') ? this.fieldValue.files : this.fieldValue.value;
    },
    getFieldClasses() {
      return this.fieldValue.additionalClasses;
    },
    getTemplateItemClassesTitle(item) {
      return item.title ?? item.identifier;
    },
    showPluginSettingsHeadline() {
      if (this.fieldValue.module.fields.length === 0) {
        return false;
      }

      let show = false;

      for (let i = 0; i <= this.fieldValue.module.fields.length - 1; i++) {
        if (this.fieldValue.module.fields[i].isPlugin === true) {
          show = true;
        }

        if (i === this.fieldValue.module.fields.length - 1) {
          return show;
        }
      }
    },
    showTabsHeadline() {
      if (this.fieldValue.module.tabs.length === 0) {
        return false;
      }

      return true;
    },
    addTab() {
      ApiService.post(
        this.tabAddCall,
        { data: this.fieldValue.module.tabs[0] }
      ).then(result =>  {
        this.fieldValue.module.tabs.push(result);

        this.$emit('addTab', result);  
      });
    },
    addColumn() {
      this.$emit('addColumn', {
        element: this.fieldValue
      });
    },
    addClass(event) {
      this.fieldValue.additionalClasses.push(event);
    },
    addTemplateItemClass(event, index) {
      this.fieldValue.templateClasses[index].value.push(event);
    },
    setItemTitle(event) {
      this.fieldValue.title = event;
    },
    emitDelete() {
      this.$emit('delete', true);
    },
    close() {
      this.$emit('close', true);
    },
    apply() {
      ApiService.put(
        this.saveCall,
        this.fieldValue
      ).then(result =>  {
        this.fieldValue.rendered = result.rendered;
        this.$emit('updateValue', this.fieldValue);
      });
    },
    removeClass(index) {
      this.fieldValue.additionalClasses.splice(index, 1);
    },
    removeTemplateItemClass(itemIndex, classIndex) {
      this.fieldValue.templateClasses[itemIndex].value.splice(classIndex, 1);
    },
    setSettingContent(event, index) {
      this.fieldValue.module.fields[index].value = event;
    },
    setSettingTabContent(event, index) {
      this.fieldValue.module.tabs[index] = event;
    }
  },
})
</script>
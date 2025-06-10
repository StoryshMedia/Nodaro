<template>
  <section class="p-3 item--configuration">
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

    <h2 class="flex flex-row flex-nowrap items-center mb-5">
      <span class="flex-grow block border-t border-dark" />
      <span class="flex-none block mx-2 px-4 py-2.5 rounded leading-none bg-dark text-white">
        {{ $t('BASIC_VALUES') }}
      </span>
      <span class="flex-grow block border-t border-dark" />
    </h2>

    <h6
      v-if="value.placeholder"
      class="mb-3"
    >
      {{ $t(value.placeholder) }}
    </h6>
    <field
      v-if="value.type"
      :edit-allowed="true"
      :field-string="value.type"
      :item-value="getItemValue()"
      :field-config="configuration"
      :base-id="baseId"
      :field-placeholder="value.placeholder"
      @updateValue="setContent($event)"
    />

    <div class="mb-5">
      <div v-if="showSettings()">
        <h2
          class="flex flex-row flex-nowrap items-center mb-5 mt-8"
        >
          <span class="flex-grow block border-t border-dark" />
          <span class="flex-none block mx-2 px-4 py-2.5 rounded leading-none bg-dark text-white">
            {{ $t('SETTINGS') }}
          </span>
          <span class="flex-grow block border-t border-dark" />
        </h2>
      </div>

      <div v-if="showSettings()">
        <div
          v-for="(setting, settingIndex) in value.settings"
          :key="settingIndex"
        >
          <h6
            v-if="setting.placeholder"
            class="mb-3"
          >
            {{ $t(setting.placeholder) }}
          </h6>
          <field
            :edit-allowed="true"
            :field-string="getFieldString(setting.type)"
            :item-value="getSettingValue(setting)"
            :field-config="configuration"
            :base-id="baseId"
            :field-placeholder="settingIndex"
            @updateValue="setSettingContent($event, settingIndex)"
          />
        </div>
      </div>
    </div>

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

    <div class="flex-none h-16 mt-3">
      <div class="flex justify-end items-center pt-2">
        <button
          type="button"
          class="btn btn-success mr-2"
          @click="apply()"
        >
          {{ $t('APPLY') }}
        </button>
      </div>
    </div>
  </section>
</template>
<script>
import { defineComponent } from 'vue';
import { defineAsyncComponent } from "vue";
import ApiService from '@SmugAdministration/js/services/api/api.service';
const Field = defineAsyncComponent(() =>
  import("../../../Field.vue" /* webpackChunkName: "field" */)
);
const ClassSelect = defineAsyncComponent(() =>
  import("./ClassSelect.vue" /* webpackChunkName: "administration-content-class-select" */)
);
  
export default defineComponent({
  name: 'ItemConfiguration',
  components: {
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
  data() {
    return {
      configuration: {},
      value: {}
    }
  },
  mounted() {
    this.configuration = (typeof this.fieldConfig === 'string') ? JSON.parse(this.fieldConfig) : this.fieldConfig;
    this.value = (typeof this.fieldValue === 'string') ? JSON.parse(this.fieldValue) : this.fieldValue;

    if (typeof this.value.classes === 'string') {
      this.value.classes = JSON.parse(this.value.classes);
    }
    if (typeof this.value.config === 'string') {
      this.value.config = JSON.parse(this.value.config);
    }
    this.configuration.classes = 'text-xs rounded-md';
    this.configuration.rows = 4;
    this.configuration.mini = true;
    this.configuration.deleteCall = '/be/api/smug/frontend/MediaContentItemModuleFieldAssociation/delete';
    this.configuration.uploadCall = '/be/api/media/image/upload';
    this.configuration.assignAlbum = 'frontend';
  },
  methods: {
    getSettingValue(setting) {
      return setting.value;
    },
    getFieldString(type) {
      return String(type).charAt(0).toUpperCase() + String(type).slice(1);
    },
    getItemValue() {
      return (this.configuration.valueType === 'file') ? this.value.files : this.value.value;
    },
    getFieldClasses() {
      return this.value.classes;
    },
    addClass(event) {
      this.value.classes.push(event);
    },
    close() {
      this.$emit('close', true);
    },
    apply() {
      ApiService.put(
        this.saveCall,
        this.value
      ).then(result =>  {
        this.value.rendered = result.rendered;
        this.$emit('updateValue', this.value);
      });
    },
    removeClass(index) {
      this.value.classes.splice(index, 1);
    },
    showSettings() {
      if (!this.value.settings) {
        return false;
      }

      return Object.keys(this.value.settings).length > 0;
    },
    setContent(event) {
      if (this.configuration.valueType === 'file') {
        this.value.files = event;
      } else {
        this.value.value = event;
      }
    },
    setSettingContent(event, index) {
      this.value.settings[index].value = event;
    }
  },
})
</script>
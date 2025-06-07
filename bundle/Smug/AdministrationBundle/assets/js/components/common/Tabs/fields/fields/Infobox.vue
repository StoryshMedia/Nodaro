<template>
  <div class="flex flex-wrap w-full justify-center">
    <div class="border w-full border-gray-500/20 rounded-md shadow-lg p-6">
      <div
        v-if="fieldConfig.icon && fieldConfig.icon !== ''"
        class="text-primary mb-5"
      >
        <icon
          :icon-string="fieldConfig.icon"
          :class="'w-12 h-12 flex-none'"
        />
      </div>
            
      <h5
        v-if="fieldConfig.headline && fieldConfig.headline !== ''"
        class="text-lg font-semibold mb-3.5"
      >
        {{ $t(fieldConfig.headline) }}
      </h5>
            
      <p
        v-if="fieldConfig.text && fieldConfig.text !== ''"
        class="text-white-dark text-md mb-3.5"
      >
        {{ $t(fieldConfig.text) }}
      </p>
            
      <a
        v-if="fieldConfig.linkUrl"
        :href="getHref()"
        class="text-primary font-semibold hover:underline group flex"
      >
        {{ $t(fieldConfig.linkText) }}
        <icon
          :icon-string="'IconNext'"
          :class="'w-4 h-5 flex-none ml-3'"
        />
      </a>
    </div>
  </div>
</template>

<script>
import { defineAsyncComponent } from "vue";
const Icon = defineAsyncComponent(() =>
  import("../../../../../../../../FrontendBundle/assets/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);

export default {
  name: "Infobox",
  components: {
    Icon
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
      type: String,
      required: false,
      default: ''
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
  methods: {
    getHref() {
      return process.env.frontendURL + this.fieldConfig.linkUrl;
    }
  }
}
</script>
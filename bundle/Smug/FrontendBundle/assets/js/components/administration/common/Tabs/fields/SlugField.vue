<template>
  <section class="flex">
    <input
      type="text"
      class="form-input slug-field-input"
      :value="slug"
      :disabled="isDisabled()"
      :class="fieldConfig.classes ?? ''"
      @change="setContent($event)"
    >
    <button
      type="button"
      class="btn btn-primary rounded-l-none"
      @click="handleGenerateClick()"
    >
      <icon
        class="transform transition duration-300"
        :icon-string="'IconRestore'"
      />
    </button>
  </section>
</template>

<script>
import { defineAsyncComponent } from "vue";
import ApiService from '@SmugAdministration/js/services/api/api.service';
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);

export default {
  name: "SlugField",
  components: {
    Icon
  },
  props: {
    editAllowed:{
      type: Boolean,
      required: true
    },
    fieldValue:{
      type: String,
      required: false,
      default: ''
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
    },
    fieldPlaceholder:{
      type: String,
      required: false,
      default: 'TEXT_PLACEHOLDER'
    }
  },
  data() {
    return {
      slug: '',
      isLoading: false
    }
  },
  mounted() {
    this.slug = this.fieldValue;
  },
  methods: {
    getValue(value) {
      return value;
    },
    setContent(content) {
      this.$emit('updateValue', content.target.value);
    },
    setSiteSlug(event) {
      this.slug = event.target.value;
      this.$emit('updateValue', this.slug);
    },
    handleGenerateClick() {
      this.isLoading = true;
      ApiService.put('/be/api/custom/site/slug/generate', {id: this.baseId}).then(result =>  {
        this.slug = result.slug;
        this.$emit('updateValue', this.slug);
        this.isLoading = false;
      });
    },
    isDisabled() {
      if (this.isLoading === true) {
        return true;
      }
      if (this.editAllowed === false) {
        return true;
      }
      if (this.fieldConfig.disabled && this.fieldConfig.disabled === true) {
        return true;
      }
    }
  }
}
</script>
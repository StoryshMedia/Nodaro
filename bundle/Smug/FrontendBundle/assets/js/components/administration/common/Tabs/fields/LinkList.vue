<template>
  <section>
    <div
      v-for="(link, linkIndex) of links"
      class="mt-2"
    >
      <site-select
        :edit-allowed="editAllowed"
        :field-value="link"
        :field-config="fieldConfig"
        :base-id="baseId"
        :field-placeholder="fieldPlaceholder"
        @update-value="setLink($event, linkIndex)"
      />
    </div>
    <div class="w-full mt-8 mb-4">
      <div
        class="text-center cursor-pointer md:col-span-1 text-white rounded-lg bg-dark bg-opacity-50 py-2"
        @click="addLink()"
      >
        {{ $t('ADD_LINK') }}
      </div>
    </div>
  </section>
</template>

<script>
import { defineAsyncComponent } from "vue";
const SiteSelect = defineAsyncComponent(() =>
  import("./SiteSelect.vue" /* webpackChunkName: "administration-site-select" */)
);

export default {
  name: "LinkList",
  components: {
    SiteSelect
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
      links: []
    }
  },
  mounted() {
    this.links = JSON.parse(this.fieldValue);
  },
  methods: {
    setLink(value, index) {
      this.links[index] = {
        id: value.id,
        slug: value.slug,
        title: value.title
      };
      this.setContent();
    },
    addLink() {
      this.links.push('');
    },
    setContent() {
      this.$emit('updateValue', JSON.stringify(this.links));
    },
    isDisabled() {
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
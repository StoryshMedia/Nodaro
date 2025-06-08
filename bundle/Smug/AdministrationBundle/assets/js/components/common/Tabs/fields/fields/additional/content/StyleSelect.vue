<template>
  <section>
    <p
      class="font-semibold text-lg pb-2"
    >
      {{ $t('SITE_STYLES') }}
    </p>
    <div
      class="mb-8"
    >
      <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div>
          <select
            class="form-select text-dark"
            @change="setNewStyle($event)"
          >
            <option value="">
              {{ $t('PLEASE_CHOOSE') }}
            </option>
            <option
              v-for="(style, styleIndex) in availableStyles"
              :key="styleIndex"
              :value="style.value"
            >
              <span>
                {{ $t(style.title) }}
              </span>
            </option>
          </select>
        </div>
      </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 my-5">
      <drag-list
        :items="siteStyles"
        :list-name="'styles'"
        @order-changed="onOrderChanged($event)"
      />
    </div>
  </section>
</template>

<script>
import ApiService from '@SmugAdministrationServices/api/api.service';
import { defineAsyncComponent } from 'vue';
const DragList = defineAsyncComponent(() =>
  import("../list/DragList.vue" /* webpackChunkName: "administration-list-drag-list" */)
);

export default {
  name: "StyleSelect",
  components: {
  },
  props: {
    baseId:{
      type: String,
      required: false,
      default: null
    }
  },
  data() {
    return {
      availableStyles: [],
      siteStyles: [],
      showAddScript: false
    };
  },
  mounted() {
    this.getData();
  },
  methods: {
    setNewStyle(event) {
      this.siteStyles.push(event.target.value);
      this.$emit('updateValue', this.siteStyles);
    },
    onOrderChanged(items) {
      this.siteStyles = items;
      this.$emit('updateValue', this.siteStyles);
    },
    getData() {
      ApiService.get('/be/api/custom/frontend/styles').then(result => {
        this.availableStyles = result;
      });
      ApiService.get(
        '/be/api/custom/site/styles/',
        this.baseId
      ).then(result => {
        this.siteStyles = result;
      });
    }
  }
}
</script>
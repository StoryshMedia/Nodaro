<template>
  <section>
    <select
      class="form-select text-white-dark"
      @change="selectItem($event)"
    >
      <option value="">
        {{ $t('PLEASE_CHOOSE') }}
      </option>
      <option
        v-for="(type, typeindex) in types"
        :key="typeindex"
        :selected="type.id === itemData.id"
        :value="getValue(type)"
      >
        {{ type.name }}
      </option>
    </select>

    <div
      v-if="fieldConfig.saveCall"
      class="flex justify-end mb-8 mt-3"
    >
      <button
        type="button"
        class="btn btn-success"
        @click="save()"
      >
        {{ $t('SAVE') }}
      </button>
    </div>

    <faq-page
      v-if="itemData.id === 1"
      :main-entity="itemData.context.mainEntity"
      @update-value="emitData($event, 'updateValue')"
    />
  </section>
</template>

<script>
import ApiService from '@SmugAdministrationServices/api/api.service';
import { defineAsyncComponent } from "vue";

const FaqPage = defineAsyncComponent(() =>
  import("./additional/seo/FaqPage.vue" /* webpackChunkName: "faq-page" */)
);

export default {
  name: "Seo",
  components: {
    FaqPage
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
      default: 'PLEASE_CHOOSE'
    }
  },
  data() {
    return {
      types: [
        {
          name: 'FaqPage',
          id: 1,
          context: {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": []
          }
        }
      ],
      itemData: {},
      itemDataBackup: {}
    };
  },
  mounted() {
    if (this.fieldConfig.getCall) {
      this.getData();
    } else {
      this.itemData = this.fieldValue;
    }
  },
  methods: {
    setContent(content) {
      this.$emit('updateValue', content.target.value);
    },
    emitData(value, eventName) {
      this.itemData.context.mainEntity = value;
      this.$emit(eventName, this.itemData);
    },
    getValue(value) {
      return JSON.stringify(value);
    },
    selectItem(item) {
      if (item.target.value !== '') {
        const itemValue = JSON.parse(item.target.value);
        if (itemValue.id === this.itemDataBackup.id) {
          this.itemData = this.itemDataBackup;
        } else {
          this.itemDataBackup = { ...this.itemData };
          this.itemData = itemValue;
        }
      } else {
        this.itemDataBackup = { ...this.itemData }
        this.itemData = {};
      }
    },
    getData() {
      this.isLoading = true;
      
      ApiService.get(this.fieldConfig.getCall, this.fieldConfig.id)
        .then(result =>  {
          this.itemData = result;
        })
        .catch(error => {
          this.isLoading = false;
        })
        .then(function () {
        });
    },
    save() {
      this.isLoading = true;
      this.itemData.entry = {id: this.fieldConfig.id};
      
      ApiService.post(
        this.fieldConfig.saveCall,
        {
          type: this.itemData,
          entry: {
            id: this.fieldConfig.id
          }
        }
      )
        .then(response =>  {
          this.isLoading = false;
        })
        .catch(error => {
          this.isLoading = false;
        })
        .then(function () {
        });
    }
  }
}
</script>
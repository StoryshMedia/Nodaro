<template>
  <section :class="(fieldConfig.allDomains) ? 'h-auto' : 'relative min-h-10'">
    <div v-if="fieldConfig.allDomains">
      <div>
        <select
          v-if="domains.length > 0"
          class="form-select text-dark"
          :disabled="isDisabled()"
          @change="setDomain($event)"
        >
          <option
            style="opacity:.7"
            value=""
          >
            {{ $t('PLEASE_SELECT') }}
          </option>
          <option
            v-for="(domain, domainIndex) in domains"
            :key="domainIndex"
            :value="JSON.stringify(domain.value)"
          >
            <span>
              {{ $t(domain.title) }}
            </span>
          </option>
        </select>
      </div>
      <div
        v-if="selectedDomain.sites"
        class="relative min-h-10 mt-3"
      >
        <input
          type="text"
          class="form-input link-field-input absolute z-10"
          style="padding: .3rem 1rem;"
          :value="link"
          :disabled="isDisabled()"
          :class="fieldConfig.classes ?? ''"
          @change="setContent($event)"
        >
        <select
          class="form-select text-dark absolute z-0"
          @change="setSiteSlug($event)"
        >
          <option
            v-for="(itemValue, itemIndex) in selectedDomain.sites"
            :key="itemIndex"
            :value="itemValue.slug"
          >
            <span>
              {{ $t(itemValue.title) }}
            </span>
          </option>
        </select>
      </div>
    </div>
    <div
      v-else
      class="relative h-10"
    >
      <input
        type="text"
        class="form-input link-field-input absolute z-10"
        style="padding: .3rem 1rem;"
        :value="link"
        :disabled="isDisabled()"
        :class="fieldConfig.classes ?? ''"
        @change="setContent($event)"
      >
      <select
        class="form-select text-dark absolute z-0"
        @change="setSiteSlug($event)"
      >
        <option
          v-for="(itemValue, itemIndex) in items"
          :key="itemIndex"
          :value="itemValue.slug"
        >
          <span>
            {{ $t(itemValue['title']) }}
          </span>
        </option>
      </select>
    </div>
  </section>
</template>

<script>
import ApiService from '@SmugAdministration/js/services/api/api.service';

export default {
  name: "LinkField",
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
      link: '',
      items: [],
      domains: [],
      selectedDomain: {}
    }
  },
  mounted() {
    this.link = this.fieldValue;
    this.getData();
  },
  methods: {
    getValue(value) {
      return value;
    },
    setDomain(domain) {
      this.selectedDomain = JSON.parse(domain.target.value);
    },
    setContent(content) {
      this.$emit('updateValue', content.target.value);
    },
    setSiteSlug(event) {
      if (this.fieldConfig.allDomains) {
        this.link = this.selectedDomain.domain.url + event.target.value;
        this.$emit('updateValue', this.link);
      } else {
        this.link = event.target.value;
        this.$emit('updateValue', this.link);
      }
    },
    getData() {
      this.isLoading = true;
      if (this.fieldConfig.allDomains) {
        ApiService.get('/be/api/custom/site/domain/sites').then(result => {
          let items = [];
          let count = 0;

          for (count; count <= result.length - 1; count++) {
            items.push({value: result[count], title: result[count].domain.title});

            if (count === result.length - 1) {
              this.domains = items;
              console.log(this.domains);
              this.isLoading = false;
            }
          }
        });
      } else {
        const url = (this.fieldConfig.fromDomain) ? '/be/api/smug/frontend/domain/sites/' : '/be/api/custom/site/domain/sites/';
        ApiService.get(url, this.baseId).then(result => {
          let items = [];
          let count = 0;

          for (count; count <= result.length - 1; count++) {
            items.push({value: result[count], title: result[count].slug});

            if (count === result.length - 1) {
              this.items = items;
              this.isLoading = false;
            }
          }
        });
      }
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
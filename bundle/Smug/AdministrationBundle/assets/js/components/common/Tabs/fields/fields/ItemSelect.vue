<template>
  <div>
    <p
      class="mt-3 mb-1 pl-2 text-sm font-bold"
    >
      {{ $t('SECTION') }}
    </p>
    <select
      class="form-select text-dark"
      :model="getSectionValue()"
      :disabled="isDisabled()"
      @change="setSection($event)"
    >
      <option :selected="selectedSection === ''">
        <span>
          {{ $t('PLEASE_SELECT') }}
        </span>
      </option>
      <option
        v-for="(section, sectionIndex) in sections"
        :key="sectionIndex"
        :value="section.value"
        :selected="isSectionSelected(section.value)"
      >
        <span>
          {{ $t(section.title) }}
        </span>
      </option>
    </select>
    <p
      class="mt-3 mb-1 pl-2 text-sm font-bold"
    >
      {{ $t('ITEM') }}
    </p>
    <select
      v-if="items.length > 0"
      class="form-select text-dark"
      :model="getItemValue()"
      :disabled="isDisabled()"
      @change="setItem($event)"
    >
      <option :selected="selectedItem === ''">
        <span>
          {{ $t('PLEASE_SELECT') }}
        </span>
      </option>
      <option
        v-for="(item, itemIndex) in items"
        :key="itemIndex"
        :value="JSON.stringify(item)"
        :selected="isItemSelected(JSON.stringify(item))"
      >
        <span>
          {{ $t(item.title) }}
        </span>
      </option>
    </select>
  </div>
</template>

<script>
import ApiService from '@SmugAdministration/js/services/api/api.service';
import ValueService from '@SmugAdministration/js/services/value/value.service';

export default {
  name: "ItemSelect",
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
      sections: [],
      selectedSection: '',
      selectedItem: '',
      items: [],
      isLoading: false
    }
  },
  mounted() {
    try {
      const value = JSON.parse(this.fieldValue);
      this.selectedItem = value.item ?? '';
      this.selectedSection = value.section ?? '';

      if (this.selectedSection !== '') {
        this.getItems(this.selectedSection);
      }
    } catch (e) {
      this.selectedItem = '';
      this.selectedSection = '';
    }

    this.getData();
  },
  methods: {
    isSectionSelected(value) {
      return this.selectedSection === value;
    },
    getSectionValue() {
      return this.selectedSection;
    },
    getItemValue() {
      return this.selectedItem;
    },
    setItem(event) {
      this.selectedItem = JSON.parse(event.target.value).id;
      this.setContent();
    },
    setSection(event) {
      this.isLoading = true;
      this.selectedSection = event.target.value;
      this.getItems(event.target.value);
    },
    getItems(table) {
      ApiService.post('/be/api/custom/item/select/items', {table: table}, true).then(result => {
        this.isLoading = false;
        this.items = result;
      });
    },
    getFieldValue() {
      return ValueService.getValue(this.fieldValue, this.fieldConfig);
    },
    getValue(value) {
      return value;
    },
    setContent() {
      this.$emit('updateValue', JSON.stringify({section: this.selectedSection, item: this.selectedItem}));
    },
    isSelected(value) {
      if (typeof this.fieldValue === 'string') {
        return value.replace(/['"]+/g, '') === this.fieldValue;
      }

      return value === this.getValue(this.fieldValue);
    },
    isItemSelected(value) {
      return JSON.parse(value).id === this.selectedItem;
    },
    getData() {
      this.isLoading = true;
      ApiService.get('/be/api/custom/item/select/section', null, true).then(result => {
        this.isLoading = false;
        this.sections = result;
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
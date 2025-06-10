<template>
  <select
    :class="getGivenClass()" 
    :model="selectBoxValue"
    @change="setValue($event)"
  >
    <option
      style="opacity:.7"
      value=""
    >
      {{ $t('PLEASE_SELECT') }}
    </option>
    <option
      v-for="(itemValue, itemIndex) in items"
      :key="itemIndex"
      :value="getValue(itemValue.value)"
      :selected="isSelected(getValue(itemValue.value))"
    >
      <span v-if="getTranslateLabel() === true">
        {{ $t(itemValue[getLabelIdentifier()]) }}
      </span>
      <span v-else>
        {{ itemValue[getLabelIdentifier()] }}
      </span>
    </option>
  </select>
</template>

<script>

export default {
  name: "SelectBox",
  components: {
  },
  props: {
    items: {
      type: Array,
      required: true
    },
    optionLabelIdentifier: {
      type: String,
      required: false,
      default: 'label'
    },
    optionValueIdentifier: {
      type: String,
      required: false,
      default: 'slug'
    },
    givenclass: {
      type: String,
      required: false,
      default: 'w-full input rounded-3xl shadow-xl border-gray appearance-none select--arrow'
    },
    clearOnSelect: {
      type: Boolean,
      required: false,
      default: false
    },
    translateLabel: {
      type: Boolean,
      required: false,
      default: false
    },
    givenValue: {
      type: String,
      required: false,
      default: ''
    },
    givenObjectValue: {
      type: Object,
      required: false,
      default: null
    }
  },
  data() {
    return {
      selectBoxValue: ""
    };
  },
  async created() {
    if (this.clearOnSelect === false) {
      this.selectBoxValue = this.givenValue;
      if (this.givenObjectValue !== null) {
        this.selectBoxValue = this.givenObjectValue;
      }
    }
  },
  methods: {
    getLabelIdentifier() {
      return this.optionLabelIdentifier;
    },
    isSelected(value) {
      if (typeof this.selectBoxValue === 'string') {
        return value === this.selectBoxValue;
      }

      return value === this.getValue(this.selectBoxValue);
    },
    getValueIdentifier() {
      return this.optionValueIdentifier;
    },
    getTranslateLabel() {
      return this.translateLabel;
    },
    getGivenClass() {
      return this.givenclass;
    },
    getValue(value) {
      return JSON.stringify(value);
    },
    setValue(event) {
      if (event.target.value !== '') {
        this.$emit('optionSelected', JSON.parse(event.target.value));
        if (this.clearOnSelect === true) {
          this.selectBoxValue = '';
          event.target.value = '';
        }
      }
    }
  }
}
</script>

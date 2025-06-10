<template>
  <div>
    <div
      v-for="(option, optionIndex) in options"
      :key="optionIndex"
      class="py-1"
    >
      <label class="inline-flex cursor-pointer">
        <input
          type="checkbox"
          class="form-checkbox outline-none mt-1"
          :model="option.value"
          :checked="isChecked(option.value)"
          @change="setFilter(option.value)"
        >
        <span class="ml-2">{{ option.label }}</span>
      </label>
    </div>
  </div>
</template>
  
<script>
  
export default {
  name: "Multi",
  props: {
    options: {
      type: Object,
      required: false,
      default: () => ({})
    },
    activeFilters: {
      type: Object,
      required: false,
      default: () => ({})
    }
  },
  methods: {
    setFilter(filter) {
      this.$emit('setFilter', filter);
    },
    isChecked(value) {
      return this.activeFilters.includes(value);
    }
  }
}
</script>
  
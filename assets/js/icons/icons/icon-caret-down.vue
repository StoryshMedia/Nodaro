<template>
  <svg
    :class="getClass()"
    viewBox="0 0 24 24"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
  >
    <title v-if="title !== ''">{{ $t(title) }}</title>
    <path
      d="M19 9L12 15L5 9"
      stroke="currentColor"
      stroke-width="1.5"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
  </svg>
</template>
<script>
export default {
  name: "IconCaretDown",
  props: {
    class: {
      type: String,
      required: false,
      default: ''
    },
    title: {
      type: String,
      required: false,
      default: ''
    }
  },
  methods: {
    getClass() {
      return this.class;
    }
  }
}
</script>

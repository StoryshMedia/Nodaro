<template>
  <svg
    xmlns="http://www.w3.org/2000/svg"
    :class="(size !== '') ? size : 'h-5 w-5'"
    fill="none"
    viewBox="0 0 24 24"
    stroke="currentColor"
  >
    <title v-if="title !== ''">{{ $t(title) }}</title>
    <path
      stroke-linecap="round"
      stroke-linejoin="round"
      stroke-width="2"
      d="M4 8h16M4 16h16"
    />
  </svg>
</template>
<script>
export default {
  name: "IconMenu",
  props: {
    size: {
      type: String,
      required: false,
      default: ''
    },
    title: {
      type: String,
      required: false,
      default: ''
    }
  }
}
</script>
<template>
  <svg
    width="16"
    height="16"
    viewBox="0 0 24 24"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
  >
    <title v-if="title !== ''">{{ $t(title) }}</title>
    <circle
      cx="11.5"
      cy="11.5"
      r="9.5"
      stroke="currentColor"
      stroke-width="1.5"
      opacity="0.5"
    />
    <path
      d="M18.5 18.5L22 22"
      stroke="currentColor"
      stroke-width="1.5"
      stroke-linecap="round"
    />
  </svg>
</template>
<script>
export default {
  name: "IconSearch",
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

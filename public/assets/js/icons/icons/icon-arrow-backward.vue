<template>
  <svg
    width="20"
    height="20"
    viewBox="0 0 24 24"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
  >
    <title v-if="title !== ''">{{ $t(title) }}</title>
    <path
      d="M9.5 7L4.5 12L9.5 17"
      stroke="currentColor"
      stroke-width="1.5"
      stroke-linecap="round"
      stroke-linejoin="round"
    />
    <path
      opacity="0.5"
      d="M4.5 12L14.5 12C16.1667 12 19.5 13 19.5 17"
      stroke="currentColor"
      stroke-width="1.5"
      stroke-linecap="round"
    />
  </svg>
</template>
<script>
export default {
  name: "IconArrowBackward",
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

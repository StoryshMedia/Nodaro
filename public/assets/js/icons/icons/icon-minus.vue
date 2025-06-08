<template>
  <svg
    width="16"
    height="20"
    viewBox="0 0 24 24"
    stroke="currentColor"
    stroke-width="1.5"
    fill="none"
    stroke-linecap="round"
    stroke-linejoin="round"
  >
    <title v-if="title !== ''">{{ $t(title) }}</title>
    <line
      x1="5"
      y1="12"
      x2="19"
      y2="12"
    />
  </svg>
</template>
<script>
export default {
  name: "IconMinus",
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

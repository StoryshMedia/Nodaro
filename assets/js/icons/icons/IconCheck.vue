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
      d="M4.5 12.75l6 6 9-13.5"
    />
  </svg>
</template>
<script>
export default {
  name: "IconCheck",
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
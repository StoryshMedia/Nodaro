<template>
  <svg
    xmlns="http://www.w3.org/2000/svg"
    class="mx-auto"
    :class="(size !== '') ? size : 'h-5 w-5'"
    fill="none"
    viewBox="0 0 24 24"
    stroke="currentColor"
  >
    <title v-if="title !== ''">{{ $t(title) }}</title>
    <path
      stroke-linecap="round"
      stroke-linejoin="round"
      d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"
    />
  </svg>
</template>
<script>
export default {
  name: "IconDownload",
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
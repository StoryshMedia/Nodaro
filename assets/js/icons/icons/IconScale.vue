<template>
  <svg
    xmlns="http://www.w3.org/2000/svg"
    :class="(size !== '') ? size : 'h-5 w-5'"
    fill="none"
    stroke="currentColor"
    viewBox="0 0 24 24"
  >
    <title v-if="title !== ''">{{ $t(title) }}</title><path
      stroke-linecap="round"
      stroke-linejoin="round"
      stroke-width="2"
      d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"
    />
  </svg>
</template>
<script>
export default {
  name: "IconScale",
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
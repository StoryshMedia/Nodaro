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
      d="M7 16l-4-4m0 0l4-4m-4 4h18"
    />
  </svg>
</template>
<script>
export default {
  name: "IconPrev",
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
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
      d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"
    />
  </svg>
</template>
<script>
export default {
  name: "IconPaper",
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
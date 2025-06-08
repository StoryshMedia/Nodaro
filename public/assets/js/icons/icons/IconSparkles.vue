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
      d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"
    />
  </svg>
</template>
<script>
export default {
  name: "IconSparkles",
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
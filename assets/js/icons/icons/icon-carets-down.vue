<template>
  <svg
    width="20"
    height="20"
    viewBox="0 0 24 24"
    xmlns="http://www.w3.org/2000/svg"
  >
    <title v-if="title !== ''">{{ $t(title) }}</title>
    <path
      opacity="0.5"
      d="M5.00004 6.25C4.68618 6.25 4.40551 6.44543 4.29662 6.73979C4.18773 7.03415 4.27364 7.36519 4.51194 7.56944L11.5119 13.5694C11.7928 13.8102 12.2073 13.8102 12.4881 13.5694L19.4881 7.56944C19.7264 7.36519 19.8123 7.03415 19.7035 6.73979C19.5946 6.44543 19.3139 6.25 19 6.25H5.00004Z"
      fill="currentColor"
    />
    <path
      fill-rule="evenodd"
      clip-rule="evenodd"
      d="M4.43057 10.5119C4.70014 10.1974 5.17361 10.161 5.48811 10.4306L12 16.0122L18.5119 10.4306C18.8264 10.161 19.2999 10.1974 19.5695 10.5119C19.839 10.8264 19.8026 11.2999 19.4881 11.5695L12.4881 17.5695C12.2072 17.8102 11.7928 17.8102 11.5119 17.5695L4.51192 11.5695C4.19743 11.2999 4.161 10.8264 4.43057 10.5119Z"
    />
  </svg>
</template>
<script>
export default {
  name: "IconCaretsDown",
  props: {
    duotone: {
      type: Boolean,
      default: true,
    },
    fill: {
      type: Boolean,
      default: false,
    },
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

<template>
  <span
    class="badge mx-1 hover:text-white"
    :class="getClass()"
  >{{ $t(title) }}</span>
</template>

<script>
export default {
  name: "Badge",
  props: {
    title:{
      type: String,
      required: true
    },
    type:{
      type: String,
      required: true
    }
  },
  methods: {
    getClass() {
      return 'badge-outline-' + this.type;
    },
  }
}
</script>
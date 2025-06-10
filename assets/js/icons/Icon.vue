<template>
  <component
    :is="comp"
    :class="getClass()"
  />
</template>

<script>
import Components from "./index.js"

export default {
  name: "Icon",
  props: {
    iconString:{
      type: String,
      required: true
    },
    class:{
      type: String,
      required: false,
      default: 'w-4 h-5 flex-none'
    }
  },
  computed: {
    comp () {
      return Components[this.iconString]
    }
  }, 
  methods: {
    getClass() {
      return this.class;
    }
  }
}
</script>
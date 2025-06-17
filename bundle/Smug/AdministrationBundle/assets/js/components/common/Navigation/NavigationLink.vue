<template>
  <a :href="getLink()">
    {{ $t(item.label) }}
  </a>
</template>
  
  
<script>

export default {
  name: "NavigationLink",
  props: {
    item: {
      type: Object,
      required: true
    }
  },
  methods: {
    getLink() {
      return process.env.frontendURL + '/admin/' + this.item.path
    }
  }
}
</script>
<template>
  <img
    class="w-20 h-20 rounded-full overflow-hidden object-cover"
    :src="getSrc()"
    alt=""
  >
</template>

<script>
export default {
  name: "Avatar",
  components: {
  },
  props: {
    editAllowed:{
      type: Boolean,
      required: true
    },
    baseId:{
      type: String,
      required: false,
      default: null
    },
    fieldValue:{
      type: Object,
      required: false,
      default: null
    },
    fieldConfig:{
      type: Object,
      required: false,
      default: () => ({})
    },
    fieldPlaceholder:{
      type: String,
      required: false,
      default: 'TEXT_PLACEHOLDER'
    }
  },
  data() {
    return {
      reload: 0
    }
  },
  methods: {
    getSrc() {
      return process.env.frontendURL + '/' + this.fieldValue.path + this.fieldValue.file + '.' + this.fieldValue.extension;
    }
  }
}
</script>
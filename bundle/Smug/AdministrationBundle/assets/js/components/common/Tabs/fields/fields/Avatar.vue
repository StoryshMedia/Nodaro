<template>
  <img
    class="w-20 h-20 rounded-full overflow-hidden object-cover"
    :src="getSrc()"
    alt=""
  >
</template>

<script>
import ImageService from '@SmugAdministration/js/services/image/image.service';

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
      return ImageService.getImagePath(this.fieldValue);
    }
  }
}
</script>
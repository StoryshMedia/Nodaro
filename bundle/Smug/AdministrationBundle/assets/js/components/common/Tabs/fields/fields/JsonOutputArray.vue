<template>
  <div>
    <div
      v-for="(subData, subDataindex) in fieldValue"
      :key="subDataindex"
    >
      <div class="flex-1 my-4">
        <h4 class="font-semibold text-lg mb-2 text-primary">
          {{ $t(subDataindex) }}
        </h4>
        <p
          class="media-text"
          v-html="getOutput(subData)"
        />
      </div>
    </div>
  </div>
</template>

<script>
import TextService from '@SmugAdministration/js/services/text/text.service';

export default {
  name: "JsonOutputArray",
  props: {
    editAllowed:{
      type: Boolean,
      required: true
    },
    fieldValue:{
      type: Object,
      required: false,
      default: null
    },
    baseId:{
      type: String,
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
  methods: {
    getOutput(value) {
      return TextService.getOutput(value, 999999999);
    }
  }
}
</script>
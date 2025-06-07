<template>
  <section>
    <div class="w-full pb-5">
      <multiselect
        :options="options"
        :multiple="false"
        group-values="classes"
        group-label="name"
        :group-select="false"
        :select-label="''"
        track-by="name"
        class="custom-multiselect w-full relative"
        label="name"
        @select="setItem($event)"
      />
    </div>
  </section>
</template>

<script>
import Multiselect from 'vue-multiselect';
import classData from '../../../../../../../../../config/administration/tailwindClasses.json'

export default {
  name: "ClassSelect",
  components: {
    Multiselect
  },
  data() {
    return {
      options: classData,
    };
  },
  methods: {
    setItem(item) {
      this.$emit('updateValue', item.name);
    }
  }
}
</script>
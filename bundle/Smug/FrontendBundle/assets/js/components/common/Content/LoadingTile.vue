<template>
  <section>
    <div
      class="shadow"
      :class="getWrapperClass()"
    >
      <div class="animate-pulse flex">
        <div
          class="bg-loading w-full rounded-md"
          :class="getClass()"
        />
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "LoadingTile",
  props: {
    class: {
      type: String,
      required: false,
      default: 'h-100 w-full'
    },
    wrapperclass: {
      type: String,
      required: false,
      default: 'w-full md:w-50vw md:max-w-100 mx-auto'
    }
  },
  methods: {
    getClass() {
      return this.class;
    },
    getWrapperClass() {
      return this.wrapperclass;
    }
  }
};
</script>
